/* ==========================
Variables globales (en español)
========================== */
let storeData = null; // Datos del negocio + productos (desde productos.json)
let carrito = []; // Carrito persistido (localStorage)
let filtroActual = "all";
let todosLosProductos = [];
let productoActualId = null;
let paginaActual = 1;
const productosPorPagina = 10;

/* --------------------------
Utilidades para persistencia
-------------------------- */
function cargarCarritoDesdeStorage() {
  try {
    const guardado = localStorage.getItem("shopping_cart");
    if (guardado) {
      carrito = JSON.parse(guardado);
    } else {
      carrito = [];
    }
  } catch (err) {
    console.error("Error cargando carrito desde localStorage", err);
    carrito = [];
  }
}

function guardarCarritoEnStorage() {
  try {
    localStorage.setItem("shopping_cart", JSON.stringify(carrito));
  } catch (err) {
    console.error("Error guardando carrito en localStorage", err);
  }
}

/* --------------------------
Carga de datos (productos.json)
-------------------------- */
async function loadData() {
  try {
    const response = await fetch("data/productos.json");
    if (!response.ok) throw new Error("No se pudo cargar productos.json");
    storeData = await response.json();

    // Validación mínima
    if (!storeData.negocio || !Array.isArray(storeData.productos)) {
      throw new Error("Estructura JSON inválida");
    }

    todosLosProductos = storeData.productos;

    // Rellenar UI con datos del negocio
    document.getElementById("storeName").textContent =
      storeData.negocio.nombre || "Mi Tienda";
    document.getElementById("footerStoreName").textContent =
      storeData.negocio.nombre || "Mi Tienda";
    document.getElementById("footerCopyright").textContent =
      storeData.negocio.nombre || "Mi Tienda";
    document.getElementById("footerWhatsapp").textContent =
      storeData.negocio.whatsapp || "";
    document.title = storeData.negocio.nombre || "Mi Tienda";

    // WhatsApp flotante
    const whatsappFloat = document.getElementById("whatsappFloat");
    if (storeData.negocio.whatsapp) {
      whatsappFloat.href = `https://wa.me/${storeData.negocio.whatsapp}`;
      whatsappFloat.classList.remove("hidden");
    }

    // Cargar carrito (o restaurarlo)
    cargarCarritoDesdeStorage();

    // Ocultar pantalla de carga
    document.getElementById("loadingScreen").classList.add("hidden");

    // Inicializar vistas
    renderCategories();
    filterProducts(); // renderiza página 1
    updateCart();
  } catch (error) {
    console.error("Error al cargar datos:", error);
    document.getElementById("loadingScreen").classList.add("hidden");
    document.getElementById("errorMessage").classList.remove("hidden");
  }
}

/* ==========================
  Vistas / Navegación
========================== */
function showHome() {
  document.getElementById("homeView").classList.remove("hidden");
  document.getElementById("productDetailView").classList.add("hidden");
  productoActualId = null;
  paginaActual = 1;
  filterProducts();
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function showProductDetail(id) {
  productoActualId = id;
  const producto = todosLosProductos.find((p) => p.id === id);
  if (!producto) return;

  document.getElementById("homeView").classList.add("hidden");
  document.getElementById("productDetailView").classList.remove("hidden");

  const detalle = document.getElementById("productDetailContent");
  const moneda =
    storeData && storeData.negocio && storeData.negocio.moneda
      ? storeData.negocio.moneda
      : "S/";
  detalle.innerHTML = `
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
                        <div>
                            <img src="${producto.imagen}" alt="${escapeHtml(
    producto.nombre
  )}" class="w-full rounded-lg shadow-md" loading="lazy">
                        </div>
                        <div class="flex flex-col justify-center">
                            <div class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold mb-4 w-fit">
                                ${escapeHtml(producto.categoria)}
                            </div>
                            <h1 class="text-3xl md:text-4xl font-bold mb-4">${escapeHtml(
                              producto.nombre
                            )}</h1>
                            <p class="text-gray-600 text-lg mb-6 leading-relaxed">${escapeHtml(
                              producto.descripcion
                            )}</p>
                            <div class="mb-6">
                                <span class="text-4xl font-bold text-blue-600">${moneda} ${Number(
    producto.precio
  ).toFixed(2)}</span>
                            </div>
                            <div class="flex gap-4">
                                <button 
                                    onclick="addToCart(${producto.id})"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg transition text-lg"
                                >
                                    🛒 Agregar al carrito
                                </button>
                                <button 
                                    onclick="buyNow(${producto.id})"
                                    class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-6 rounded-lg transition text-lg"
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

  renderRelatedProducts(producto.categoria, producto.id);
  window.scrollTo({ top: 0, behavior: "smooth" });
}

/* ==========================
  Productos relacionados
========================== */
function renderRelatedProducts(categoria, excluirId) {
  const relacionados = todosLosProductos
    .filter((p) => p.categoria === categoria && p.id !== excluirId)
    .slice(0, 4);
  const cont = document.getElementById("relatedProducts");
  if (!relacionados || relacionados.length === 0) {
    cont.innerHTML =
      '<p class="text-center text-gray-500 col-span-full">No hay productos relacionados</p>';
    return;
  }

  cont.innerHTML = relacionados
    .map(
      (product) => `
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1 cursor-pointer" onclick="showProductDetail(${
                  product.id
                })">
                    <img src="${product.imagen}" alt="${escapeHtml(
        product.nombre
      )}" class="w-full h-48 object-cover" loading="lazy">
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">${escapeHtml(
                          product.nombre
                        )}</h3>
                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">${escapeHtml(
                          product.descripcion
                        )}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-blue-600">${
                              storeData &&
                              storeData.negocio &&
                              storeData.negocio.moneda
                                ? storeData.negocio.moneda
                                : "S/"
                            } ${Number(product.precio).toFixed(2)}</span>
                            <button onclick="event.stopPropagation(); addToCart(${
                              product.id
                            })" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg font-semibold transition text-sm">
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>
            `
    )
    .join("");
}

/* ==========================
 Paginación y filtrado
 ========================== */
function getCategories() {
  if (!todosLosProductos) return ["all"];
  const categorias = [
    "all",
    ...new Set(todosLosProductos.map((p) => p.categoria)),
  ];
  return categorias;
}

function renderCategories() {
  const cont = document.getElementById("categoriesContainer");
  const categorias = getCategories();
  cont.innerHTML = categorias
    .map(
      (cat) => `
                <button 
                    onclick="filterByCategory('${cat}')"
                    class="px-4 py-2 rounded-full font-semibold transition ${
                      filtroActual === cat
                        ? "bg-blue-600 text-white"
                        : "bg-white text-gray-700 hover:bg-gray-100"
                    } shadow-sm"
                >
                    ${cat === "all" ? "🏷️ Todos" : escapeHtml(cat)}
                </button>
            `
    )
    .join("");
}

function filterByCategory(categoria) {
  filtroActual = categoria;
  paginaActual = 1;
  renderCategories();
  filterProducts();
}

function renderPagination(totalProductos) {
  const totalPaginas = Math.ceil(totalProductos / productosPorPagina);
  const cont = document.getElementById("paginationContainer");
  if (totalPaginas <= 1) {
    cont.innerHTML = "";
    return;
  }

  let html = "";

  if (paginaActual > 1) {
    html += `<button onclick="changePage(${
      paginaActual - 1
    })" class="px-4 py-2 bg-white rounded-lg shadow hover:bg-gray-100 transition font-semibold">← Anterior</button>`;
  }

  for (let i = 1; i <= totalPaginas; i++) {
    if (
      i === 1 ||
      i === totalPaginas ||
      (i >= paginaActual - 1 && i <= paginaActual + 1)
    ) {
      html += `
                        <button onclick="changePage(${i})" class="px-4 py-2 rounded-lg shadow font-semibold transition ${
        i === paginaActual
          ? "bg-blue-600 text-white"
          : "bg-white hover:bg-gray-100"
      }">${i}</button>
                    `;
    } else if (i === paginaActual - 2 || i === paginaActual + 2) {
      html += `<span class="px-2">...</span>`;
    }
  }

  if (paginaActual < totalPaginas) {
    html += `<button onclick="changePage(${
      paginaActual + 1
    })" class="px-4 py-2 bg-white rounded-lg shadow hover:bg-gray-100 transition font-semibold">Siguiente →</button>`;
  }

  cont.innerHTML = html;
}

function changePage(pagina) {
  paginaActual = pagina;
  filterProducts();
  window.scrollTo({ top: 0, behavior: "smooth" });
}

/* ==========================
 Render de productos (grilla)
 ========================== */
function renderProducts(productos) {
  const grid = document.getElementById("productsGrid");
  const noResults = document.getElementById("noResults");

  if (!productos || productos.length === 0) {
    grid.classList.add("hidden");
    noResults.classList.remove("hidden");
    document.getElementById("paginationContainer").innerHTML = "";
    return;
  }

  grid.classList.remove("hidden");
  noResults.classList.add("hidden");

  // Paginación local
  const start = (paginaActual - 1) * productosPorPagina;
  const end = start + productosPorPagina;
  const paginados = productos.slice(start, end);

  grid.innerHTML = paginados
    .map(
      (product) => `
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition transform hover:-translate-y-1 cursor-pointer" onclick="showProductDetail(${
                  product.id
                })">
                    <img src="${product.imagen}" alt="${escapeHtml(
        product.nombre
      )}" class="w-full h-48 object-cover" loading="lazy">
                    <div class="p-4">
                        <h3 class="font-bold text-lg mb-2">${escapeHtml(
                          product.nombre
                        )}</h3>
                        <p class="text-gray-600 text-sm mb-3">${escapeHtml(
                          product.descripcion
                        )}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-blue-600">${
                              storeData &&
                              storeData.negocio &&
                              storeData.negocio.moneda
                                ? storeData.negocio.moneda
                                : "S/"
                            } ${Number(product.precio).toFixed(2)}</span>
                            <button onclick="event.stopPropagation(); addToCart(${
                              product.id
                            })" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>
            `
    )
    .join("");

  renderPagination(productos.length);
}

function filterProducts() {
  const searchTerm = (document.getElementById("searchInput").value || "")
    .toLowerCase()
    .trim();
  let filtrados = todosLosProductos || [];

  if (filtroActual !== "all") {
    filtrados = filtrados.filter(
      (p) =>
        String(p.categoria).toLowerCase() === String(filtroActual).toLowerCase()
    );
  }

  if (searchTerm) {
    filtrados = filtrados.filter(
      (p) =>
        String(p.nombre).toLowerCase().includes(searchTerm) ||
        String(p.descripcion).toLowerCase().includes(searchTerm) ||
        String(p.categoria).toLowerCase().includes(searchTerm)
    );
  }

  renderProducts(filtrados);
}

/* ==========================
 Carrito: agregar, actualizar, renderizar
 ========================== */
function addToCart(productId) {
  const producto = todosLosProductos.find((p) => p.id === productId);
  if (!producto) return;

  const existente = carrito.find((item) => item.id === productId);
  if (existente) {
    existente.quantity = (existente.quantity || 1) + 1;
  } else {
    // Guardamos únicamente la info necesaria para el carrito
    carrito.push({
      id: producto.id,
      nombre: producto.nombre,
      precio: Number(producto.precio),
      imagen: producto.imagen,
      quantity: 1,
    });
  }

  guardarCarritoEnStorage();
  updateCart();
}

function changeQuantity(productId, cambio) {
  const item = carrito.find((i) => i.id === productId);
  if (!item) return;
  item.quantity += cambio;
  if (item.quantity <= 0) {
    removeFromCart(productId);
  } else {
    guardarCarritoEnStorage();
    updateCart();
  }
}

function removeFromCart(productId) {
  carrito = carrito.filter((i) => i.id !== productId);
  guardarCarritoEnStorage();
  updateCart();
}

function clearCart() {
  if (!confirm("¿Estás seguro de vaciar el carrito?")) return;
  carrito = [];
  guardarCarritoEnStorage();
  updateCart();
}

function toggleCart() {
  const modal = document.getElementById("cartModal");
  modal.classList.toggle("hidden");
}

function updateCart() {
  // Actualiza contador y render del carrito
  const cartCount = document.getElementById("cartCount");
  const totalItems = carrito.reduce((s, it) => s + (it.quantity || 0), 0);
  cartCount.textContent = totalItems;

  if (totalItems > 0) {
    cartCount.classList.add("cart-badge");
    setTimeout(() => cartCount.classList.remove("cart-badge"), 300);
  }

  renderCart();
}

function renderCart() {
  const cont = document.getElementById("cartItems");
  const cartTotalEl = document.getElementById("cartTotal");
  const moneda =
    storeData && storeData.negocio && storeData.negocio.moneda
      ? storeData.negocio.moneda
      : "S/";

  if (!carrito || carrito.length === 0) {
    cont.innerHTML =
      '<p class="text-center text-gray-500 py-8">Tu carrito está vacío</p>';
    cartTotalEl.textContent = `${moneda} 0.00`;
    return;
  }

  const total = carrito.reduce(
    (s, it) => s + Number(it.precio) * (it.quantity || 0),
    0
  );

  cont.innerHTML = carrito
    .map(
      (item) => `
                <div class="flex items-center gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                    <img src="${item.imagen}" alt="${escapeHtml(
        item.nombre
      )}" class="w-20 h-20 object-cover rounded" loading="lazy">
                    <div class="flex-1">
                        <h4 class="font-bold">${escapeHtml(item.nombre)}</h4>
                        <p class="text-gray-600">${moneda} ${Number(
        item.precio
      ).toFixed(2)}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="changeQuantity(${
                          item.id
                        }, -1)" class="bg-gray-200 hover:bg-gray-300 w-8 h-8 rounded-full flex items-center justify-center">-</button>
                        <span class="w-8 text-center font-bold">${
                          item.quantity
                        }</span>
                        <button onclick="changeQuantity(${
                          item.id
                        }, 1)" class="bg-blue-600 hover:bg-blue-700 text-white w-8 h-8 rounded-full flex items-center justify-center">+</button>
                    </div>
                    <div class="pl-4">
                        <button onclick="removeFromCart(${
                          item.id
                        })" class="text-red-500 hover:text-red-700 font-bold">🗑️</button>
                    </div>
                </div>
            `
    )
    .join("");

  cartTotalEl.textContent = `${moneda} ${total.toFixed(2)}`;
}

/* ==========================
 Enviar pedido por WhatsApp
 ========================== */
function sendToWhatsApp() {
  if (!carrito || carrito.length === 0) {
    alert("Tu carrito está vacío");
    return;
  }
  if (!storeData || !storeData.negocio || !storeData.negocio.whatsapp) {
    alert("Número de WhatsApp del negocio no configurado.");
    return;
  }

  let mensaje = `🛒 *Nuevo Pedido - ${storeData.negocio.nombre}*\n\n`;
  carrito.forEach((item) => {
    mensaje += `📦 *${item.nombre}*\n`;
    mensaje += `   Cantidad: ${item.quantity}\n`;
    mensaje += `   Precio unitario: ${storeData.negocio.moneda} ${Number(
      item.precio
    ).toFixed(2)}\n`;
    mensaje += `   Subtotal: ${storeData.negocio.moneda} ${(
      Number(item.precio) * item.quantity
    ).toFixed(2)}\n\n`;
  });
  const total = carrito.reduce(
    (s, it) => s + Number(it.precio) * it.quantity,
    0
  );
  mensaje += `💰 *Total: ${storeData.negocio.moneda} ${total.toFixed(2)}*`;

  const encoded = encodeURIComponent(mensaje);
  const whatsappUrl = `https://wa.me/${storeData.negocio.whatsapp}?text=${encoded}`;
  window.open(whatsappUrl, "_blank");
}

/* ==========================
 Compra rápida (Agregar + enviar)
 ========================== */
function buyNow(productId) {
  addToCart(productId);
  // esperar un tic para asegurar que se guardó el carrito y el total quedó actualizado.
  setTimeout(() => {
    sendToWhatsApp();
  }, 150);
}

/* ==========================
 Helper: escapar texto (para inyección mínima)
 ========================== */
function escapeHtml(text) {
  if (text === null || text === undefined) return "";
  return String(text)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#39;");
}

/* ==========================
 Inicialización
 ========================== */
// Evitar que algunas funciones usen storeData antes de tiempo.
document.addEventListener("DOMContentLoaded", () => {
  // Carga inicial del carrito desde storage (para contar icono aunque storeData no exista aún)
  cargarCarritoDesdeStorage();
  updateCart();
  // Arranca la carga de datos
  loadData();
});
