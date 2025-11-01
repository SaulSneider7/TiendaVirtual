<?php
// Ocultar warnings y notices que rompen el JSON
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

require_once '../admin/config.php';

// Verificar autenticación
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

header('Content-Type: application/json');

$conn = getConnection();

try {
    // Validar datos recibidos
    if (!isset($_POST['nombre']) || empty($_POST['nombre'])) {
        echo json_encode(['success' => false, 'message' => 'El nombre es requerido']);
        exit();
    }

    $productId = isset($_POST['productId']) && !empty($_POST['productId']) ? intval($_POST['productId']) : null;
    $nombre = sanitize($_POST['nombre']);
    $descripcion = sanitize($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $categoria_id = intval($_POST['categoria_id']);
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Validaciones básicas
    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre no puede estar vacío']);
        exit();
    }
    
    if ($precio <= 0) {
        echo json_encode(['success' => false, 'message' => 'El precio debe ser mayor a 0']);
        exit();
    }
    
    if ($categoria_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Selecciona una categoría válida']);
        exit();
    }
    
    // Manejar imagen
    $imagen_url = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $upload_result = uploadImage($_FILES['imagen']);
        if ($upload_result['success']) {
            $imagen_url = $upload_result['url'];
        } else {
            echo json_encode(['success' => false, 'message' => $upload_result['message']]);
            exit();
        }
    }
    
    if ($productId) {
        // Actualizar producto existente
        if ($imagen_url) {
            // Con nueva imagen
            $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, categoria_id=?, imagen=?, activo=? WHERE id=?");
            $stmt->bind_param("ssdiisii", $nombre, $descripcion, $precio, $stock, $categoria_id, $imagen_url, $activo, $productId);
        } else {
            // Sin cambiar imagen
            $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, categoria_id=?, activo=? WHERE id=?");
            $stmt->bind_param("ssdiiii", $nombre, $descripcion, $precio, $stock, $categoria_id, $activo, $productId);
        }
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Producto actualizado correctamente',
                'id' => $productId
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al actualizar: ' . $stmt->error
            ]);
        }
    } else {
        // Crear nuevo producto
        if (!$imagen_url) {
            echo json_encode(['success' => false, 'message' => 'Debes subir una imagen para el nuevo producto']);
            exit();
        }
        
        $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, imagen, activo) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdissi", $nombre, $descripcion, $precio, $stock, $categoria_id, $imagen_url, $activo);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Producto creado correctamente',
                'id' => $conn->insert_id
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Error al crear: ' . $stmt->error
            ]);
        }
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>