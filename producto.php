<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargando producto...</title>
    <link rel="stylesheet" href="./assets/output.css">
    <!-- <script src="https://cdn.tailwindcss.com"></script> -->
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="./assets/css/producto.css">

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
            <p class="mt-4 text-gray-600">Cargando producto ...</p>
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

    <!-- Breadcrumb Mejorado -->
    <div class="w-full bg-white/90 backdrop-blur-md border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 py-4">

            <nav class="flex items-center gap-2 text-sm font-medium text-gray-600">

                <!-- Inicio -->
                <a href="index.php" class="flex items-center gap-1 text-gray-700 hover:text-indigo-600 transition">
                    <i class="fa-solid fa-house text-indigo-500"></i>
                    <span>Inicio</span>
                </a>

                <!-- Separador -->
                <i class="fa-solid fa-chevron-right text-gray-400 text-xs"></i>

                <!-- Categoría -->
                <span id="breadcrumbCategory"
                    class="flex items-center gap-1 text-gray-700 hover:text-indigo-600 transition cursor-pointer">
                    <i class="fa-solid fa-folder-open text-yellow-500"></i>
                    <span></span>
                </span>

                <!-- Separador -->
                <i class="fa-solid fa-chevron-right text-gray-400 text-xs"></i>

                <!-- Producto -->
                <span id="breadcrumbProduct" class="flex items-center gap-1 text-gray-900 font-semibold">
                    <i class="fa-solid fa-box-open text-indigo-600"></i>
                    <span></span>
                </span>

            </nav>

        </div>
    </div>


    <!-- PRODUCT DETAIL WRAPPER -->
    <div class="max-w-6xl mx-auto px-4 py-10">

        <!-- Product detail box -->
        <div id="productContent" class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 
               transition-all duration-300 hover:shadow-2xl mb-10">
        </div>

        <!-- Navigation Between Products -->
        <div id="productNavigation" class="flex justify-between items-center mb-12 px-2">
        </div>

        <!-- BENEFIT BANNER -->
        <div class="bg-gradient-to-br from-white to-gray-50 py-14 rounded-3xl shadow-lg border border-gray-100 mb-12">
            <div class="max-w-5xl mx-auto px-4">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10 text-center">

                    <!-- Item -->
                    <div class="group flex flex-col items-center">
                        <div class="w-20 h-20 bg-indigo-100 rounded-2xl flex items-center justify-center 
                                mb-4 shadow-md group-hover:shadow-lg transition">
                            <i class="fa-solid fa-truck-fast text-3xl text-indigo-600"></i>
                        </div>
                        <h3 class="font-bold text-xl text-gray-800 mb-1">Envío Rápido</h3>
                        <p class="text-gray-500">Entregas en 24-48 horas a nivel nacional</p>
                    </div>

                    <!-- Item -->
                    <div class="group flex flex-col items-center">
                        <div class="w-20 h-20 bg-green-100 rounded-2xl flex items-center justify-center 
                                mb-4 shadow-md group-hover:shadow-lg transition">
                            <i class="fa-solid fa-shield-halved text-3xl text-green-600"></i>
                        </div>
                        <h3 class="font-bold text-xl text-gray-800 mb-1">Pagos Seguros</h3>
                        <p class="text-gray-500">Tus transacciones protegidas en todo momento</p>
                    </div>

                    <!-- Item -->
                    <div class="group flex flex-col items-center">
                        <div class="w-20 h-20 bg-purple-100 rounded-2xl flex items-center justify-center 
                                mb-4 shadow-md group-hover:shadow-lg transition">
                            <i class="fa-solid fa-headset text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="font-bold text-xl text-gray-800 mb-1">Soporte 24/7</h3>
                        <p class="text-gray-500">Atención personalizada por WhatsApp</p>
                    </div>

                </div>

            </div>
        </div>

        <!-- RELATED PRODUCTS -->
        <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-10">
            <h2 class="text-2xl md:text-3xl font-extrabold mb-10 text-center text-gray-800 tracking-tight">
                Productos Relacionados
            </h2>

            <div id="relatedProducts" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            </div>
        </div>
    </div>


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

    <!-- Error Message -->
    <div id="errorMessage"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center">
            <div class="text-6xl mb-4">😞</div>
            <h2 class="text-2xl font-bold mb-4">Producto no encontrado</h2>
            <p class="text-gray-600 mb-6">El producto que buscas no existe o ya no está disponible.</p>
            <a href="index.php"
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition">
                Volver a la tienda
            </a>
        </div>
    </div>

    <script src="./assets/js/producto.js?v=<?= time() ?>"></script>
</body>

</html>