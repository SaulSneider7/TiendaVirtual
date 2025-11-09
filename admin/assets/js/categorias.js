
function openModal() {
    document.getElementById('categoryModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Agregar Categoría';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
}

function closeModal() {
    document.getElementById('categoryModal').classList.add('hidden');
}

function editCategory(category) {
    document.getElementById('categoryModal').classList.remove('hidden');
    document.getElementById('modalTitle').textContent = 'Editar Categoría';
    document.getElementById('categoryId').value = category.id;
    document.getElementById('nombre').value = category.nombre;
}

document.getElementById('categoryForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
        const response = await fetch('../api/save_categoria.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ Error: ' + data.message);
        }
    } catch (error) {
        alert('❌ Error al guardar la categoría');
        console.error(error);
    }
});

async function deleteCategory(id, productCount) {
    if (productCount > 0) {
        if (!confirm(`Esta categoría tiene ${productCount} productos. ¿Estás seguro de eliminarla? Los productos quedarán sin categoría.`)) {
            return;
        }
    } else {
        if (!confirm('¿Estás seguro de eliminar esta categoría?')) {
            return;
        }
    }

    try {
        const response = await fetch('../api/delete_categoria.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        });

        const data = await response.json();

        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ Error: ' + data.message);
        }
    } catch (error) {
        alert('❌ Error al eliminar la categoría');
        console.error(error);
    }
}
