<?php
require_once '../admin/config.php';

if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

header('Content-Type: application/json');

$conn = getConnection();

try {
    $categoryId = isset($_POST['categoryId']) && !empty($_POST['categoryId']) ? intval($_POST['categoryId']) : null;
    $nombre = isset($_POST['nombre']) ? sanitize($_POST['nombre']) : '';
    
    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
        exit();
    }
    
    if ($categoryId) {
        // Actualizar categoría existente
        $stmt = $conn->prepare("UPDATE categorias SET nombre=? WHERE id=?");
        $stmt->bind_param("si", $nombre, $categoryId);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Categoría actualizada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al actualizar la categoría'
            ]);
        }
        $stmt->close();
    } else {
        // Crear nueva categoría
        $stmt = $conn->prepare("INSERT INTO categorias (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Categoría creada correctamente'
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al crear la categoría'
            ]);
        }
        $stmt->close();
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>