<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();
$config = $conn->query("SELECT * FROM configuracion LIMIT 1")->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de la Tienda</title>
    <link rel="stylesheet" href="../assets/output.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Configuración de la Tienda</h1>
            <div class="flex items-center gap-4">
                <span class="text-gray-600">Hola, <?php echo $_SESSION['admin_username']; ?></span>
                <a href="../index.php" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    Ver Tienda
                </a>
                <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                    Cerrar Sesión
                </a>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-gray-800 text-white">
        <div class="container mx-auto px-4">
            <ul class="flex space-x-4 py-4">
                <li><a href="dashboard.php" class="hover:text-blue-400">Dashboard</a></li>
                <li><a href="productos.php" class="hover:text-blue-400">Productos</a></li>
                <li><a href="categorias.php" class="hover:text-blue-400">Categorías</a></li>
                <li><a href="configuracion.php" class="hover:text-blue-400 font-semibold">Configuración</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-8 max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold mb-6">⚙️ Configuración General</h2>
            
            <form id="configForm">
                <input type="hidden" name="config_id" value="<?php echo $config['id']; ?>">
                
                <!-- Información Básica -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-4 text-blue-600">📋 Información Básica</h3>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Nombre de la Tienda *</label>
                        <input type="text" name="nombre_tienda" value="<?php echo htmlspecialchars($config['nombre_tienda']); ?>" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">WhatsApp *</label>
                            <input type="text" name="whatsapp" value="<?php echo htmlspecialchars($config['whatsapp']); ?>" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500" placeholder="51987654321">
                            <p class="text-xs text-gray-500 mt-1">Código país + número sin espacios</p>
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Moneda *</label>
                            <input type="text" name="moneda" value="<?php echo htmlspecialchars($config['moneda']); ?>" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500" placeholder="S/">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($config['email'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500" placeholder="contacto@tienda.com">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Dirección</label>
                        <textarea name="direccion" rows="2" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500" placeholder="Calle Principal 123, Ciudad, País"><?php echo htmlspecialchars($config['direccion'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <!-- Redes Sociales -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-4 text-purple-600">🌐 Redes Sociales</h3>
                    <p class="text-sm text-gray-600 mb-4">Deja en blanco las que no uses. Usa la URL completa (https://...)</p>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <span class="text-blue-600">📘</span> Facebook
                        </label>
                        <input type="url" name="facebook" value="<?php echo htmlspecialchars($config['facebook'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500" placeholder="https://facebook.com/tutienda">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <span class="text-pink-500">📷</span> Instagram
                        </label>
                        <input type="url" name="instagram" value="<?php echo htmlspecialchars($config['instagram'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500" placeholder="https://instagram.com/tutienda">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <span class="text-blue-400">🐦</span> Twitter / X
                        </label>
                        <input type="url" name="twitter" value="<?php echo htmlspecialchars($config['twitter'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500" placeholder="https://twitter.com/tutienda">
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">
                            <span>🎵</span> TikTok
                        </label>
                        <input type="url" name="tiktok" value="<?php echo htmlspecialchars($config['tiktok'] ?? ''); ?>" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500" placeholder="https://tiktok.com/@tutienda">
                    </div>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition">
                        💾 Guardar Configuración
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script src="./assets/js/configuracion.js"></script>
</body>
</html>