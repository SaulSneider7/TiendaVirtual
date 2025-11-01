<?php
// Test de configuración y permisos
header('Content-Type: application/json');

$tests = [];

// Test 1: Conexión a base de datos
try {
    require_once 'admin/config.php';
    $conn = getConnection();
    $tests['database'] = ['status' => 'OK', 'message' => 'Conexión exitosa'];
    $conn->close();
} catch (Exception $e) {
    $tests['database'] = ['status' => 'ERROR', 'message' => $e->getMessage()];
}

// Test 2: Carpeta uploads existe
if (file_exists('uploads/')) {
    $tests['uploads_folder'] = ['status' => 'OK', 'message' => 'Carpeta uploads existe'];
} else {
    $tests['uploads_folder'] = ['status' => 'ERROR', 'message' => 'Carpeta uploads NO existe'];
}

// Test 3: Permisos de escritura
if (is_writable('uploads/')) {
    $tests['uploads_writable'] = ['status' => 'OK', 'message' => 'Carpeta uploads tiene permisos de escritura'];
} else {
    $tests['uploads_writable'] = ['status' => 'ERROR', 'message' => 'Carpeta uploads NO tiene permisos de escritura'];
}

// Test 4: Configuración PHP
$tests['php_version'] = ['status' => 'INFO', 'message' => 'PHP ' . PHP_VERSION];
$tests['upload_max_filesize'] = ['status' => 'INFO', 'message' => ini_get('upload_max_filesize')];
$tests['post_max_size'] = ['status' => 'INFO', 'message' => ini_get('post_max_size')];

// Test 5: Extensiones necesarias
$tests['mysqli'] = extension_loaded('mysqli') ? 
    ['status' => 'OK', 'message' => 'MySQLi habilitado'] : 
    ['status' => 'ERROR', 'message' => 'MySQLi NO habilitado'];

$tests['gd'] = extension_loaded('gd') ? 
    ['status' => 'OK', 'message' => 'GD (imágenes) habilitado'] : 
    ['status' => 'ERROR', 'message' => 'GD (imágenes) NO habilitado'];

echo json_encode($tests, JSON_PRETTY_PRINT);
?>