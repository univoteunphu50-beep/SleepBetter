-- Tabla de Facturas
CREATE TABLE IF NOT EXISTS facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    vendedor VARCHAR(100) NOT NULL,
    cedula_cliente VARCHAR(13) NOT NULL,
    subtotal DECIMAL(10,2),
    itbis DECIMAL(10,2),
    total DECIMAL(10,2)
);

-- Tabla de Detalle de Facturas
CREATE TABLE IF NOT EXISTS detalle_factura (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_factura INT NOT NULL,
    id_producto INT NOT NULL,
    nombre VARCHAR(100),
    precio DECIMAL(10,2),
    itebis BOOLEAN DEFAULT 0,
    descuento DECIMAL(5,2),
    cantidad INT,
    precio_unitario DECIMAL(10,2),
    total DECIMAL(10,2),
    FOREIGN KEY (id_factura) REFERENCES facturas(id),
    FOREIGN KEY (id_producto) REFERENCES productos(id)
);
