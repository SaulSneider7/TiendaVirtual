<?php
// Si tu index.php usa PHP para sesiones/headers, mantenlo.
// Este archivo asume que tu API sigue en api/get_productos.php
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tienda Online — Juvenil / Elegante</title>

    <!-- Tailwind CDN (rápido para prototipo). En producción usa build con purge. -->
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <link rel="stylesheet" href="./assets/output.css">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    /* Micro estilos adicionales */
    .card-hover {
        transition: transform .28s cubic-bezier(.2, .8, .2, 1), box-shadow .28s;
    }

    .card-hover:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 40px rgba(20, 40, 80, 0.12);
    }

    .pulse-btn {
        transition: transform .18s;
    }

    .pulse-btn:active {
        transform: scale(.98);
    }

    .cart-badge-anim {
        animation: cartPulse .36s ease;
    }

    @keyframes cartPulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.25);
        }

        100% {
            transform: scale(1);
        }
    }

    /* loader pequeño */
    .skeleton {
        background: linear-gradient(90deg, #f3f4f6, #e6eefc, #f3f4f6);
        background-size: 200% 100%;
        animation: shine 1.2s linear infinite;
    }

    @keyframes shine {
        0% {
            background-position: 200% 0
        }

        100% {
            background-position: -200% 0
        }
    }


    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes zoomIn {
        from {
            transform: scale(0.85);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .animate-fadeIn {
        animation: fadeIn .25s ease-out;
    }

    .animate-zoomIn {
        animation: zoomIn .25s ease-out;
    }
    </style>
</head>

<body class="bg-gradient-to-b from-white to-gray-50 text-gray-800">

    <!-- LOADER -->
    <div id="loadingScreen" class="fixed inset-0 z-50 flex items-center justify-center bg-white">
        <div class="text-center">
            <div class="w-14 h-14 rounded-full border-4 border-blue-100 border-t-blue-600 animate-spin mx-auto"></div>
            <p class="mt-4 text-gray-600">Cargando productos...</p>
        </div>
    </div>

    <!-- WHATSAPP FLOTANTE -->
    <a id="whatsappFloat" class="hidden fixed right-6 bottom-6 z-40" target="_blank" rel="noopener noreferrer">
        <div
            class="w-16 h-16 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white shadow-2xl transform hover:scale-105 transition">
            <i class="fab fa-whatsapp text-3xl"></i>
        </div>
    </a>

    <!-- HEADER -->
    <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-xl shadow-sm border-b border-gray-100">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">

            <!-- LOGO + NOMBRE -->
            <button onclick="showHome()" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-indigo-500 to-blue-600 
                        flex items-center justify-center text-white shadow-md 
                        group-hover:scale-105 transition">
                    <i class="fa-solid fa-store text-xl"></i>
                </div>

                <div class="text-left">
                    <h1 id="storeName"
                        class="text-xl font-extrabold text-gray-800 tracking-tight group-hover:text-indigo-600 transition">
                        Mi Tienda
                    </h1>
                    <p class="text-xs text-gray-500">Tienda juvenil & elegante</p>
                </div>
            </button>

            <!-- BUSCADOR -->
            <div class="relative w-1/2 max-w-xl">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input id="searchInput" oninput="filterProducts()" placeholder="Buscar productos, categorías..." class="w-full pl-12 pr-4 py-3 rounded-full border border-gray-200 shadow-sm 
                       focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 
                       transition bg-white" />
            </div>

            <!-- CARRITO -->
            <button title="Ver carrito" onclick="toggleCart()" class="relative bg-white border border-gray-200 px-4 py-2 rounded-xl 
                   flex items-center gap-2 hover:shadow-lg transition-all focus:ring-2 
                   focus:ring-indigo-300 active:scale-95">
                <i class="fa-solid fa-cart-shopping text-lg text-indigo-600"></i>
                <span class="hidden md:inline font-semibold text-gray-700">Carrito</span>

                <span id="cartCount" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full 
                       w-6 h-6 flex items-center justify-center shadow-md animate-pulse">
                    0
                </span>
            </button>
        </div>
    </header>


    <!-- MAIN -->
    <main class="container mx-auto px-4 mt-8">

        <!-- CATEGORÍAS -->
        <section class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold">Categorías</h2>
                <div class="text-sm text-gray-500">Explora por secciones</div>
            </div>
            <div id="categoriesContainer" class="flex flex-wrap gap-3"></div>
        </section>

        <!-- GRID PRODUCTOS -->
        <section>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold">Productos</h2>
                <div id="productsInfo" class="text-sm text-gray-500"></div>
            </div>

            <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Cards se renderizan por JS -->
            </div>

            <div id="noResults" class="hidden text-center py-12">
                <p class="text-gray-500 text-xl">No se encontraron productos</p>
            </div>

            <div id="paginationContainer" class="flex justify-center items-center gap-2 mt-8"></div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="mt-16 bg-white border-t pt-12 pb-8 border-gray-100 text-gray-500">
        <div class="container mx-auto px-4 pb-12 grid grid-cols-1 md:grid-cols-3 gap-10">

            <!-- INFO GENERAL -->
            <div>
                <h3 id="footerStoreName"
                    class="text-2xl font-extrabold bg-gradient-to-r from-indigo-600 to-blue-600 bg-clip-text text-transparent">
                    Mi Tienda
                </h3>

                <p class="text-gray-500 mt-3 leading-relaxed">
                    Diseños juveniles y productos con estilo.
                    Atención rápida por WhatsApp 💬
                </p>

                <!-- Redes -->
                <div id="socialMediaLinks" class="flex gap-3 mt-5"></div>
            </div>

            <!-- ENLACES -->
            <div>
                <h4 class="text-lg font-semibold text-gray-800 border-l-4 border-indigo-500 pl-3 mb-3">
                    Enlaces rápidos
                </h4>

                <ul class="space-y-3 text-gray-600">
                    <li>
                        <a href="#" onclick="showHome()"
                            class="hover:text-indigo-600 transition flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-xs"></i> Inicio
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="toggleCart()"
                            class="hover:text-indigo-600 transition flex items-center gap-2">
                            <i class="fa-solid fa-chevron-right text-xs"></i> Mi carrito
                        </a>
                    </li>
                </ul>
            </div>

            <!-- CONTACTO -->
            <div>
                <h4 class="text-lg font-semibold text-gray-800 border-l-4 border-indigo-500 pl-3 mb-3">
                    Contacto
                </h4>

                <div class="space-y-2 text-gray-600">
                    <p class="flex items-center gap-2">
                        <i class="fab fa-whatsapp text-green-500"></i>
                        <span id="footerWhatsapp"></span>
                    </p>

                    <p class="flex items-center gap-2">
                        <i class="fa-solid fa-envelope text-indigo-500"></i>
                        <span id="footerEmail"></span>
                    </p>

                    <p class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-red-500"></i>
                        <span id="footerDireccion"></span>
                    </p>
                </div>
            </div>
        </div>

        <!-- COPYRIGHT -->
        <div class="bg-gray-50 border-t py-4 text-center text-gray-500 text-sm border-gray-200">
            © 2025 <span id="footerCopyright">Mi Tienda</span> — Todos los derechos reservados.
        </div>
    </footer>


    <!-- CART MODAL -->
    <div id="cartModal" class="hidden fixed inset-0 z-50 flex items-center justify-center 
            bg-black/40 backdrop-blur-sm p-4 animate-fadeIn">

        <div class="bg-white/90 backdrop-blur-lg rounded-3xl w-full max-w-2xl 
                shadow-[0_8px_35px_rgba(0,0,0,0.2)] overflow-hidden 
                scale-95 animate-zoomIn border border-white/40">

            <!-- HEADER -->
            <div class="flex justify-between items-center p-5 
                    bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                <h3 class="text-xl font-bold flex items-center gap-2">
                    <i class="fa-solid fa-cart-shopping text-2xl"></i>
                    Tu carrito
                </h3>

                <div class="flex items-center gap-3">
                    <button onclick="clearCart()" class="px-3 py-2 rounded-xl bg-white/20 hover:bg-white/30 
                           text-red-100 font-semibold transition-all duration-200 
                           flex items-center gap-2">
                        <i class="fa-solid fa-trash"></i> Vaciar
                    </button>

                    <button onclick="toggleCart()" class="text-white text-2xl hover:scale-125 transition">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>

            <!-- ITEMS -->
            <div id="cartItems" class="p-5 max-h-[60vh] overflow-y-auto scrollbar-thin 
                    scrollbar-thumb-indigo-400 scrollbar-track-transparent">
            </div>

            <!-- FOOTER -->
            <div class="p-5 border-t bg-gray-50/70 backdrop-blur-sm flex flex-col gap-4">

                <div class="flex justify-between items-center">
                    <span class="font-semibold text-lg">Total</span>
                    <span id="cartTotal" class="text-2xl font-black text-indigo-600">S/ 0.00</span>
                </div>

                <div class="flex gap-4">
                    <button onclick="sendToWhatsApp()" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-3 rounded-2xl 
                           font-bold tracking-wide shadow-md hover:shadow-lg 
                           transition-all flex items-center justify-center gap-2">
                        <i class="fab fa-whatsapp text-xl"></i>
                        Enviar por WhatsApp
                    </button>

                    <button onclick="toggleCart()" class="flex-1 bg-white border border-gray-300 
                           hover:bg-gray-100 py-3 rounded-2xl font-semibold 
                           shadow-sm transition-all">
                        Seguir comprando
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- PRODUCT DETAIL VIEW (hidden, usado por showProductDetail) -->
    <div id="productDetailView" class="hidden fixed inset-0 z-50 overflow-auto bg-black/40 p-6">
        <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6" id="productDetailContent"></div>
        </div>
    </div>

    <!-- SCRIPTS: TU JS MEJORADO (reemplazando alert/confirm por SweetAlert2) -->
    <script>
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
            <button onclick="filterByCategory('${cat}')" class="px-4 py-2 rounded-full border font-semibold text-sm md:text-base md:px-6 cursor-pointer border-gray-200 ${currentFilter===cat ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'} shadow-sm hover:scale-[1.02] transition">
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
            `<button onclick="changePage(${currentPage-1})" class="px-3 py-2 bg-white rounded shadow">←</button>`;
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                html +=
                    `<button onclick="changePage(${i})" class="px-3 py-2 rounded ${i===currentPage ? 'bg-indigo-600 text-white' : 'bg-white'}">${i}</button>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += `<span class="px-2">...</span>`;
            }
        }
        if (currentPage < totalPages) html +=
            `<button onclick="changePage(${currentPage+1})" class="px-3 py-2 bg-white rounded shadow">→</button>`;
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
    </script>
</body>

</html>