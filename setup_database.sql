-- Script para configurar la base de datos PostgreSQL en Render
-- Ejecuta este script en tu base de datos PostgreSQL

-- Crear tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(20) DEFAULT 'usuario',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla de clientes
CREATE TABLE IF NOT EXISTS clientes (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefono VARCHAR(20),
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INTEGER DEFAULT 0,
    categoria VARCHAR(100),
    imagen VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla de facturas
CREATE TABLE IF NOT EXISTS facturas (
    id SERIAL PRIMARY KEY,
    numero_factura VARCHAR(50) UNIQUE NOT NULL,
    cliente_id INTEGER REFERENCES clientes(id),
    fecha_factura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(10,2) NOT NULL,
    estado VARCHAR(20) DEFAULT 'pendiente'
);

-- Crear tabla de detalles de factura
CREATE TABLE IF NOT EXISTS detalles_factura (
    id SERIAL PRIMARY KEY,
    factura_id INTEGER REFERENCES facturas(id),
    producto_id INTEGER REFERENCES productos(id),
    cantidad INTEGER NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL
);

-- Crear tabla de actividades
CREATE TABLE IF NOT EXISTS actividades (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER REFERENCES usuarios(id),
    accion VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crear tabla de movimientos de stock
CREATE TABLE IF NOT EXISTS movimientos_stock (
    id SERIAL PRIMARY KEY,
    producto_id INTEGER REFERENCES productos(id),
    tipo_movimiento VARCHAR(20) NOT NULL, -- 'entrada' o 'salida'
    cantidad INTEGER NOT NULL,
    motivo TEXT,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertar usuario administrador por defecto
INSERT INTO usuarios (nombre, email, password, rol) 
VALUES ('Administrador', 'admin@sleepbetter.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON CONFLICT (email) DO NOTHING;

-- Crear índices para mejorar rendimiento
CREATE INDEX IF NOT EXISTS idx_productos_categoria ON productos(categoria);
CREATE INDEX IF NOT EXISTS idx_facturas_cliente ON facturas(cliente_id);
CREATE INDEX IF NOT EXISTS idx_actividades_usuario ON actividades(usuario_id);
CREATE INDEX IF NOT EXISTS idx_movimientos_producto ON movimientos_stock(producto_id);

-- Verificar que las tablas se crearon correctamente
SELECT 'Tablas creadas exitosamente' as mensaje;
SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'; 