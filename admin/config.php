<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost'); // Por lo general es localhost en HostGator
define('DB_USER', 'root'); // Cambia por tu usuario de MySQL
define('DB_PASS', ''); // Cambia por tu contraseña de MySQL
define('DB_NAME', 'tienda_online'); // Nombre de tu base de datos

// Ocultar errores en producción
error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', 0);

// Conexión a la base de datos
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado
function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Verificar autenticación para páginas protegidas
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

// Función para subir imágenes
function uploadImage($file) {
    $target_dir = "../uploads/";
    
    // Crear carpeta si no existe
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Verificar que sea una imagen real
    $check = getimagesize($file["tmp_name"]);
    if($check === false) {
        return ["success" => false, "message" => "El archivo no es una imagen válida."];
    }
    
    // Verificar tamaño del archivo (5MB máximo)
    if ($file["size"] > 5000000) {
        return ["success" => false, "message" => "El archivo es demasiado grande. Máximo 5MB."];
    }
    
    // Permitir solo ciertos formatos
    if($file_extension != "jpg" && $file_extension != "png" && $file_extension != "jpeg" && $file_extension != "gif" && $file_extension != "webp") {
        return ["success" => false, "message" => "Solo se permiten archivos JPG, JPEG, PNG, GIF y WEBP."];
    }
    
    // Intentar subir el archivo
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return ["success" => true, "url" => "uploads/" . $new_filename];
    } else {
        return ["success" => false, "message" => "Error al subir el archivo."];
    }
}

// Función para sanitizar inputs
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>