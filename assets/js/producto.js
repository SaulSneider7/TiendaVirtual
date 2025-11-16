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

let productData = null;
let cart = [];

// Obtener ID del producto desde la URL
const urlParams = new URLSearchParams(window.location.search);
const productId = urlParams.get('id');

if (!productId) {
    showError();
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
    LocalStorage - Cargar y guardar carrito
   ======================== */
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
    Cargar producto desde API
   ======================== */
async function loadProduct() {
    try {
        const response = await fetch(`api/get_producto.php?id=${productId}`);
        if (!response.ok) {
            throw new Error('Error al cargar el producto');
        }

        productData = await response.json();

        if (productData.error) {
            throw new Error(productData.error);
        }

        // Page info
        document.title = `${productData.producto.nombre} - ${productData.negocio.nombre}`;
        document.getElementById('storeName').textContent = productData.negocio.nombre;
        document.getElementById('footerStoreName').textContent = productData.negocio.nombre;
        document.getElementById('footerCopyright').textContent = productData.negocio.nombre;
        document.getElementById('footerWhatsapp').textContent = productData.negocio.whatsapp;

        // Email y dirección
        const footerEmail = document.getElementById('footerEmail');
        const footerDireccion = document.getElementById('footerDireccion');

        footerEmail.style.display = productData.negocio.email ? 'block' : 'none';
        footerDireccion.style.display = productData.negocio.direccion ? 'block' : 'none';

        if (productData.negocio.email) {
            footerEmail.textContent = '📧 ' + productData.negocio.email;
        }
        if (productData.negocio.direccion) {
            footerDireccion.textContent = '📍 ' + productData.negocio.direccion;
        }

        // WhatsApp button
        const whatsappFloat = document.getElementById('whatsappFloat');
        whatsappFloat.href = `https://wa.me/${productData.negocio.whatsapp}`;
        whatsappFloat.classList.remove('hidden');

        // Render
        renderBreadcrumb();
        renderProduct();
        renderNavigation();
        renderRelatedProducts();
        renderSocialMedia();

        document.getElementById('loadingScreen').classList.add('hidden');

        loadCartFromStorage();

    } catch (error) {
        console.error('Error:', error);
        showError();
    }
}

/* ========================
    Breadcrumb
   ======================== */
function renderBreadcrumb() {
    const cat = document.getElementById('breadcrumbCategory');
    const prod = document.getElementById('breadcrumbProduct');

    if (productData.producto.categoria) {
        cat.textContent = productData.producto.categoria;
        cat.onclick = () => window.location.href = `index.php?categoria=${productData.producto.categoria}`;
    } else {
        cat.textContent = 'Productos';
        cat.onclick = () => window.location.href = 'index.php';
    }

    prod.textContent = productData.producto.nombre;
}

/* ========================
    Render del producto
   ======================== */
function renderProduct() {
    const product = productData.producto;
    const c = document.getElementById('productContent');

    c.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
            <div>
                <img src="${product.imagen}" alt="${product.nombre}" class="w-full rounded-lg shadow-md">
            </div>
            <div class="flex flex-col justify-center">

                ${product.categoria ? `<div class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold mb-4 w-fit">${product.categoria}</div>` : ''}

                <h1 class="text-3xl md:text-4xl font-bold mb-4">${product.nombre}</h1>
                <p class="text-gray-600 text-lg mb-6 leading-relaxed">${product.descripcion}</p>

                <div class="mb-6">
                    <span class="text-4xl font-bold text-blue-600">${productData.negocio.moneda} ${product.precio.toFixed(2)}</span>
                    ${product.stock < 5 && product.stock > 0 ? `<p class="text-orange-600 font-semibold mt-2">⚠️ Solo quedan ${product.stock} unidades</p>` : ''}
                    ${product.stock === 0 ? `<p class="text-red-600 font-bold mt-2">⛔ Producto agotado</p>` : ''}
                </div>

                <div class="flex gap-4">
                    <button 
                        onclick="addToCart(${product.id})"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-4 px-6 rounded-lg text-lg ${product.stock === 0 ? 'opacity-50 cursor-not-allowed' : ''}"
                        ${product.stock === 0 ? 'disabled' : ''}>🛒 Agregar al carrito</button>

                    <button 
                        onclick="buyNow(${product.id})"
                        class="flex-1 bg-green-500 hover:bg-green-600 text-white py-4 px-6 rounded-lg text-lg ${product.stock === 0 ? 'opacity-50 cursor-not-allowed' : ''}"
                        ${product.stock === 0 ? 'disabled' : ''}>📱 Comprar ahora</button>
                </div>
            </div>
        </div>`;
}

/* ========================
    Navegación productos
   ======================== */
function renderNavigation() {
    const nav = productData.navegacion;
    const c = document.getElementById('productNavigation');

    let html = '';

    if (nav.anterior) {
        html += `
            <a href="producto.php?id=${nav.anterior.id}" class="flex items-center gap-2 bg-white px-6 py-3 rounded-lg shadow-md">
                <span class="text-2xl">←</span>
                <div>
                    <p class="text-xs text-gray-500">Anterior</p>
                    <p class="font-semibold">${nav.anterior.nombre}</p>
                </div>
            </a>`;
    } else {
        html += '<div></div>';
    }

    if (nav.siguiente) {
        html += `
            <a href="producto.php?id=${nav.siguiente.id}" class="flex items-center gap-2 bg-white px-6 py-3 rounded-lg shadow-md">
                <div class="text-right">
                    <p class="text-xs text-gray-500">Siguiente</p>
                    <p class="font-semibold">${nav.siguiente.nombre}</p>
                </div>
                <span class="text-2xl">→</span>
            </a>`;
    }

    c.innerHTML = html;
}

/* ========================
    Productos relacionados
   ======================== */
function renderRelatedProducts() {
    const related = productData.relacionados;
    const c = document.getElementById('relatedProducts');

    if (related.length === 0) {
        c.innerHTML = `<p class="text-center text-gray-500 col-span-full py-6 text-lg">No hay productos relacionados</p>`;
        return;
    }

    c.innerHTML = related
        .map(p => `
        <a href="producto.php?id=${p.id}" class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition relative">
            <div class="aspect-square bg-gray-100 overflow-hidden">
                <img src="${p.imagen}" class="w-full h-full object-cover group-hover:scale-110 transition">
            </div>

            <div class="p-4">
                <h3 class="font-bold text-lg group-hover:text-indigo-600 transition">
                    ${p.nombre}
                </h3>

                <p class="text-gray-500 text-sm mt-1 mb-3 line-clamp-2">${p.descripcion}</p>

                <div class="flex items-center justify-between mt-2">
                    <span class="text-xl font-bold text-indigo-600">${productData.negocio.moneda} ${p.precio.toFixed(2)}</span>

                    <button onclick="event.preventDefault(); addToCart(${p.id})"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg text-sm">
                        <i class="fa-solid fa-cart-plus"></i>
                    </button>
                </div>
            </div>
        </a>`
        ).join('');
}

/* ========================
    Redes sociales (circular premium)
   ======================== */
function renderSocialMedia() {
    const social = productData.negocio;
    const c = document.getElementById('socialMediaLinks');

    if (!c) return;

    let html = '';

    const baseClasses = `
        w-12 h-12 flex items-center justify-center 
        rounded-full text-white text-xl
        shadow-md hover:shadow-xl
        transition-transform duration-200
        hover:-translate-y-1
    `;

    if (social.facebook)
        html += `
            <a href="${social.facebook}" target="_blank"
                class="${baseClasses} bg-blue-600 hover:bg-blue-700">
                <i class="fab fa-facebook-f"></i>
            </a>
        `;

    if (social.instagram)
        html += `
            <a href="${social.instagram}" target="_blank"
                class="${baseClasses} bg-gradient-to-tr from-pink-500 to-purple-600 hover:opacity-90">
                <i class="fab fa-instagram"></i>
            </a>
        `;

    if (social.twitter)
        html += `
            <a href="${social.twitter}" target="_blank"
                class="${baseClasses} bg-sky-500 hover:bg-sky-600">
                <i class="fab fa-twitter"></i>
            </a>
        `;

    if (social.tiktok)
        html += `
            <a href="${social.tiktok}" target="_blank"
                class="${baseClasses} bg-black hover:bg-gray-800">
                <i class="fab fa-tiktok"></i>
            </a>
        `;

    c.innerHTML = html || `<p class="text-gray-400">Síguenos en redes sociales</p>`;
}


/* ========================
    Carrito
   ======================== */
function addToCart(id) {
    const p = productData.producto;

    if (p.stock === 0) {
        Swal.fire({
            icon: 'info',
            title: 'Agotado',
            text: 'Este producto está agotado.'
        });
        return;
    }

    const exists = cart.find(i => i.id === id);

    if (exists) {
        exists.quantity++;
    } else {
        cart.push({
            id: p.id,
            nombre: p.nombre,
            precio: p.precio,
            imagen: p.imagen,
            quantity: 1
        });
    }

    saveCartToStorage();
    updateCart();
    Toast.fire({
        icon: 'success',
        title: 'Añadido al carrito',
        text: p.nombre
    });
}

function buyNow(id) {
    addToCart(id);
    sendToWhatsApp();
}

function updateCart() {
    const count = document.getElementById('cartCount');
    const totalItems = cart.reduce((s, i) => s + i.quantity, 0);
    count.textContent = totalItems;

    renderCart();
}

function renderCart() {
    const list = document.getElementById('cartItems');
    const totalEl = document.getElementById('cartTotal');

    if (cart.length === 0) {
        list.innerHTML = `
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
                </div >
        `;
        totalEl.textContent = `${productData.negocio.moneda} 0.00`;
        return;
    }

    const total = cart.reduce((s, i) => s + i.precio * i.quantity, 0);

    list.innerHTML = cart.map(i => `
        <div class="flex items-center gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
            <img src="${i.imagen}" class="w-20 h-20 rounded">

            <div class="flex-1">
                <h4 class="font-bold">${i.nombre}</h4>
                <p>${productData.negocio.moneda} ${i.precio.toFixed(2)}</p>
            </div>

            <div class="flex items-center gap-2">
                <button onclick="changeQuantity(${i.id}, -1)" class="w-8 h-8 bg-gray-300 rounded-full">-</button>
                <span class="w-8 text-center font-bold">${i.quantity}</span>
                <button onclick="changeQuantity(${i.id}, 1)" class="w-8 h-8 bg-blue-600 text-white rounded-full">+</button>
            </div>

            <button onclick="removeFromCart(${i.id})" class="text-red-600 font-bold">🗑️</button>
        </div>
    `).join('');

    totalEl.textContent = `${productData.negocio.moneda} ${total.toFixed(2)}`;
}

function changeQuantity(id, change) {
    const item = cart.find(i => i.id === id);
    if (!item) return;

    item.quantity += change;

    if (item.quantity <= 0) {
        removeFromCart(id);
    } else {
        saveCartToStorage();
        updateCart();
    }
}

function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);
    saveCartToStorage();
    updateCart();
}

function toggleCart() {
    document.getElementById('cartModal').classList.toggle('hidden');
}

/* ========================
    WhatsApp - Enviar pedido
   ======================== */
function sendToWhatsApp() {
    if (cart.length === 0) {
        alert('Tu carrito está vacío');
        return;
    }

    let message = `🛒 *Nuevo pedido*\n\n`;

    cart.forEach(i => {
        message += `• ${i.nombre} (${i.quantity}) - ${productData.negocio.moneda} ${i.precio.toFixed(2)}\n`;
    });

    const total = cart.reduce((s, i) => s + i.precio * i.quantity, 0);
    message += `\nTotal: *${productData.negocio.moneda} ${total.toFixed(2)}*`;

    const url = `https://wa.me/${productData.negocio.whatsapp}?text=${encodeURIComponent(message)}`;

    window.open(url, '_blank');
}

/* ========================
    Error
   ======================== */
function showError() {
    document.body.innerHTML = `
        <div class="w-full h-screen flex items-center justify-center">
            <h1 class="text-2xl font-bold text-red-500">❌ Error: Producto no encontrado</h1>
        </div>
    `;
}

/* ========================
    Iniciar al cargar
   ======================== */
document.addEventListener('DOMContentLoaded', () => {
    loadProduct();
});
