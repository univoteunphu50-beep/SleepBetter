-- Crear tabla 'productos' con estructura optimizada
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    restock INT DEFAULT 0,
    precio DECIMAL(10,2) NOT NULL,
    costo DECIMAL(10,2) DEFAULT 0.00,
    comentarios TEXT,
    palabras_clave TEXT,
    imagen VARCHAR(255),
    tiene_lote BOOLEAN DEFAULT 0,
    lote VARCHAR(50),
    expiracion DATE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;