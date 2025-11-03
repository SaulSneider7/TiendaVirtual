<?php
require_once '../admin/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = getConnection();

try {
    // Obtener configuración del negocio
    $config_result = $conn->query("SELECT * FROM configuracion LIMIT 1");
    $config = $config_result->fetch_assoc();
    
    // Obtener productos activos
    $productos_result = $conn->query("SELECT p.*, c.nombre as categoria 
                                      FROM productos p 
                                      LEFT JOIN categorias c ON p.categoria_id = c.id 
                                      WHERE p.activo = 1 
                                      ORDER BY p.created_at DESC");
    
    $productos = [];
    while ($row = $productos_result->fetch_assoc()) {
        $productos[] = [
            'id' => (int)$row['id'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'precio' => (float)$row['precio'],
            'imagen' => $row['imagen'],
            'categoria' => $row['categoria'],
            'stock' => (int)$row['stock']
        ];
    }
    
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
        'productos' => $productos
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al obtener productos: ' . $e->getMessage()]);
}

$conn->close();
?>