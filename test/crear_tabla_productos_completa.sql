-- Script completo para crear la tabla productos con todas las columnas necesarias
-- Usar este script si la tabla no existe o si necesitas recrearla completamente

USE sleepbetter_db;

-- Eliminar tabla si existe (¡CUIDADO! Esto borrará todos los datos)
-- DROP TABLE IF EXISTS productos;

-- Crear tabla productos con estructura completa
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    restock INT DEFAULT 0,
    precio DECIMAL(10,2) DEFAULT 0.00,
    costo DECIMAL(10,2) DEFAULT 0.00,
    comentarios TEXT,
    palabras_clave TEXT,
    keywords TEXT,
    imagen VARCHAR(255),
    con_lote BOOLEAN DEFAULT 0,
    numero_lote VARCHAR(50),
    fecha_fabricacion DATE,
    fecha_expiracion DATE,
    temperatura_almacenamiento VARCHAR(100),
    condiciones_especiales TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Crear índices para mejorar el rendimiento
CREATE INDEX idx_productos_nombre ON productos(nombre);
CREATE INDEX idx_productos_stock ON productos(stock);
CREATE INDEX idx_productos_con_lote ON productos(con_lote);
CREATE INDEX idx_productos_fecha_expiracion ON productos(fecha_expiracion);

-- Insertar algunos productos de ejemplo
INSERT INTO productos (nombre, stock, restock, precio, costo, comentarios, palabras_clave, con_lote, numero_lote, fecha_fabricacion, fecha_expiracion, temperatura_almacenamiento, condiciones_especiales) VALUES
('Mascarilla CPAP ResMed AirFit F40', 50, 10, 299.99, 200.00, 'Mascarilla nasal de almohadilla de gel', 'cpap, mascarilla, resmed, airfit', 1, 'LOT-2024-001', '2024-01-15', '2026-01-15', '20-25°C', 'Mantener en lugar seco'),
('Tubos CPAP 6ft', 100, 20, 25.99, 15.00, 'Tubos de repuesto para CPAP', 'cpap, tubos, repuesto', 0, NULL, NULL, NULL, '20-25°C', 'Evitar dobleces'),
('Filtros CPAP', 200, 50, 12.99, 8.00, 'Filtros de aire para CPAP', 'cpap, filtros, aire', 0, NULL, NULL, NULL, '20-25°C', 'Cambiar cada 3 meses');

-- Verificar la estructura creada
DESCRIBE productos;

-- Mostrar productos de ejemplo
SELECT * FROM productos; 