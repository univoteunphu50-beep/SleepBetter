<?php
include("conexion.php");

echo "<h2>Creando tablas para sistema de stock</h2>";

try {
    // Crear tabla movimientos_stock
    $sql_movimientos = "
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
    ";
    
    if ($conn->query($sql_movimientos)) {
        echo "<p style='color: green;'>✅ Tabla 'movimientos_stock' creada exitosamente</p>";
    } else {
        echo "<p style='color: red;'>❌ Error al crear tabla 'movimientos_stock': " . $conn->error . "</p>";
    }
    
    // Crear índices para movimientos_stock
    $indices_movimientos = [
        "CREATE INDEX idx_movimientos_producto ON movimientos_stock(id_producto)",
        "CREATE INDEX idx_movimientos_fecha ON movimientos_stock(fecha_movimiento)",
        "CREATE INDEX idx_movimientos_tipo ON movimientos_stock(tipo_movimiento)",
        "CREATE INDEX idx_movimientos_factura ON movimientos_stock(id_factura)"
    ];
    
    foreach ($indices_movimientos as $indice) {
        if ($conn->query($indice)) {
            echo "<p style='color: green;'>✅ Índice creado</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Índice ya existe o error: " . $conn->error . "</p>";
        }
    }
    
    // Crear tabla alertas_restock
    $sql_alertas = "
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
    ";
    
    if ($conn->query($sql_alertas)) {
        echo "<p style='color: green;'>✅ Tabla 'alertas_restock' creada exitosamente</p>";
    } else {
        echo "<p style='color: red;'>❌ Error al crear tabla 'alertas_restock': " . $conn->error . "</p>";
    }
    
    // Crear índices para alertas_restock
    $indices_alertas = [
        "CREATE INDEX idx_alertas_producto ON alertas_restock(id_producto)",
        "CREATE INDEX idx_alertas_estado ON alertas_restock(estado)",
        "CREATE INDEX idx_alertas_fecha ON alertas_restock(fecha_alerta)"
    ];
    
    foreach ($indices_alertas as $indice) {
        if ($conn->query($indice)) {
            echo "<p style='color: green;'>✅ Índice creado</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Índice ya existe o error: " . $conn->error . "</p>";
        }
    }
    
    echo "<h3>✅ Proceso completado</h3>";
    echo "<p><a href='index.php'>Volver al panel principal</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

$conn->close();
?> 