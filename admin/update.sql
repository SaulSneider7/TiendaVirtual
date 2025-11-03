-- Agregar campos de redes sociales a la tabla configuracion
ALTER TABLE configuracion
ADD COLUMN facebook VARCHAR(255) DEFAULT NULL,
ADD COLUMN instagram VARCHAR(255) DEFAULT NULL,
ADD COLUMN twitter VARCHAR(255) DEFAULT NULL,
ADD COLUMN tiktok VARCHAR(255) DEFAULT NULL,
ADD COLUMN email VARCHAR(255) DEFAULT NULL,
ADD COLUMN direccion TEXT DEFAULT NULL;

-- Actualizar con valores de ejemplo (puedes cambiarlos después)
UPDATE configuracion
SET
    facebook = 'https://facebook.com/tutienda',
    instagram = 'https://instagram.com/tutienda',
    email = 'contacto@tutienda.com'
WHERE
    id = 1;

-- Nota: Ejecuta este SQL solo UNA VEZ en phpMyAdmin