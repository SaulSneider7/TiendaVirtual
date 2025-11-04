<?php
require_once '../admin/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = getConnection();

try {
    $producto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($producto_id <= 0) {
        echo json_encode(['error' => 'ID de producto inválido']);
        exit();
    }
    
    // Obtener configuración del negocio
    $config_result = $conn->query("SELECT * FROM configuracion LIMIT 1");
    $config = $config_result->fetch_assoc();
    
    // Obtener el producto específico
    $stmt = $conn->prepare("SELECT p.*, c.nombre as categoria, c.id as categoria_id
                           FROM productos p 
                           LEFT JOIN categorias c ON p.categoria_id = c.id 
                           WHERE p.id = ? AND p.activo = 1");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['error' => 'Producto no encontrado']);
        exit();
    }
    
    $producto = $result->fetch_assoc();
    
    // Obtener productos relacionados (misma categoría)
    $relacionados = [];
    if ($producto['categoria_id']) {
        $stmt_relacionados = $conn->prepare("SELECT p.*, c.nombre as categoria
                                             FROM productos p 
                                             LEFT JOIN categorias c ON p.categoria_id = c.id 
                                             WHERE p.categoria_id = ? AND p.id != ? AND p.activo = 1 
                                             LIMIT 4");
        $stmt_relacionados->bind_param("ii", $producto['categoria_id'], $producto_id);
        $stmt_relacionados->execute();
        $result_relacionados = $stmt_relacionados->get_result();
        
        while ($row = $result_relacionados->fetch_assoc()) {
            $relacionados[] = [
                'id' => (int)$row['id'],
                'nombre' => $row['nombre'],
                'descripcion' => $row['descripcion'],
                'precio' => (float)$row['precio'],
                'imagen' => $row['imagen'],
                'categoria' => $row['categoria'],
                'stock' => (int)$row['stock']
            ];
        }
        $stmt_relacionados->close();
    }
    
    // Obtener producto anterior y siguiente
    $anterior = null;
    $siguiente = null;
    
    // Producto anterior
    $stmt_ant = $conn->prepare("SELECT id, nombre FROM productos WHERE id < ? AND activo = 1 ORDER BY id DESC LIMIT 1");
    $stmt_ant->bind_param("i", $producto_id);
    $stmt_ant->execute();
    $result_ant = $stmt_ant->get_result();
    if ($result_ant->num_rows > 0) {
        $anterior = $result_ant->fetch_assoc();
    }
    $stmt_ant->close();
    
    // Producto siguiente
    $stmt_sig = $conn->prepare("SELECT id, nombre FROM productos WHERE id > ? AND activo = 1 ORDER BY id ASC LIMIT 1");
    $stmt_sig->bind_param("i", $producto_id);
    $stmt_sig->execute();
    $result_sig = $stmt_sig->get_result();
    if ($result_sig->num_rows > 0) {
        $siguiente = $result_sig->fetch_assoc();
    }
    $stmt_sig->close();
    
    $response = [
        'negocio' => [
            'nombre' => $config['nombre_tienda'],
            'whatsapp' => $config['whatsapp'],
            'moneda' => $config['moneda'],
            'facebook' => $config['facebook'] ?? '',
            'instagram' => $config['instagram'] ?? '',
            'twitter' => $config['twitter'] ?? '',
            'tiktok' => $config['tiktok'] ?? '',
            'email' => $config['email'] ?? '',
            'direccion' => $config['direccion'] ?? ''
        ],
        'producto' => [
            'id' => (int)$producto['id'],
            'nombre' => $producto['nombre'],
            'descripcion' => $producto['descripcion'],
            'precio' => (float)$producto['precio'],
            'imagen' => $producto['imagen'],
            'categoria' => $producto['categoria'],
            'categoria_id' => (int)$producto['categoria_id'],
            'stock' => (int)$producto['stock']
        ],
        'relacionados' => $relacionados,
        'navegacion' => [
            'anterior' => $anterior,
            'siguiente' => $siguiente
        ]
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
    $stmt->close();
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al obtener producto: ' . $e->getMessage()]);
}

$conn->close();
?>