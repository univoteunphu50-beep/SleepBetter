CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    stock INT NOT NULL,
    restock INT,
    precio DECIMAL(10,2),
    costo DECIMAL(10,2),
    comentarios TEXT,
    palabras_clave TEXT,
    imagen VARCHAR(255),
    tiene_lote BOOLEAN DEFAULT 0,
    lote VARCHAR(50),
    expiracion DATE
);
