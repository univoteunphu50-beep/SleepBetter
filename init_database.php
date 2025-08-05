<?php
/**
 * Script para inicializar la base de datos PostgreSQL
 * Ejecuta este script una vez para crear todas las tablas necesarias
 */

include("conexion.php");

echo "<h2>🔧 Inicializando Base de Datos PostgreSQL</h2>";

try {
    // Verificar conexión
    if ($conn instanceof PDO) {
        echo "<p>✅ Conexión a PostgreSQL establecida correctamente</p>";
    } else {
        echo "<p>⚠️ Usando MySQL local</p>";
    }
    
    // Script SQL para crear todas las tablas
    $sql_script = "
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
        cedula VARCHAR(20) UNIQUE,
        cliente VARCHAR(100) NOT NULL,
        telefono VARCHAR(20),
        email VARCHAR(100),
        direccion TEXT,
        comentarios TEXT,
        fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Crear tabla de productos
    CREATE TABLE IF NOT EXISTS productos (
        id SERIAL PRIMARY KEY,
        nombre VARCHAR(200) NOT NULL,
        stock INTEGER DEFAULT 0,
        restock INTEGER DEFAULT 0,
        precio DECIMAL(10,2) NOT NULL,
        costo DECIMAL(10,2) DEFAULT 0,
        comentarios TEXT,
        palabras_clave TEXT,
        imagen VARCHAR(255),
        con_lote BOOLEAN DEFAULT FALSE,
        numero_lote VARCHAR(50),
        fecha_fabricacion DATE,
        fecha_expiracion DATE,
        temperatura_almacenamiento VARCHAR(100),
        condiciones_especiales TEXT,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Crear tabla de facturas
    CREATE TABLE IF NOT EXISTS facturas (
        id SERIAL PRIMARY KEY,
        numero_factura VARCHAR(50) UNIQUE NOT NULL,
        fecha_factura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        vendedor VARCHAR(100),
        cliente VARCHAR(100),
        subtotal DECIMAL(10,2) NOT NULL,
        itbis DECIMAL(10,2) DEFAULT 0,
        total DECIMAL(10,2) NOT NULL,
        estado VARCHAR(20) DEFAULT 'pendiente'
    );

    -- Crear tabla de detalles de factura
    CREATE TABLE IF NOT EXISTS detalles_factura (
        id SERIAL PRIMARY KEY,
        factura_id INTEGER REFERENCES facturas(id),
        producto_id INTEGER REFERENCES productos(id),
        nombre_producto VARCHAR(200) NOT NULL,
        precio DECIMAL(10,2) NOT NULL,
        aplicar_itbis BOOLEAN DEFAULT TRUE,
        descuento DECIMAL(10,2) DEFAULT 0,
        cantidad INTEGER NOT NULL,
        total_producto DECIMAL(10,2) NOT NULL
    );

    -- Crear tabla de actividades
    CREATE TABLE IF NOT EXISTS actividades (
        id SERIAL PRIMARY KEY,
        usuario_id INTEGER REFERENCES usuarios(id),
        usuario_nombre VARCHAR(100),
        accion VARCHAR(100) NOT NULL,
        modulo VARCHAR(50),
        descripcion TEXT,
        datos_anteriores TEXT,
        datos_nuevos TEXT,
        ip_address VARCHAR(45),
        user_agent TEXT,
        fecha_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Crear tabla de movimientos de stock
    CREATE TABLE IF NOT EXISTS movimientos_stock (
        id SERIAL PRIMARY KEY,
        id_producto INTEGER REFERENCES productos(id),
        tipo_movimiento VARCHAR(20) NOT NULL,
        cantidad_anterior INTEGER NOT NULL,
        cantidad_movimiento INTEGER NOT NULL,
        cantidad_nueva INTEGER NOT NULL,
        motivo TEXT,
        usuario VARCHAR(100),
        id_factura INTEGER REFERENCES facturas(id),
        fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    -- Crear tabla de alertas de restock
    CREATE TABLE IF NOT EXISTS alertas_restock (
        id SERIAL PRIMARY KEY,
        id_producto INTEGER REFERENCES productos(id),
        stock_actual INTEGER NOT NULL,
        restock_limite INTEGER NOT NULL,
        estado VARCHAR(20) DEFAULT 'activa',
        usuario_resuelve VARCHAR(100),
        fecha_alerta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        fecha_resuelta TIMESTAMP
    );

    -- Insertar usuario administrador por defecto
    INSERT INTO usuarios (nombre, email, password, rol) 
    VALUES ('Administrador', 'admin@sleepbetter.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
    ON CONFLICT (email) DO NOTHING;

    -- Crear índices para mejorar rendimiento
    CREATE INDEX IF NOT EXISTS idx_productos_categoria ON productos(nombre);
    CREATE INDEX IF NOT EXISTS idx_facturas_cliente ON facturas(cliente);
    CREATE INDEX IF NOT EXISTS idx_actividades_usuario ON actividades(usuario_id);
    CREATE INDEX IF NOT EXISTS idx_movimientos_producto ON movimientos_stock(id_producto);
    CREATE INDEX IF NOT EXISTS idx_alertas_producto ON alertas_restock(id_producto);
    CREATE INDEX IF NOT EXISTS idx_alertas_estado ON alertas_restock(estado);
    ";

    // Ejecutar el script SQL
    $statements = explode(';', $sql_script);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                if ($conn instanceof PDO) {
                    $conn->exec($statement);
                } else {
                    $conn->query($statement);
                }
                echo "<p>✅ Ejecutado: " . substr($statement, 0, 50) . "...</p>";
            } catch (Exception $e) {
                echo "<p>⚠️ Error en: " . substr($statement, 0, 50) . "... - " . $e->getMessage() . "</p>";
            }
        }
    }
    
    echo "<h3>🎉 Base de datos inicializada correctamente</h3>";
    echo "<p><strong>Credenciales de acceso:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> admin@sleepbetter.com</li>";
    echo "<li><strong>Password:</strong> password</li>";
    echo "</ul>";
    
    echo "<p><a href='login.php'>🔗 Ir al login</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?> 