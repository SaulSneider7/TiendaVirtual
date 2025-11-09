<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();
$categorias = $conn->query("SELECT c.*, COUNT(p.id) as productos_count 
                            FROM categorias c 
                            LEFT JOIN productos p ON c.id = p.categoria_id 
                            GROUP BY c.id 
                            ORDER BY c.nombre");
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    <link rel="stylesheet" href="../assets/output.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Categorías</h1>
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
                <li><a href="categorias.php" class="hover:text-blue-400 font-semibold">Categorías</a></li>
                <li><a href="configuracion.php" class="hover:text-blue-400">Configuración</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Botón Agregar Categoría -->
        <div class="mb-6">
            <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                ➕ Agregar Nueva Categoría
            </button>
        </div>

        <!-- Grid de Categorías -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php while($categoria = $categorias->fetch_assoc()): ?>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($categoria['nombre']); ?></h3>
                    <span class="text-3xl">🏷️</span>
                </div>
                <p class="text-gray-600 mb-4">
                    <strong><?php echo $categoria['productos_count']; ?></strong> productos
                </p>
                <div class="flex gap-2">
                    <button onclick='editCategory(<?php echo json_encode($categoria); ?>)' class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded font-semibold transition">
                        ✏️ Editar
                    </button>
                    <button onclick="deleteCategory(<?php echo $categoria['id']; ?>, <?php echo $categoria['productos_count']; ?>)" class="flex-1 bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded font-semibold transition">
                        🗑️ Eliminar
                    </button>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <?php if ($categorias->num_rows === 0): ?>
        <div class="text-center py-12 bg-white rounded-lg shadow-md">
            <p class="text-gray-500 text-xl">No hay categorías creadas</p>
            <button onclick="openModal()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                Crear primera categoría
            </button>
        </div>
        <?php endif; ?>
    </main>

    <!-- Modal para Agregar/Editar Categoría -->
    <div id="categoryModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="bg-blue-600 text-white p-6">
                <h2 class="text-2xl font-bold" id="modalTitle">Agregar Categoría</h2>
            </div>
            
            <form id="categoryForm" class="p-6">
                <input type="hidden" id="categoryId" name="categoryId">
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Nombre de la Categoría *</label>
                    <input type="text" id="nombre" name="nombre" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500" placeholder="Ej: Electrónica, Ropa, Accesorios">
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition">
                        Guardar Categoría
                    </button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 rounded-lg transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

   <script src="./assets/js/categorias.js"></script>
</body>
</html>