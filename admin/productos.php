<?php
require_once 'config.php';
requireLogin();

$conn = getConnection();

// Obtener categorías para el select
$categorias = $conn->query("SELECT * FROM categorias ORDER BY nombre");

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
    
    <!-- jQuery (requerido por DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    
    <style>
        /* Estilos personalizados para DataTables */
        .dataTables_wrapper {
            padding: 20px;
        }
        .dataTables_filter input {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            margin-left: 0.5rem;
        }
        .dataTables_length select {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0.5rem;
            margin: 0 0.5rem;
        }
        table.dataTable thead th {
            background: #1f2937;
            color: white;
            font-weight: 600;
            padding: 12px;
        }
        table.dataTable tbody tr:hover {
            background-color: #f9fafb;
        }
        .badge-active {
            background: #dcfce7;
            color: #166534;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .stock-low {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Productos</h1>
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
                <li><a href="productos.php" class="hover:text-blue-400 font-semibold">Productos</a></li>
                <li><a href="categorias.php" class="hover:text-blue-400">Categorías</a></li>
                <li><a href="configuracion.php" class="hover:text-blue-400">Configuración</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Botón Agregar Producto -->
        <div class="mb-6 flex justify-between items-center">
            <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                ➕ Agregar Nuevo Producto
            </button>
            <div class="text-sm text-gray-600">
                💡 Tip: Usa el buscador para encontrar productos rápidamente
            </div>
        </div>

        <!-- Tabla de Productos con DataTables -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table id="productosTable" class="display responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán vía AJAX -->
                </tbody>
            </table>
        </div>
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
        let productTable;

        $(document).ready(function() {
            // Inicializar DataTable
            productTable = $('#productosTable').DataTable({
                ajax: {
                    url: '../api/get_productos_admin.php',
                    dataSrc: 'productos'
                },
                columns: [
                    { data: 'id' },
                    { data: 'nombre' },
                    { data: 'categoria_nombre' },
                    { 
                        data: 'precio',
                        render: function(data, type, row) {
                            return 'S/ ' + parseFloat(data).toFixed(2);
                        }
                    },
                    { 
                        data: 'stock',
                        render: function(data, type, row) {
                            if (data < 5) {
                                return '<span class="stock-low">' + data + '</span>';
                            }
                            return data;
                        }
                    },
                    { 
                        data: 'activo',
                        render: function(data, type, row) {
                            if (data == 1) {
                                return '<span class="badge-active">Activo</span>';
                            } else {
                                return '<span class="badge-inactive">Inactivo</span>';
                            }
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <button onclick='editProduct(${JSON.stringify(row)})' class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded mr-2" title="Editar">
                                    ✏️
                                </button>
                                <button onclick="viewProduct(${row.id})" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mr-2" title="Ver">
                                    👁️
                                </button>
                                <button onclick="deleteProduct(${row.id})" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded" title="Eliminar">
                                    🗑️
                                </button>
                            `;
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                },
                responsive: true,
                pageLength: 25,
                order: [[0, 'desc']],
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });
        });

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

        function editProduct(product) {
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

        function viewProduct(productId) {
            window.open(`../producto.php?id=${productId}`, '_blank');
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
                    alert('✅ ' + data.message);
                    closeModal();
                    productTable.ajax.reload();
                } else {
                    alert('❌ Error: ' + data.message);
                }
            } catch (error) {
                alert('❌ Error al guardar el producto');
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
                    alert('✅ ' + data.message);
                    productTable.ajax.reload();
                } else {
                    alert('❌ Error: ' + data.message);
                }
            } catch (error) {
                alert('❌ Error al eliminar el producto');
                console.error(error);
            }
        }
    </script>
</body>
</html>