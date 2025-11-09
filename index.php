<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Online</title>
    <link rel="stylesheet" href="./assets/output.css">
    <link rel="stylesheet" href="./assets/css/index.css">
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
                    <!-- Nombre desde el dashboard -->
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

    <script src="./assets/js/index.js"></script>
</body>

</html>