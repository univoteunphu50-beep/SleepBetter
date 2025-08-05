
CREATE DATABASE IF NOT EXISTS sleepbetter_db;
USE sleepbetter_db;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    rol ENUM('admin', 'vendedor', 'usuario') DEFAULT 'usuario',
    activo BOOLEAN DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    cedula VARCHAR(13) PRIMARY KEY,
    cliente VARCHAR(100) NOT NULL,
    telefono VARCHAR(15),
    email VARCHAR(100),
    direccion VARCHAR(255),
    comentarios TEXT
);

-- Tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    stock INT DEFAULT 0,
    restock INT DEFAULT 0,
    precio DECIMAL(10,2) NOT NULL,
    costo DECIMAL(10,2),
    comentarios TEXT,
    imagen VARCHAR(255),
    palabras_clave TEXT,
    con_lote BOOLEAN DEFAULT 0,
    numero_lote VARCHAR(50),
    fecha_fabricacion DATE,
    fecha_expiracion DATE,
    temperatura_almacenamiento VARCHAR(50),
    condiciones_especiales TEXT,
    tiene_lote BOOLEAN DEFAULT 0,
    lote VARCHAR(50),
    expiracion DATE
);

-- Tabla de facturas
CREATE TABLE IF NOT EXISTS facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula_cliente VARCHAR(13),
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    subtotal DECIMAL(10,2),
    itbis DECIMAL(10,2),
    total DECIMAL(10,2),
    FOREIGN KEY (cedula_cliente) REFERENCES clientes(cedula)
);

-- Tabla de detalle de factura
CREATE TABLE IF NOT EXISTS detalle_factura (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_factura INT,
    id_producto INT,
    nombre VARCHAR(100),
    precio DECIMAL(10,2),
    itebis BOOLEAN DEFAULT 0,
    descuento DECIMAL(5,2) DEFAULT 0,
    cantidad INT,
    precio_unitario DECIMAL(10,2),
    total DECIMAL(10,2),
    FOREIGN KEY (id_factura) REFERENCES facturas(id),
    FOREIGN KEY (id_producto) REFERENCES productos(id)
);

-- Tabla de movimientos de stock
CREATE TABLE IF NOT EXISTS movimientos_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    tipo ENUM('venta', 'compra', 'ajuste', 'devolucion', 'merma', 'restock') NOT NULL,
    cantidad INT NOT NULL,
    stock_anterior INT NOT NULL,
    stock_nuevo INT NOT NULL,
    motivo VARCHAR(255),
    usuario VARCHAR(100),
    id_factura INT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES productos(id),
    FOREIGN KEY (id_factura) REFERENCES facturas(id)
);

-- Tabla de alertas de restock
CREATE TABLE IF NOT EXISTS alertas_restock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    stock_actual INT NOT NULL,
    stock_restock INT NOT NULL,
    estado ENUM('activa', 'resuelta', 'ignorada') DEFAULT 'activa',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_resolucion TIMESTAMP NULL,
    usuario_resolucion VARCHAR(100) NULL,
    FOREIGN KEY (id_producto) REFERENCES productos(id)
);

-- Tabla de actividades
CREATE TABLE IF NOT EXISTS actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    nombre_usuario VARCHAR(100) NOT NULL,
    accion VARCHAR(50) NOT NULL,
    modulo VARCHAR(50) NOT NULL,
    descripcion TEXT,
    datos_anteriores JSON NULL,
    datos_nuevos JSON NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- Insertar usuario administrador por defecto
INSERT INTO usuarios (usuario, password, nombre, email, rol) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin@sleepbetter.com', 'admin')
ON DUPLICATE KEY UPDATE id=id;

-- Crear índices para optimizar consultas
CREATE INDEX idx_movimientos_stock_producto ON movimientos_stock(id_producto);
CREATE INDEX idx_movimientos_stock_fecha ON movimientos_stock(fecha);
CREATE INDEX idx_alertas_restock_producto ON alertas_restock(id_producto);
CREATE INDEX idx_alertas_restock_estado ON alertas_restock(estado);
CREATE INDEX idx_actividades_usuario ON actividades(id_usuario);
CREATE INDEX idx_actividades_fecha ON actividades(fecha);
CREATE INDEX idx_actividades_modulo ON actividades(modulo);
