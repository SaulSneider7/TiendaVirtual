<?php
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

require_once '../admin/config.php';

ob_end_clean();

if (!isLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

header('Content-Type: application/json');

$conn = getConnection();

try {
    $config_id = intval($_POST['config_id']);
    $nombre_tienda = sanitize($_POST['nombre_tienda']);
    $whatsapp = sanitize($_POST['whatsapp']);
    $moneda = sanitize($_POST['moneda']);
    $facebook = sanitize($_POST['facebook'] ?? '');
    $instagram = sanitize($_POST['instagram'] ?? '');
    $twitter = sanitize($_POST['twitter'] ?? '');
    $tiktok = sanitize($_POST['tiktok'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $direccion = sanitize($_POST['direccion'] ?? '');
    
    // Validaciones
    if (empty($nombre_tienda)) {
        echo json_encode(['success' => false, 'message' => 'El nombre de la tienda es requerido']);
        exit();
    }
    
    if (empty($whatsapp)) {
        echo json_encode(['success' => false, 'message' => 'El WhatsApp es requerido']);
        exit();
    }
    
    if (empty($moneda)) {
        echo json_encode(['success' => false, 'message' => 'La moneda es requerida']);
        exit();
    }
    
    $stmt = $conn->prepare("UPDATE configuracion SET 
                           nombre_tienda=?, whatsapp=?, moneda=?, 
                           facebook=?, instagram=?, twitter=?, tiktok=?, 
                           email=?, direccion=? 
                           WHERE id=?");
    $stmt->bind_param("sssssssssi", 
                      $nombre_tienda, $whatsapp, $moneda, 
                      $facebook, $instagram, $twitter, $tiktok, 
                      $email, $direccion, $config_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Configuración actualizada correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Error al actualizar la configuración'
        ]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
exit();
?>