
let productData = null;
let cart = [];

// Get product ID from URL
const urlParams = new URLSearchParams(window.location.search);
const productId = urlParams.get('id');

if (!productId) {
    showError();
}

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

        // Update page
        document.title = `${productData.producto.nombre} - ${productData.negocio.nombre}`;
        document.getElementById('storeName').textContent = productData.negocio.nombre;
        document.getElementById('footerStoreName').textContent = productData.negocio.nombre;
        document.getElementById('footerCopyright').textContent = productData.negocio.nombre;
        document.getElementById('footerWhatsapp').textContent = productData.negocio.whatsapp;

        // Email and address
        const footerEmail = document.getElementById('footerEmail');
        const footerDireccion = document.getElementById('footerDireccion');

        if (productData.negocio.email && productData.negocio.email !== '') {
            footerEmail.textContent = '📧 ' + productData.negocio.email;
            footerEmail.style.display = 'block';
        } else {
            footerEmail.style.display = 'none';
        }

        if (productData.negocio.direccion && productData.negocio.direccion !== '') {
            footerDireccion.textContent = '📍 ' + productData.negocio.direccion;
            footerDireccion.style.display = 'block';
        } else {
            footerDireccion.style.display = 'none';
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

        // Hide loading
        document.getElementById('loadingScreen').classList.add('hidden');

        // Load cart
        loadCartFromStorage();

    } catch (error) {
        console.error('Error:', error);
        showError();
    }
}

function renderBreadcrumb() {
    const categoryEl = document.getElementById('breadcrumbCategory');
    const productEl = document.getElementById('breadcrumbProduct');

    if (productData.producto.categoria) {
        categoryEl.textContent = productData.producto.categoria;
        categoryEl.onclick = () => window.location.href = `index.php?categoria=${productData.producto.categoria}`;
    } else {
        categoryEl.textContent = 'Productos';
        categoryEl.onclick = () => window.location.href = 'index.php';
    }

    productEl.textContent = productData.producto.nombre;
}

function renderProduct() {
    const product = productData.producto;
    const container = document.getElementById('productContent');

    container.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
                    <div>
                        <img src="${product.imagen}" alt="${product.nombre}" class="w-full rounded-lg shadow-md">
                    </div>
                    <div class="flex flex-col justify-center">
                        ${product.categoria ? `<div class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold mb-4 w-fit">
                            ${product.categoria}
                        </div>` : ''}
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
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg transition text-lg ${product.stock === 0 ? 'opacity-50 cursor-not-allowed' : ''}"
                                ${product.stock === 0 ? 'disabled' : ''}
                            >
                                ${product.stock === 0 ? '⛔ Agotado' : '🛒 Agregar al carrito'}
                            </button>
                            <button 
                                onclick="buyNow(${product.id})"
                                class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-6 rounded-lg transition text-lg ${product.stock === 0 ? 'opacity-50 cursor-not-allowed' : ''}"
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
            `;
}

function renderNavigation() {
    const container = document.getElementById('productNavigation');
    const nav = productData.navegacion;
    let html = '';

    if (nav.anterior) {
        html += `<a href="producto.php?id=${nav.anterior.id}" class="flex items-center gap-2 bg-white hover:bg-gray-50 px-6 py-3 rounded-lg shadow-md transition">
                    <span class="text-2xl">←</span>
                    <div class="text-left">
                        <p class="text-xs text-gray-500">Anterior</p>
                        <p class="font-semibold text-gray-900">${nav.anterior.nombre}</p>
                    </div>
                </a>`;
    } else {
        html += '<div></div>';
    }

    if (nav.siguiente) {
        html += `<a href="producto.php?id=${nav.siguiente.id}" class="flex items-center gap-2 bg-white hover:bg-gray-50 px-6 py-3 rounded-lg shadow-md transition">
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Siguiente</p>
                        <p class="font-semibold text-gray-900">${nav.siguiente.nombre}</p>
                    </div>
                    <span class="text-2xl">→</span>
                </a>`;
    }

    container.innerHTML = html;
}

function renderRelatedProducts() {
    const container = document.getElementById('relatedProducts');
    const related = productData.relacionados;

    if (related.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-500 col-span-full">No hay productos relacionados</p>';
        return;
    }

    container.innerHTML = related.map(product => `
                <a href="producto.php?id=${product.id}" class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1">
                    <img src="${product.imagen}" alt="${product.nombre}" class="w-full h-48 object-cover" loading="lazy">
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">${product.nombre}</h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">${product.descripcion}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-bold text-blue-600">${productData.negocio.moneda} ${product.precio.toFixed(2)}</span>
                            <button 
                                onclick="event.preventDefault(); event.stopPropagation(); addToCart(${product.id})"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg font-semibold transition text-sm"
                            >
                                Agregar
                            </button>
                        </div>
                    </div>
                </a>
            `).join('');
}

function renderSocialMedia() {
    const container = document.getElementById('socialMediaLinks');
    if (!container || !productData || !productData.negocio) {
        return;
    }

    const social = productData.negocio;
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

function addToCart(productId) {
    const product = productData.producto;

    if (product.stock === 0) {
        alert('Este producto está agotado');
        return;
    }

    const existingItem = cart.find(item => item.id === productId);

    if (existingItem) {
        existingItem.quantity++;
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

    // Show feedback
    alert('✅ Producto agregado al carrito');
}

function buyNow(productId) {
    addToCart(productId);
    sendToWhatsApp();
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
        cartTotal.textContent = `${productData.negocio.moneda} 0.00`;
        return;
    }

    const total = cart.reduce((sum, item) => sum + (item.precio * item.quantity), 0);

    cartItems.innerHTML = cart.map(item => `
                <div class="flex items-center gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                    <img src="${item.imagen}" alt="${item.nombre}" class="w-20 h-20 object-cover rounded" loading="lazy">
                    <div class="flex-1">
                        <h4 class="font-bold">${item.nombre}</h4>
                        <p class="text-gray-600">${productData.negocio.moneda} ${item.precio.toFixed(2)}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="changeQuantity(${item.id}, -1)" class="bg-gray-300 hover:bg-gray-400 w-8 h-8 rounded-full font-bold">-</button>
                        <span class="w-8 text-center font-bold">${item.quantity}</span>
                        <button onclick="changeQuantity(${item.id}, 1)" class="bg-blue-600 hover:bg-blue-700 text-white w-8 h-8 rounded-full font-bold">+</button>
                    </div>
                    <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700 font-bold">🗑️</button>
                </div>
            `).join('');

    cartTotal.textContent = `${productData.negocio.moneda} ${total.toFixed(2)}`;
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

    let message = `🛒 *Nuevo Pedido - ${productData.negocio.nombre}*\n\n`;

    cart.forEach(item => {
        message += `📦 *${item.nombre}*\n`;
        message += `   Cantidad: ${item.quantity}\n`;
        message += `   Precio unitario: ${productData.negocio.moneda} ${item.precio.toFixed(2)}\n`;
        message += `   Subtotal: ${productData.negocio.moneda} ${(item.precio * item.quantity).toFixed(2)}\n\n`;
    });

    const total = cart.reduce((sum, item) => sum + (item.precio * item.quantity), 0);
    message += `💰 *Total: ${productData.negocio.moneda} ${total.toFixed(2)}*`;

    const encodedMessage = encodeURIComponent(message);
    const whatsappUrl = `https://wa.me/${productData.negocio.whatsapp}?text=${encodedMessage}`;

    window.open(whatsappUrl, '_blank');
}

function showError() {
    document.getElementById('loadingScreen').classList.add('hidden');
    document.getElementById('errorMessage').classList.remove('hidden');
}

// Initialize
loadProduct();
