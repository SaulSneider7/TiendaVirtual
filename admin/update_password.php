<?php
$new_pass = password_hash("admin123", PASSWORD_DEFAULT);

$conexion = new mysqli("localhost", "root", "", "tienda_online");
$sql = "UPDATE admin_users SET password = ? WHERE username = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ss", $new_pass, $username);

$username = "admin";
$stmt->execute();

echo "Contraseña actualizada correctamente.";
?>
