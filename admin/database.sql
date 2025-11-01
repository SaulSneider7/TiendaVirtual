-- Crear base de datos
CREATE DATABASE IF NOT EXISTS tienda_online CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE tienda_online;

-- Tabla de configuración del negocio
CREATE TABLE IF NOT EXISTS configuracion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_tienda VARCHAR(255) NOT NULL,
    whatsapp VARCHAR(20) NOT NULL,
    moneda VARCHAR(10) DEFAULT 'S/',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertar configuración inicial
INSERT INTO
    configuracion (
        nombre_tienda,
        whatsapp,
        moneda
    )
VALUES (
        'Mi Tienda Online',
        '51987654321',
        'S/'
    );

-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar categorías de ejemplo
INSERT INTO
    categorias (nombre)
VALUES ('Electrónica'),
    ('Hogar'),
    ('Accesorios'),
    ('Ropa'),
    ('Deportes');

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    imagen VARCHAR(500) NOT NULL,
    categoria_id INT NOT NULL,
    stock INT DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias (id) ON DELETE CASCADE
);

-- Tabla de usuarios administradores
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear usuario admin (password: admin123)
INSERT INTO
    admin_users (username, password, email)
VALUES (
        'admin',
        '$2y$10$1WYMGgOkE2AVgLPbpj3vFOSVttcVMTQ1AcAblv13Mc4cXEnW7LAia',
        'admin@tienda.com'
    );

-- NOTA: Cambia la contraseña después de instalar ejecutando:
-- UPDATE admin_users SET password = PASSWORD('tu_nueva_contraseña') WHERE username = 'admin';