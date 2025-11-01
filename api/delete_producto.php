<?php
require_once '../admin/config.php';
requireLogin();

header('Content-Type: application/json');

$conn = getConnection();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = intval($input['id']);
    
    if ($productId > 0) {
        $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $productId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar el producto']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'ID de producto inválido']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>