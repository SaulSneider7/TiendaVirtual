<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tienda Online — Juvenil / Elegante</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="./assets/img/favicon.ico" type="image/x-icon">
    <!-- Tailwind CDN (rápido para prototipo). En producción usa build con purge. -->
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <link rel="stylesheet" href="./assets/output.css">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- My CSS -->
    <link rel="stylesheet" href="./assets/css/index.css">

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

    <!-- Hero Shopify Style -->
    <section class="relative h-[90vh] w-full flex items-center justify-center text-center overflow-hidden">

        <!-- Fondo con imagen + blur -->
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=1600&q=80"
                class="w-full h-full object-cover scale-110 blur-lg opacity-70" />
        </div>

        <!-- Overlay degradado elegante -->
        <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/30 to-white/10"></div>

        <!-- Contenido del Hero -->
        <div class="relative z-10 max-w-3xl mx-auto px-6">
            <h1 class="text-5xl md:text-6xl font-extrabold text-white leading-tight drop-shadow-lg">
                Potencia tu Tienda Online al Estilo Shopify
            </h1>

            <p class="mt-6 text-lg md:text-xl text-gray-100 opacity-90 drop-shadow-md">
                Crea una experiencia de compra moderna, rápida y profesional para tus clientes.
            </p>

            <div class="mt-8 flex justify-center gap-4">
                <a href="#"
                    class="px-8 py-3 text-lg font-semibold bg-white text-gray-900 rounded-full shadow-lg hover:shadow-xl hover:-translate-y-1 transition">
                    Empieza Ahora
                </a>

                <a href="#"
                    class="px-8 py-3 text-lg font-semibold border border-white/70 text-white rounded-full hover:bg-white/10 backdrop-blur-md transition">
                    Ver Demo
                </a>
            </div>
        </div>

    </section>





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

     <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- My JS -->
    <script src="./assets/js/index.js"></script>
</body>

</html>