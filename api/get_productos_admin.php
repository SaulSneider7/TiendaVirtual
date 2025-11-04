<?php
require_once '../admin/config.php';
requireLogin();

header('Content-Type: application/json');

$conn = getConnection();

try {
    // Obtener todos los productos (activos e inactivos) para el admin
    $productos_result = $conn->query("SELECT p.id, p.nombre, p.descripcion, p.precio, p.stock, 
                                      p.imagen, p.activo, p.categoria_id, c.nombre as categoria_nombre 
                                      FROM productos p 
                                      LEFT JOIN categorias c ON p.categoria_id = c.id 
                                      ORDER BY p.id DESC");
    
    $productos = [];
    while ($row = $productos_result->fetch_assoc()) {
        $productos[] = [
            'id' => (int)$row['id'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'],
            'precio' => (float)$row['precio'],
            'stock' => (int)$row['stock'],
            'imagen' => $row['imagen'], // Solo la ruta, no la imagen en base64
            'activo' => (int)$row['activo'],
            'categoria_id' => (int)$row['categoria_id'],
            'categoria_nombre' => $row['categoria_nombre'] ?? 'Sin categoría'
        ];
    }
    
    $response = [
        'productos' => $productos
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al obtener productos: ' . $e->getMessage()]);
}

$conn->close();
?>