-- Crear tabla para historial de movimientos de stock
USE sleepbetter_db;

CREATE TABLE IF NOT EXISTS movimientos_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    tipo_movimiento ENUM('venta', 'compra', 'ajuste', 'devolucion', 'merma', 'restock') NOT NULL,
    cantidad_anterior INT NOT NULL,
    cantidad_movimiento INT NOT NULL,
    cantidad_nueva INT NOT NULL,
    motivo VARCHAR(255),
    usuario VARCHAR(100),
    id_factura INT NULL,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_factura) REFERENCES facturas(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crear índices para mejorar el rendimiento
CREATE INDEX idx_movimientos_producto ON movimientos_stock(id_producto);
CREATE INDEX idx_movimientos_fecha ON movimientos_stock(fecha_movimiento);
CREATE INDEX idx_movimientos_tipo ON movimientos_stock(tipo_movimiento);
CREATE INDEX idx_movimientos_factura ON movimientos_stock(id_factura);

-- Crear tabla para alertas de restock
CREATE TABLE IF NOT EXISTS alertas_restock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_producto INT NOT NULL,
    stock_actual INT NOT NULL,
    restock_limite INT NOT NULL,
    fecha_alerta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado ENUM('activa', 'resuelta', 'ignorada') DEFAULT 'activa',
    usuario_resuelve VARCHAR(100),
    fecha_resuelta TIMESTAMP NULL,
    FOREIGN KEY (id_producto) REFERENCES productos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crear índices para alertas
CREATE INDEX idx_alertas_producto ON alertas_restock(id_producto);
CREATE INDEX idx_alertas_estado ON alertas_restock(estado);
CREATE INDEX idx_alertas_fecha ON alertas_restock(fecha_alerta); 