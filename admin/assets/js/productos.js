let productTable;

$(document).ready(function () {
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
                render: function (data, type, row) {
                    return 'S/ ' + parseFloat(data).toFixed(2);
                }
            },
            {
                data: 'stock',
                render: function (data, type, row) {
                    if (data < 5) {
                        return '<span class="stock-low">' + data + '</span>';
                    }
                    return data;
                }
            },
            {
                data: 'activo',
                render: function (data, type, row) {
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
                render: function (data, type, row) {
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

document.getElementById('productForm').addEventListener('submit', async function (e) {
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
