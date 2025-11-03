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
    $input = json_decode(file_get_contents('php://input'), true);
    $categoryId = intval($input['id']);
    
    if ($categoryId > 0) {
        // Verificar si tiene productos asociados
        $check = $conn->query("SELECT COUNT(*) as count FROM productos WHERE categoria_id = $categoryId");
        $result = $check->fetch_assoc();
        
        if ($result['count'] > 0) {
            // Actualizar productos para que no tengan categoría (NULL)
            $conn->query("UPDATE productos SET categoria_id = NULL WHERE categoria_id = $categoryId");
        }
        
        $stmt = $conn->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->bind_param("i", $categoryId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Categoría eliminada correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la categoría']);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'ID de categoría inválido']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>