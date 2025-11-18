/* ========================
  Configuración SweetAlert2
  ======================== */
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 1800,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
});

/* ========================
   Variables y estado
   ======================== */
let storeData = null;
let cart = [];
let currentFilter = 'all';
let allProducts = [];
let currentPage = 1;
const productsPerPage = 12;

/* ========================
   LocalStorage - carrito
   ======================== */
function loadCartFromStorage() {
    try {
        const savedCart = localStorage.getItem('shopping_cart');
        if (savedCart) {
            cart = JSON.parse(savedCart);
            updateCart();
        }
    } catch (e) {
        console.error('Error cargando carrito', e);
    }
}

function saveCartToStorage() {
    try {
        localStorage.setItem('shopping_cart', JSON.stringify(cart));
    } catch (e) {
        console.error('Error guardando carrito', e);
    }
}

/* ========================
   Vaciar carrito (con confirm)
   ======================== */
function clearCart() {
    if (cart.length === 0) {
        Toast.fire({
            icon: 'info',
            title: 'El carrito ya está vacío'
        });
        return;
    }
    Swal.fire({
        title: '¿Vaciar carrito?',
        text: 'Se eliminarán todos los productos del carrito.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, vaciar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444'
    }).then(result => {
        if (result.isConfirmed) {
            cart = [];
            saveCartToStorage();
            updateCart();
            Swal.fire({
                icon: 'success',
                title: 'Carrito vacío',
                timer: 1400,
                showConfirmButton: false
            });
        }
    });
}

/* ========================
   Carga inicial de datos (API)
   ======================== */
async function loadData() {
    try {
        const res = await fetch('api/get_productos.php');
        if (!res.ok) throw new Error('No se pudo obtener productos');
        storeData = await res.json();
        if (storeData.error) throw new Error(storeData.error || 'Error API');

        allProducts = storeData.productos || [];

        // Rellenar datos del negocio en UI
        document.getElementById('storeName').textContent = storeData.negocio.nombre || 'Mi Tienda';
        document.getElementById('footerStoreName').textContent = storeData.negocio.nombre || 'Mi Tienda';
        document.getElementById('footerCopyright').textContent = storeData.negocio.nombre || 'Mi Tienda';
        document.getElementById('footerWhatsapp').textContent = storeData.negocio.whatsapp || '';

        // Mostrar email/dirección si existen
        const footerEmail = document.getElementById('footerEmail');
        const footerDireccion = document.getElementById('footerDireccion');
        if (storeData.negocio.email) {
            footerEmail.textContent = '📧 ' + storeData.negocio.email;
        } else {
            footerEmail.textContent = '';
        }
        if (storeData.negocio.direccion) {
            footerDireccion.textContent = '📍 ' + storeData.negocio.direccion;
        } else {
            footerDireccion.textContent = '';
        }

        // WhatsApp flotante
        const wa = document.getElementById('whatsappFloat');
        if (storeData.negocio.whatsapp) {
            wa.href = `https://wa.me/${storeData.negocio.whatsapp}`;
            wa.classList.remove('hidden');
        }

        renderSocialMedia();
        loadCartFromStorage();
        document.getElementById('loadingScreen').classList.add('hidden');

        renderCategories();
        filterProducts();
    } catch (err) {
        console.error(err);
        document.getElementById('loadingScreen').classList.add('hidden');
        document.getElementById('errorMessage')?.classList.remove('hidden');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudieron cargar los productos.'
        });
    }
}

/* ========================
 Mostrar home (ocultar detalle)
 ======================== */
function showHome() {
    const home = document.getElementById('homeView');
    const detail = document.getElementById('productDetailView');

    if (home) home.classList.remove('hidden');
    if (detail) detail.classList.add('hidden');

    currentPage = 1;
    filterProducts();

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}


/* ========================
Social media render (circular premium)
======================== */
function renderSocialMedia() {
    const container = document.getElementById('socialMediaLinks');

    // Validación correcta
    if (!container || !storeData || !storeData.negocio) return;

    const s = storeData.negocio;
    let html = '';

    // Estilo base circular
    const base =
        `w-12 h-12 flex items-center justify-center 
         rounded-full text-white text-xl
         shadow-md hover:shadow-xl 
         transition-all duration-200 
         hover:-translate-y-1`;

    if (s.facebook) {
        html += `
            <a href="${s.facebook}" target="_blank" 
                class="${base} bg-blue-600 hover:bg-blue-700">
                <i class="fab fa-facebook-f"></i>
            </a>
        `;
    }

    if (s.instagram) {
        html += `
            <a href="${s.instagram}" target="_blank" 
                class="${base} bg-gradient-to-br from-pink-500 to-purple-600 hover:opacity-90">
                <i class="fab fa-instagram"></i>
            </a>
        `;
    }

    if (s.twitter) {
        html += `
            <a href="${s.twitter}" target="_blank" 
                class="${base} bg-sky-500 hover:bg-sky-600">
                <i class="fab fa-twitter"></i>
            </a>
        `;
    }

    if (s.tiktok) {
        html += `
            <a href="${s.tiktok}" target="_blank" 
                class="${base} bg-black hover:bg-gray-800">
                <i class="fab fa-tiktok"></i>
            </a>
        `;
    }

    container.innerHTML = html || `<p class="text-gray-400">Síguenos en redes</p>`;
}


/* ========================
   Render categorías
   ======================== */
function getCategories() {
    const categories = ['all', ...new Set(allProducts.map(p => p.categoria || 'Sin categoría'))];
    return categories;
}

function renderCategories() {
    const container = document.getElementById('categoriesContainer');
    const categories = getCategories();
    container.innerHTML = categories.map(cat => `
            <button onclick="filterByCategory('${cat}')" class="px-4 py-2 rounded-full border font-semibold text-sm md:text-base md:px-6 cursor-pointer border-gray-200 ${currentFilter === cat ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'} shadow-sm hover:scale-[1.02] transition">
            ${cat === 'all' ? '🏷️ Todos' : cat}
            </button>
        `).join('');
}

/* ========================
   Filtrado y paginación
   ======================== */
function filterByCategory(category) {
    currentFilter = category;
    currentPage = 1;
    renderCategories();
    filterProducts();
}

function filterProducts() {
    const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
    let filtered = allProducts.slice();

    if (currentFilter !== 'all') filtered = filtered.filter(p => (p.categoria || '').toLowerCase() === currentFilter
        .toLowerCase());

    if (searchTerm) {
        filtered = filtered.filter(p => (p.nombre || '').toLowerCase().includes(searchTerm) || (p.descripcion || '')
            .toLowerCase().includes(searchTerm) || (p.categoria || '').toLowerCase().includes(searchTerm));
    }

    renderProducts(filtered);
    document.getElementById('productsInfo').textContent = `${filtered.length} productos`;
}

function renderPagination(totalProducts) {
    const totalPages = Math.ceil(totalProducts / productsPerPage);
    const container = document.getElementById('paginationContainer');
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '';
    if (currentPage > 1) html +=
        `<button onclick="changePage(${currentPage - 1})" class="px-3 py-2 bg-white rounded shadow">←</button>`;
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
            html +=
                `<button onclick="changePage(${i})" class="px-3 py-2 rounded ${i === currentPage ? 'bg-indigo-600 text-white' : 'bg-white'}">${i}</button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += `<span class="px-2">...</span>`;
        }
    }
    if (currentPage < totalPages) html +=
        `<button onclick="changePage(${currentPage + 1})" class="px-3 py-2 bg-white rounded shadow">→</button>`;
    container.innerHTML = html;
}

function changePage(page) {
    currentPage = page;
    filterProducts();
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

/* ========================
   Renderizar productos (cards)
   ======================== */
function renderProducts(products) {
    const grid = document.getElementById('productsGrid');
    const noResults = document.getElementById('noResults');

    if (!products || products.length === 0) {
        grid.classList.add('hidden');
        noResults.classList.remove('hidden');
        document.getElementById('paginationContainer').innerHTML = '';
        return;
    }

    grid.classList.remove('hidden');
    noResults.classList.add('hidden');

    const start = (currentPage - 1) * productsPerPage;
    const slice = products.slice(start, start + productsPerPage);

    grid.innerHTML = slice.map(p => {
        const img = p.imagen || 'https://via.placeholder.com/600x600?text=Sin+imagen';
        const price = (typeof p.precio === 'number') ? p.precio.toFixed(2) : '0.00';

        const stockBadge =
            p.stock === 0 ?
                `<span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full">Agotado</span>` :
                p.stock < 5 ?
                    `<span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">Pocas unidades</span>` :
                    '';

        return `
        <article 
            class="bg-white rounded-2xl overflow-hidden shadow-md border border-gray-100
            hover:shadow-xl hover:-translate-y-1 transition-all duration-300">

            <a href="producto.php?id=${p.id}" class="block relative group">

                <!-- Imagen cuadrada perfecta sin deformación -->
                <div class="w-full aspect-square bg-gray-100 overflow-hidden">
                    <img 
                        src="${img}" 
                        alt="${p.nombre || ''}" 
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500 ease-out"
                    >
                </div>

                <!-- Badge de stock -->
                <div class="absolute top-3 right-3">
                    ${stockBadge}
                </div>
            </a>

            <div class="p-4 flex flex-col gap-2">

                <h3 class="text-lg font-semibold text-gray-800 line-clamp-2">
                    ${p.nombre}
                </h3>

                <p class="text-sm text-gray-500 line-clamp-2">
                    ${p.descripcion || ''}
                </p>

                <div class="mt-2 flex items-center justify-between">

                    <div class="text-indigo-600 font-extrabold text-xl">
                        ${storeData?.negocio?.moneda || 'S/'} ${price}
                    </div>

                    <div class="flex items-center gap-2">

                        <!-- Agregar al carrito -->
                        <button 
                            onclick="event.preventDefault(); event.stopPropagation(); addToCart(${p.id})" 
                            class="p-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition">
                            <i class="fa-solid fa-cart-plus"></i>
                        </button>

                        <!-- Ver detalle -->
                        <button 
                            onclick="showProductDetail(${p.id})" 
                            class="p-2 rounded-xl bg-gray-100 text-gray-700 border border-gray-200 
                            hover:bg-gray-200 transition">
                            <i class="fa-solid fa-eye"></i>
                        </button>

                    </div>
                </div>
            </div>
        </article>
    `;
    }).join("");


    renderPagination(products.length);
}

/* ========================
   Carrito: añadir / actualizar
   ======================== */
function addToCart(productId) {
    const product = allProducts.find(p => p.id === productId);
    if (!product) {
        Toast.fire({
            icon: 'error',
            title: 'Producto no encontrado'
        });
        return;
    }
    if (product.stock === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Agotado',
            text: 'Este producto está agotado.'
        });
        return;
    }

    const existing = cart.find(i => i.id === productId);
    if (existing) {
        existing.quantity++;
    } else {
        cart.push({
            id: product.id,
            nombre: product.nombre,
            precio: product.precio,
            imagen: product.imagen,
            quantity: 1
        });
    }

    saveCartToStorage();
    updateCart();

    Toast.fire({
        icon: 'success',
        title: 'Añadido al carrito',
        text: product.nombre
    });
}

function updateCart() {
    const cartCount = document.getElementById('cartCount');
    const totalItems = cart.reduce((s, item) => s + item.quantity, 0);
    cartCount.textContent = totalItems;
    cartCount.classList.add('cart-badge-anim');
    setTimeout(() => cartCount.classList.remove('cart-badge-anim'), 380);
    renderCart();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    const cartTotalEl = document.getElementById('cartTotal');

    if (cart.length === 0) {
        container.innerHTML = `
                <div class="flex flex-col items-center justify-center py-12 text-gray-600 animate-fadeIn">

                    <div class="w-28 h-28 flex items-center justify-center rounded-full 
                                bg-white/80 backdrop-blur-md shadow-lg border border-gray-200 mb-4">
                        <i class="fa-solid fa-cart-arrow-down text-5xl text-indigo-500 opacity-80"></i>
                    </div>

                    <h3 class="text-xl font-bold text-gray-700 mb-1">
                        Tu carrito está vacío
                    </h3>

                    <p class="text-gray-500 text-sm">
                        Agrega productos para continuar con tu compra
                    </p>
                </div>
            `;

        cartTotalEl.textContent = `${storeData?.negocio?.moneda || 'S/'} 0.00`;
        return;
    }


    let total = 0;
    container.innerHTML = cart.map(item => {
        const subtotal = (item.precio * item.quantity) || 0;
        total += subtotal;
        return `
                <div class="flex items-center gap-4 mb-3 p-3 bg-gray-50 rounded-lg">
                    <img src="${item.imagen || 'https://via.placeholder.com/80'}" class="w-16 h-16 object-cover rounded" alt="${item.nombre}">
                    <div class="flex-1">
                    <h4 class="font-semibold">${item.nombre}</h4>
                    <div class="text-sm text-gray-500">${storeData?.negocio?.moneda || 'S/'} ${item.precio.toFixed(2)}</div>
                    </div>
                    <div class="flex items-center gap-2">
                    <button onclick="changeQuantity(${item.id}, -1)" class="px-2 py-1 bg-gray-200 rounded">-</button>
                    <div class="w-8 text-center">${item.quantity}</div>
                    <button onclick="changeQuantity(${item.id}, 1)" class="px-2 py-1 bg-indigo-600 text-white rounded">+</button>
                    </div>
                    <button onclick="removeFromCart(${item.id})" class="text-red-500 ml-2"><i class="fa-solid fa-trash"></i></button>
                </div>
                `;
    }).join('');

    cartTotalEl.textContent = `${storeData?.negocio?.moneda || 'S/'} ${total.toFixed(2)}`;
}

/* ========================
   Cambiar cantidad / remover
   ======================== */
function changeQuantity(productId, delta) {
    const item = cart.find(i => i.id === productId);
    if (!item) return;
    item.quantity += delta;
    if (item.quantity <= 0) {
        removeFromCart(productId);
    } else {
        saveCartToStorage();
        updateCart();
    }
}

function removeFromCart(productId) {
    cart = cart.filter(i => i.id !== productId);
    saveCartToStorage();
    updateCart();
}

/* ========================
   Toggle carrito modal
   ======================== */
function toggleCart() {
    document.getElementById('cartModal').classList.toggle('hidden');
}




/* ========================
   Enviar pedido por WhatsApp (con validaciones)
   ======================== */
function sendToWhatsApp() {
    if (cart.length === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Carrito vacío',
            text: 'Agrega productos antes de enviar el pedido.'
        });
        return;
    }

    let message = `🛒 *Nuevo Pedido - ${storeData?.negocio?.nombre || 'Mi Tienda'}*%0A%0A`;
    cart.forEach(it => {
        message += encodeURIComponent(
            `• ${it.nombre} — Cant: ${it.quantity} — ${storeData?.negocio?.moneda || 'S/'} ${it.precio.toFixed(2)}%0A`
        );
    });

    const total = cart.reduce((s, i) => s + (i.precio * i.quantity), 0);
    message += encodeURIComponent(`%0A*Total: ${storeData?.negocio?.moneda || 'S/'} ${total.toFixed(2)}*`);

    const phone = storeData?.negocio?.whatsapp || '';
    if (!phone) {
        Swal.fire({
            icon: 'error',
            title: 'WhatsApp no configurado',
            text: 'No se encontró número de contacto.'
        });
        return;
    }

    const url = `https://wa.me/${phone}?text=${message}`;
    window.open(url, '_blank');
}

/* ========================
   Detalle de producto (modal)
   ======================== */
let currentProductId = null;

function showProductDetail(productId) {
    currentProductId = productId;
    const p = allProducts.find(x => x.id === productId);
    if (!p) return;

    const detailView = document.getElementById('productDetailView');
    detailView.classList.remove('hidden');

    const content = document.getElementById('productDetailContent');

    content.innerHTML = `
        <div class="animate-fade-in">
            
            <!-- Botón para volver -->
            <button 
                onclick="closeProductDetail()" 
                class="mb-4 flex items-center gap-2 text-blue-600 hover:text-blue-800 font-semibold">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <img src="${p.imagen || 'https://via.placeholder.com/800x600'}" 
                        class="w-full h-auto rounded-lg object-cover shadow-md">
                </div>

                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                            ${p.categoria || ''}
                        </span>
                    </div>

                    <h3 class="text-3xl font-bold mb-2">${p.nombre}</h3>
                    <p class="text-gray-600 mb-4 leading-relaxed">${p.descripcion || ''}</p>

                    <div class="text-3xl font-extrabold text-indigo-600 mb-6">
                        ${storeData?.negocio?.moneda || 'S/'} ${Number(p.precio).toFixed(2)}
                    </div>

                    <div class="flex gap-3">
                        <button onclick="addToCart(${p.id});"
                            class="bg-indigo-600 text-white px-5 py-3 rounded-lg hover:bg-indigo-700 transition">
                            <i class="fa-solid fa-cart-plus"></i> Agregar al carrito
                        </button>

                        <button onclick="buyNow(${p.id})"
                            class="bg-green-500 text-white px-5 py-3 rounded-lg hover:bg-green-600 transition">
                            <i class="fa-solid fa-bolt"></i> Comprar ahora
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function closeProductDetail() {
    document.getElementById('productDetailView').classList.add('hidden');
}



/* ========================
   Comprar ahora (agrega y abre WA)
   ======================== */
function buyNow(productId) {
    addToCart(productId);
    // pequeña espera para que se actualice carrito
    setTimeout(() => {
        sendToWhatsApp();
    }, 250);
}

/* ========================
   Productos relacionados (si usas)
   ======================== */
function renderRelatedProducts(category, excludeId) {
    // opcional: ya tenías lógica — se puede incluir aquí si quieres
}



/* ========================
   Inicio
   ======================== */
loadData();