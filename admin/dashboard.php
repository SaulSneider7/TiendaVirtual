<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();

// Obtener estadísticas
$stats = [];
$stats['total_productos'] = $conn->query("SELECT COUNT(*) as count FROM productos")->fetch_assoc()['count'];
$stats['productos_activos'] = $conn->query("SELECT COUNT(*) as count FROM productos WHERE activo = 1")->fetch_assoc()['count'];
$stats['categorias'] = $conn->query("SELECT COUNT(*) as count FROM categorias")->fetch_assoc()['count'];
$stats['stock_bajo'] = $conn->query("SELECT COUNT(*) as count FROM productos WHERE stock < 5")->fetch_assoc()['count'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Panel Administrativo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">

            <!-- Título -->
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 text-center sm:text-left">
                Panel Administrativo
            </h1>

            <!-- Controles -->
            <div class="flex flex-wrap justify-center sm:justify-end items-center gap-2 sm:gap-4">
                <span class="text-gray-700 text-sm sm:text-base">
                    👋 Hola, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
                </span>

                <a href="../index.php" target="_blank"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 sm:px-4 py-2 rounded-lg transition text-sm flex items-center gap-1 sm:gap-2">
                    🛍️ <span>Ver Tienda</span>
                </a>

                <a href="logout.php"
                    class="bg-red-600 hover:bg-red-700 text-white px-3 sm:px-4 py-2 rounded-lg transition text-sm flex items-center gap-1 sm:gap-2">
                    🚪 <span>Cerrar Sesión</span>
                </a>
            </div>
        </div>
    </header>


    <!-- Navigation -->
    <nav class="bg-gray-800 text-white">
        <div class="container mx-auto px-4">
            <ul class="flex space-x-4 py-4">
                <li><a href="dashboard.php" class="hover:text-blue-400 font-semibold">Dashboard</a></li>
                <li><a href="productos.php" class="hover:text-blue-400">Productos</a></li>
                <li><a href="categorias.php" class="hover:text-blue-400">Categorías</a></li>
                <li><a href="configuracion.php" class="hover:text-blue-400">Configuración</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Productos</p>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $stats['total_productos']; ?></p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-4">
                        <span class="text-3xl">📦</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Productos Activos</p>
                        <p class="text-3xl font-bold text-green-600"><?php echo $stats['productos_activos']; ?></p>
                    </div>
                    <div class="bg-green-100 rounded-full p-4">
                        <span class="text-3xl">✅</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Categorías</p>
                        <p class="text-3xl font-bold text-purple-600"><?php echo $stats['categorias']; ?></p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-4">
                        <span class="text-3xl">🏷️</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Stock Bajo</p>
                        <p class="text-3xl font-bold text-red-600"><?php echo $stats['stock_bajo']; ?></p>
                    </div>
                    <div class="bg-red-100 rounded-full p-4">
                        <span class="text-3xl">⚠️</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Acciones Rápidas</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="productos.php" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-6 text-center transition">
                    <span class="text-4xl mb-2 block">➕</span>
                    <p class="font-semibold">Agregar Producto</p>
                </a>
                <a href="productos.php" class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-6 text-center transition">
                    <span class="text-4xl mb-2 block">📋</span>
                    <p class="font-semibold">Ver Todos los Productos</p>
                </a>
                <a href="../index.php" target="_blank" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-6 text-center transition">
                    <span class="text-4xl mb-2 block">🌐</span>
                    <p class="font-semibold">Ver Tienda Online</p>
                </a>
            </div>
        </div>
    </main>
</body>
</html>