<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
    .cart-badge {
        animation: bounce 0.3s ease;
    }

    @keyframes bounce {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.2);
        }
    }

    .loader {
        border: 4px solid #f3f4f6;
        border-top: 4px solid #3B82F6;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .whatsapp-float {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 100;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Loading Screen -->
    <div id="loadingScreen" class="fixed inset-0 bg-white z-50 flex items-center justify-center">
        <div class="text-center">
            <div class="loader mx-auto mb-4"></div>
            <p class="text-gray-600">Cargando productos...</p>
        </div>
    </div>

    <!-- WhatsApp Float Button -->
    <a id="whatsappFloat" href="#" target="_blank" class="whatsapp-float hidden">
        <div
            class="bg-green-500 hover:bg-green-600 text-white rounded-full w-16 h-16 flex items-center justify-center shadow-2xl transition">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
            </svg>
        </div>
    </a>

    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-5">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl md:text-3xl font-bold cursor-pointer" id="storeName" onclick="showHome()">
                    Mi Tienda
                </h1>
                <button onclick="toggleCart()"
                    class="relative bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition">
                    🛒 Carrito
                    <span id="cartCount"
                        class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center cart-badge">0</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Home View -->
    <div id="homeView" class="mt-6 sm:mt-10">
        <!-- Search Bar -->
        <div class="max-w-2xl mx-auto px-4 mb-6">
            <input type="text" id="searchInput" placeholder="🔍 Buscar productos..."
                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-300 focus:outline-none shadow-sm transition"
                oninput="filterProducts()">
        </div>

        <!-- Categorías -->
        <div class="container mx-auto px-4 mb-8">
            <div class="flex flex-wrap gap-3 justify-center" id="categoriesContainer"></div>
        </div>

        <!-- Productos -->
        <div class="container mx-auto px-4 pb-12">
            <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"></div>
            <div id="noResults" class="hidden text-center py-12">
                <p class="text-gray-500 text-xl">No se encontraron productos</p>
            </div>
            <div id="errorMessage" class="hidden text-center py-12">
                <p class="text-red-500 text-xl mb-4">❌ Error al cargar los productos</p>
                <p class="text-gray-600">Por favor, intenta nuevamente más tarde</p>
            </div>
            <div id="paginationContainer" class="flex justify-center items-center gap-2 mt-8"></div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4" id="footerStoreName">Mi Tienda</h3>
                    <p class="text-gray-400 mb-4">Tu tienda online de confianza. Calidad y servicio garantizados.</p>

                    <!-- Redes Sociales -->
                    <div id="socialMediaLinks" class="flex gap-3 mt-4"></div>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" onclick="showHome()" class="hover:text-white transition">Inicio</a></li>
                        <li><a href="#" onclick="toggleCart()" class="hover:text-white transition">Mi Carrito</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-lg font-bold mb-4">Contacto</h4>
                    <p class="text-gray-400 mb-2">📱 WhatsApp: <span id="footerWhatsapp"></span></p>
                    <p class="text-gray-400 mb-2" id="footerEmail"></p>
                    <p class="text-gray-400" id="footerDireccion"></p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 <span id="footerCopyright">Mi Tienda</span>. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Shopping Cart Modal -->
    <div id="cartModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold">🛒 Tu Carrito</h2>
                    <button onclick="toggleCart()" class="text-white hover:text-gray-200 text-3xl">&times;</button>
                </div>
            </div>

            <div id="cartItems" class="flex-1 overflow-y-auto p-6"></div>

            <div class="border-t p-6 bg-gray-50">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-xl font-bold">Total:</span>
                    <span id="cartTotal" class="text-2xl font-bold text-blue-600">S/ 0.00</span>
                </div>
                <button onclick="sendToWhatsApp()"
                    class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-lg transition flex items-center justify-center gap-2 text-lg">
                    <span>📱</span> Enviar pedido por WhatsApp
                </button>
                <button onclick="clearCart()"
                    class="w-full mt-2 bg-red-100 hover:bg-red-200 text-red-600 font-semibold py-2 rounded-lg transition">
                    Vaciar carrito
                </button>
            </div>
        </div>
    </div>

    <script>
    let storeData = null;
    let cart = [];
    let currentFilter = 'all';
    let allProducts = [];
    let currentPage = 1;
    const productsPerPage = 12;

    function loadCartFromStorage() {
        try {
            const savedCart = localStorage.getItem('shopping_cart');
            if (savedCart) {
                cart = JSON.parse(savedCart);
                updateCart();
            }
        } catch (error) {
            console.error('Error loading cart:', error);
        }
    }

    function saveCartToStorage() {
        try {
            localStorage.setItem('shopping_cart', JSON.stringify(cart));
        } catch (error) {
            console.error('Error saving cart:', error);
        }
    }

    function clearCart() {
        if (confirm('¿Estás seguro de vaciar el carrito?')) {
            cart = [];
            saveCartToStorage();
            updateCart();
        }
    }

    async function loadData() {
        try {
            const response = await fetch('api/get_productos.php');
            if (!response.ok) {
                throw new Error('No se pudo cargar los productos');
            }
            storeData = await response.json();

            if (storeData.error) {
                throw new Error(storeData.error);
            }

            allProducts = storeData.productos;

            document.getElementById('storeName').textContent = storeData.negocio.nombre;
            document.getElementById('footerStoreName').textContent = storeData.negocio.nombre;
            document.getElementById('footerCopyright').textContent = storeData.negocio.nombre;
            document.getElementById('footerWhatsapp').textContent = storeData.negocio.whatsapp;

            // Mostrar email y dirección si existen
            const footerEmail = document.getElementById('footerEmail');
            const footerDireccion = document.getElementById('footerDireccion');

            if (storeData.negocio.email && storeData.negocio.email !== '') {
                footerEmail.textContent = '📧 ' + storeData.negocio.email;
                footerEmail.style.display = 'block';
            } else {
                footerEmail.style.display = 'none';
            }

            if (storeData.negocio.direccion && storeData.negocio.direccion !== '') {
                footerDireccion.textContent = '📍 ' + storeData.negocio.direccion;
                footerDireccion.style.display = 'block';
            } else {
                footerDireccion.style.display = 'none';
            }

            // Renderizar redes sociales
            renderSocialMedia();

            document.title = storeData.negocio.nombre;

            const whatsappFloat = document.getElementById('whatsappFloat');
            whatsappFloat.href = `https://wa.me/${storeData.negocio.whatsapp}`;
            whatsappFloat.classList.remove('hidden');

            loadCartFromStorage();

            document.getElementById('loadingScreen').classList.add('hidden');

            renderCategories();
            filterProducts();
        } catch (error) {
            console.error('Error al cargar datos:', error);
            document.getElementById('loadingScreen').classList.add('hidden');
            document.getElementById('errorMessage').classList.remove('hidden');
        }
    }

    function showHome() {
        document.getElementById('homeView').classList.remove('hidden');
        document.getElementById('productDetailView').classList.add('hidden');
        currentProductId = null;
        currentPage = 1;
        filterProducts();
        window.scrollTo(0, 0);
    }

    // Renderizar redes sociales
    function renderSocialMedia() {
        const container = document.getElementById('socialMediaLinks');
        if (!container || !storeData || !storeData.negocio) {
            return;
        }

        const social = storeData.negocio;
        let html = '';

        if (social.facebook && social.facebook.trim() !== '') {
            html += `<a href="${social.facebook}" target="_blank" rel="noopener noreferrer" class="bg-blue-600 hover:bg-blue-700 w-10 h-10 rounded-full flex items-center justify-center transition" title="Facebook">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </a>`;
        }

        if (social.instagram && social.instagram.trim() !== '') {
            html += `<a href="${social.instagram}" target="_blank" rel="noopener noreferrer" class="bg-gradient-to-br from-purple-600 to-pink-500 hover:from-purple-700 hover:to-pink-600 w-10 h-10 rounded-full flex items-center justify-center transition" title="Instagram">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </a>`;
        }

        if (social.twitter && social.twitter.trim() !== '') {
            html += `<a href="${social.twitter}" target="_blank" rel="noopener noreferrer" class="bg-blue-400 hover:bg-blue-500 w-10 h-10 rounded-full flex items-center justify-center transition" title="Twitter/X">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                </a>`;
        }

        if (social.tiktok && social.tiktok.trim() !== '') {
            html += `<a href="${social.tiktok}" target="_blank" rel="noopener noreferrer" class="bg-gray-800 hover:bg-gray-900 w-10 h-10 rounded-full flex items-center justify-center transition" title="TikTok">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                </a>`;
        }

        if (html === '') {
            container.innerHTML = '<p class="text-gray-400 text-sm">Síguenos en redes sociales</p>';
        } else {
            container.innerHTML = html;
        }
    }

    function showProductDetail(productId) {
        currentProductId = productId;
        const product = allProducts.find(p => p.id === productId);

        if (!product) return;

        document.getElementById('homeView').classList.add('hidden');
        document.getElementById('productDetailView').classList.remove('hidden');

        const detailContent = document.getElementById('productDetailContent');
        detailContent.innerHTML = `
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
                        <div>
                            <img src="${product.imagen}" alt="${product.nombre}" class="w-full rounded-lg shadow-md" loading="lazy">
                        </div>
                        <div class="flex flex-col justify-center">
                            <div class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold mb-4 w-fit">
                                ${product.categoria}
                            </div>
                            <h1 class="text-3xl md:text-4xl font-bold mb-4">${product.nombre}</h1>
                            <p class="text-gray-600 text-lg mb-6 leading-relaxed">${product.descripcion}</p>
                            <div class="mb-6">
                                <span class="text-4xl font-bold text-blue-600">${storeData.negocio.moneda} ${product.precio.toFixed(2)}</span>
                                ${product.stock < 5 ? `<p class="text-red-600 font-semibold mt-2">⚠️ Solo quedan ${product.stock} unidades</p>` : ''}
                            </div>
                            <div class="flex gap-4">
                                <button 
                                    onclick="addToCart(${product.id})"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg transition text-lg"
                                    ${product.stock === 0 ? 'disabled' : ''}
                                >
                                    ${product.stock === 0 ? '⛔ Agotado' : '🛒 Agregar al carrito'}
                                </button>
                                <button 
                                    onclick="buyNow(${product.id})"
                                    class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-6 rounded-lg transition text-lg"
                                    ${product.stock === 0 ? 'disabled' : ''}
                                >
                                    📱 Comprar ahora
                                </button>
                            </div>
                            <div class="mt-8 border-t pt-6">
                                <h3 class="font-bold text-lg mb-3">Características:</h3>
                                <ul class="space-y-2 text-gray-700">
                                    <li class="flex items-center"><span class="mr-2">✓</span> Producto de alta calidad</li>
                                    <li class="flex items-center"><span class="mr-2">✓</span> Envío rápido y seguro</li>
                                    <li class="flex items-center"><span class="mr-2">✓</span> Garantía de satisfacción</li>
                                    <li class="flex items-center"><span class="mr-2">✓</span> Atención personalizada</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            `;

        renderRelatedProducts(product.categoria, product.id);

        window.scrollTo(0, 0);
    }

    function renderRelatedProducts(category, excludeId) {
        const related = allProducts.filter(p => p.categoria === category && p.id !== excludeId).slice(0, 4);
        const container = document.getElementById('relatedProducts');

        if (related.length === 0) {
            container.innerHTML =
                '<p class="text-center text-gray-500 col-span-full">No hay productos relacionados</p>';
            return;
        }

        container.innerHTML = related.map(product => `
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1 cursor-pointer" onclick="showProductDetail(${product.id})">
                    <img src="${product.imagen}" alt="${product.nombre}" class="w-full h-48 object-cover" loading="lazy">
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">${product.nombre}</h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">${product.descripcion}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-bold text-blue-600">${storeData.negocio.moneda} ${product.precio.toFixed(2)}</span>
                            <button 
                                onclick="event.stopPropagation(); addToCart(${product.id})"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg font-semibold transition text-sm"
                            >
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
    }

    function buyNow(productId) {
        addToCart(productId);
        sendToWhatsApp();
    }

    function getCategories() {
        const categories = ['all', ...new Set(allProducts.map(p => p.categoria))];
        return categories;
    }

    function renderCategories() {
        const container = document.getElementById('categoriesContainer');
        const categories = getCategories();

        container.innerHTML = categories.map(cat => `
                <button 
                    onclick="filterByCategory('${cat}')"
                    class="px-4 py-2 rounded-full font-semibold transition ${
                        currentFilter === cat 
                            ? 'bg-blue-600 text-white' 
                            : 'bg-white text-gray-700 hover:bg-gray-100'
                    } shadow-sm"
                >
                    ${cat === 'all' ? '🏷️ Todos' : cat}
                </button>
            `).join('');
    }

    function filterByCategory(category) {
        currentFilter = category;
        currentPage = 1;
        renderCategories();
        filterProducts();
    }

    function renderPagination(totalProducts) {
        const totalPages = Math.ceil(totalProducts / productsPerPage);
        const container = document.getElementById('paginationContainer');

        if (totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let paginationHTML = '';

        if (currentPage > 1) {
            paginationHTML +=
                `<button onclick="changePage(${currentPage - 1})" class="px-4 py-2 bg-white rounded-lg shadow hover:bg-gray-100 transition font-semibold">← Anterior</button>`;
        }

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                paginationHTML += `
                        <button 
                            onclick="changePage(${i})" 
                            class="px-4 py-2 rounded-lg shadow font-semibold transition ${
                                i === currentPage 
                                    ? 'bg-blue-600 text-white' 
                                    : 'bg-white hover:bg-gray-100'
                            }"
                        >${i}</button>
                    `;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                paginationHTML += `<span class="px-2">...</span>`;
            }
        }

        if (currentPage < totalPages) {
            paginationHTML +=
                `<button onclick="changePage(${currentPage + 1})" class="px-4 py-2 bg-white rounded-lg shadow hover:bg-gray-100 transition font-semibold">Siguiente →</button>`;
        }

        container.innerHTML = paginationHTML;
    }

    function changePage(page) {
        currentPage = page;
        filterProducts();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function renderProducts(products) {
        const grid = document.getElementById('productsGrid');
        const noResults = document.getElementById('noResults');

        if (products.length === 0) {
            grid.classList.add('hidden');
            noResults.classList.remove('hidden');
            document.getElementById('paginationContainer').innerHTML = '';
            return;
        }

        grid.classList.remove('hidden');
        noResults.classList.add('hidden');

        const startIndex = (currentPage - 1) * productsPerPage;
        const endIndex = startIndex + productsPerPage;
        const paginatedProducts = products.slice(startIndex, endIndex);

        grid.innerHTML = paginatedProducts.map(product => `
                <a href="producto.php?id=${product.id}" class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1">
                    <img src="${product.imagen}" alt="${product.nombre}" class="w-full h-48 object-cover" loading="lazy">
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">${product.nombre}</h3>
                        <p class="text-gray-600 text-sm mb-3">${product.descripcion}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-blue-600">${storeData.negocio.moneda} ${product.precio.toFixed(2)}</span>
                            <button 
                                onclick="event.preventDefault(); event.stopPropagation(); addToCart(${product.id})"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition"
                            >
                                Agregar
                            </button>
                        </div>
                    </div>
                </a>
            `).join('');

        renderPagination(products.length);
    }

    function filterProducts() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        let filtered = allProducts;

        if (currentFilter !== 'all') {
            filtered = filtered.filter(p => p.categoria === currentFilter);
        }

        if (searchTerm) {
            filtered = filtered.filter(p =>
                p.nombre.toLowerCase().includes(searchTerm) ||
                p.descripcion.toLowerCase().includes(searchTerm) ||
                p.categoria.toLowerCase().includes(searchTerm)
            );
        }

        renderProducts(filtered);
    }

    function addToCart(productId) {
        const product = allProducts.find(p => p.id === productId);

        if (product.stock === 0) {
            alert('Este producto está agotado');
            return;
        }

        const existingItem = cart.find(item => item.id === productId);

        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({
                ...product,
                quantity: 1
            });
        }

        saveCartToStorage();
        updateCart();
    }

    function updateCart() {
        const cartCount = document.getElementById('cartCount');
        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
        cartCount.textContent = totalItems;

        if (totalItems > 0) {
            cartCount.classList.add('cart-badge');
            setTimeout(() => cartCount.classList.remove('cart-badge'), 300);
        }

        renderCart();
    }

    function renderCart() {
        const cartItems = document.getElementById('cartItems');
        const cartTotal = document.getElementById('cartTotal');

        if (cart.length === 0) {
            cartItems.innerHTML = '<p class="text-center text-gray-500 py-8">Tu carrito está vacío</p>';
            cartTotal.textContent = `${storeData.negocio.moneda} 0.00`;
            return;
        }

        const total = cart.reduce((sum, item) => sum + (item.precio * item.quantity), 0);

        cartItems.innerHTML = cart.map(item => `
                <div class="flex items-center gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                    <img src="${item.imagen}" alt="${item.nombre}" class="w-20 h-20 object-cover rounded" loading="lazy">
                    <div class="flex-1">
                        <h4 class="font-bold">${item.nombre}</h4>
                        <p class="text-gray-600">${storeData.negocio.moneda} ${item.precio.toFixed(2)}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="changeQuantity(${item.id}, -1)" class="bg-gray-300 hover:bg-gray-400 w-8 h-8 rounded-full font-bold">-</button>
                        <span class="w-8 text-center font-bold">${item.quantity}</span>
                        <button onclick="changeQuantity(${item.id}, 1)" class="bg-blue-600 hover:bg-blue-700 text-white w-8 h-8 rounded-full font-bold">+</button>
                    </div>
                    <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700 font-bold">🗑️</button>
                </div>
            `).join('');

        cartTotal.textContent = `${storeData.negocio.moneda} ${total.toFixed(2)}`;
    }

    function changeQuantity(productId, change) {
        const item = cart.find(i => i.id === productId);
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                removeFromCart(productId);
            } else {
                saveCartToStorage();
                updateCart();
            }
        }
    }

    function removeFromCart(productId) {
        cart = cart.filter(item => item.id !== productId);
        saveCartToStorage();
        updateCart();
    }

    function toggleCart() {
        const modal = document.getElementById('cartModal');
        modal.classList.toggle('hidden');
    }

    function sendToWhatsApp() {
        if (cart.length === 0) {
            alert('Tu carrito está vacío');
            return;
        }

        let message = `🛒 *Nuevo Pedido - ${storeData.negocio.nombre}*\n\n`;

        cart.forEach(item => {
            message += `📦 *${item.nombre}*\n`;
            message += `   Cantidad: ${item.quantity}\n`;
            message += `   Precio unitario: ${storeData.negocio.moneda} ${item.precio.toFixed(2)}\n`;
            message +=
                `   Subtotal: ${storeData.negocio.moneda} ${(item.precio * item.quantity).toFixed(2)}\n\n`;
        });

        const total = cart.reduce((sum, item) => sum + (item.precio * item.quantity), 0);
        message += `💰 *Total: ${storeData.negocio.moneda} ${total.toFixed(2)}*`;

        const encodedMessage = encodeURIComponent(message);
        const whatsappUrl = `https://wa.me/${storeData.negocio.whatsapp}?text=${encodedMessage}`;

        window.open(whatsappUrl, '_blank');
    }

    loadData();
    </script>
</body>

</html>