function compartirTienda() {
    const enlaceTienda = window.location.origin + "/index.php"; // Ruta base + página principal
    if (navigator.share) {
        navigator.share({
            title: "Mi Tienda Online",
            text: "¡Mira mi tienda virtual!",
            url: enlaceTienda
        }).catch(() => {
            // Si el usuario cancela el compartir
            console.log("Compartir cancelado");
        });
    } else {
        navigator.clipboard.writeText(enlaceTienda).then(() => {
            alert("📋 Enlace copiado al portapapeles:\n" + enlaceTienda);
        }).catch(() => {
            alert("❌ No se pudo copiar el enlace. Intenta manualmente.");
        });
    }
}