<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();

// Obtener categorías para el select
$categorias = $conn->query("SELECT * FROM categorias ORDER BY nombre");

// Obtener productos
$productos = $conn->query("SELECT p.*, c.nombre as categoria_nombre FROM productos p 
                           LEFT JOIN categorias c ON p.categoria_id = c.id 
                           ORDER BY p.created_at DESC");

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            
            <!-- Título -->
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 text-center sm:text-left">
                Gestión de Productos
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
                    🚪 <span>Cerrar</span>
                </a>
            </div>
        </div>
    </header>


    <!-- Navigation -->
    <nav class="bg-gray-800 text-white">
        <div class="container mx-auto px-4">
            <ul class="flex space-x-4 py-4">
                <li><a href="dashboard.php" class="hover:text-blue-400">Dashboard</a></li>
                <li><a href="productos.php" class="hover:text-blue-400 font-semibold">Productos</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
    <!-- Botón Agregar Producto -->
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">Listado de Productos</h2>
        <button onclick="openModal()" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2 transition">
            ➕ <span>Agregar Producto</span>
        </button>
    </div>

    <!-- Tabla de Productos (scroll horizontal en móvil) -->
    <div class="bg-white rounded-lg shadow-md overflow-x-auto">
        <table class="w-full min-w-[700px]">
            <thead class="bg-gray-800 text-white text-sm">
                <tr>
                    <th class="px-4 py-3 text-left">ID</th>
                    <th class="px-4 py-3 text-left">Imagen</th>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Categoría</th>
                    <th class="px-4 py-3 text-left">Precio</th>
                    <th class="px-4 py-3 text-left">Stock</th>
                    <th class="px-4 py-3 text-left">Estado</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="productosTable" class="text-gray-700 text-sm">
                <?php while($producto = $productos->fetch_assoc()): ?>
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="px-4 py-3"><?php echo $producto['id']; ?></td>
                    <td class="px-4 py-3">
                        <img src="../<?php echo $producto['imagen']; ?>" 
                             alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                             class="w-14 h-14 object-cover rounded-lg shadow-sm">
                    </td>
                    <td class="px-4 py-3 font-medium"><?php echo htmlspecialchars($producto['nombre']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></td>
                    <td class="px-4 py-3 font-semibold">S/ <?php echo number_format($producto['precio'], 2); ?></td>
                    <td class="px-4 py-3">
                        <span class="<?php echo $producto['stock'] < 5 ? 'text-red-600 font-bold' : ''; ?>">
                            <?php echo $producto['stock']; ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                            <?php echo $producto['activo'] 
                                ? 'bg-green-100 text-green-700' 
                                : 'bg-red-100 text-red-700'; ?>">
                            <?php echo $producto['activo'] ? 'Activo' : 'Inactivo'; ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center flex justify-center gap-2">
                        <button 
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded text-sm btn-edit transition" 
                            data-product='<?php echo htmlspecialchars(json_encode($producto), ENT_QUOTES, 'UTF-8'); ?>'>
                            ✏️ Editar
                        </button>

                        <button onclick="deleteProduct(<?php echo $producto['id']; ?>)" 
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded text-sm transition">
                            🗑️ Eliminar
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Nota para pantallas pequeñas -->
    <p class="text-gray-500 text-sm mt-3 text-center sm:hidden">
        🔄 Desliza horizontalmente para ver todos los campos →
    </p>
</main>


    <!-- Modal para Agregar/Editar Producto -->
    <div id="productModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-blue-600 text-white p-6">
                <h2 class="text-2xl font-bold" id="modalTitle">Agregar Producto</h2>
            </div>
            
            <form id="productForm" class="p-6" enctype="multipart/form-data">
                <input type="hidden" id="productId" name="productId">
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Nombre del Producto *</label>
                    <input type="text" id="nombre" name="nombre" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Descripción *</label>
                    <textarea id="descripcion" name="descripcion" required rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500"></textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Precio *</label>
                        <input type="number" id="precio" name="precio" step="0.01" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Stock *</label>
                        <input type="number" id="stock" name="stock" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Categoría *</label>
                    <select id="categoria_id" name="categoria_id" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                        <option value="">Seleccione una categoría</option>
                        <?php 
                        $categorias->data_seek(0);
                        while($cat = $categorias->fetch_assoc()): 
                        ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['nombre']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-2">Imagen del Producto</label>
                    <input type="file" id="imagen" name="imagen" accept="image/*" class="w-full px-4 py-2 border rounded-lg">
                    <p class="text-sm text-gray-600 mt-1">Deja en blanco si no quieres cambiar la imagen</p>
                    <div id="currentImagePreview" class="mt-2"></div>
                </div>
                
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="activo" name="activo" checked class="mr-2">
                        <span class="text-gray-700 font-semibold">Producto Activo</span>
                    </label>
                </div>
                
                <div class="flex gap-4">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition">
                        Guardar Producto
                    </button>
                    <button type="button" onclick="closeModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 rounded-lg transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('productModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Agregar Producto';
            document.getElementById('productForm').reset();
            document.getElementById('productId').value = '';
            document.getElementById('currentImagePreview').innerHTML = '';
        }

        function closeModal() {
            document.getElementById('productModal').classList.add('hidden');
        }

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const product = JSON.parse(btn.getAttribute('data-product'));
                openEditModal(product);
            });
        });

        function openEditModal(product) {
            document.getElementById('productModal').classList.remove('hidden');
            document.getElementById('modalTitle').textContent = 'Editar Producto';
            
            document.getElementById('productId').value = product.id;
            document.getElementById('nombre').value = product.nombre;
            document.getElementById('descripcion').value = product.descripcion;
            document.getElementById('precio').value = product.precio;
            document.getElementById('stock').value = product.stock;
            document.getElementById('categoria_id').value = product.categoria_id;
            document.getElementById('activo').checked = product.activo == 1;
            
            document.getElementById('currentImagePreview').innerHTML = `
                <img src="../${product.imagen}" alt="${product.nombre}" class="w-32 h-32 object-cover rounded">
                <p class="text-sm text-gray-600 mt-1">Imagen actual</p>
            `;
        }

        document.getElementById('productForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('../api/save_producto.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Error al guardar el producto');
                console.error(error);
            }
        });

        async function deleteProduct(id) {
            if (!confirm('¿Estás seguro de eliminar este producto?')) {
                return;
            }
            
            try {
                const response = await fetch('../api/delete_producto.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Error al eliminar el producto');
                console.error(error);
            }
        }
    </script>
</body>
</html>