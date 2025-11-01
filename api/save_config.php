<?php
header('Content-Type: application/json');
require_once '../admin/config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "No se recibieron datos."]);
    exit;
}

$nombre_tienda = sanitize($data['nombre_tienda']);
$whatsapp = sanitize($data['whatsapp']);
$moneda = sanitize($data['moneda']);

$conn = getConnection();
$stmt = $conn->prepare("UPDATE configuracion SET nombre_tienda = ?, whatsapp = ?, moneda = ?, updated_at = NOW() WHERE id = 1");
$stmt->bind_param("sss", $nombre_tienda, $whatsapp, $moneda);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
