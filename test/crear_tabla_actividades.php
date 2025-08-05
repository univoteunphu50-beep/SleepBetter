<?php
include("conexion.php");

echo "<h2>Creando tabla de registro de actividades</h2>";

try {
    // Crear tabla actividades
    $sql_actividades = "
    CREATE TABLE IF NOT EXISTS actividades (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        usuario_nombre VARCHAR(100) NOT NULL,
        accion VARCHAR(100) NOT NULL,
        modulo VARCHAR(50) NOT NULL,
        descripcion TEXT,
        datos_anteriores JSON NULL,
        datos_nuevos JSON NULL,
        ip_address VARCHAR(45),
        user_agent TEXT,
        fecha_actividad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    if ($conn->query($sql_actividades)) {
        echo "<p style='color: green;'>✅ Tabla 'actividades' creada exitosamente</p>";
    } else {
        echo "<p style='color: red;'>❌ Error al crear tabla 'actividades': " . $conn->error . "</p>";
    }

    // Crear índices para actividades
    $indices_actividades = [
        "CREATE INDEX idx_actividades_usuario ON actividades(usuario_id)",
        "CREATE INDEX idx_actividades_fecha ON actividades(fecha_actividad)",
        "CREATE INDEX idx_actividades_modulo ON actividades(modulo)",
        "CREATE INDEX idx_actividades_accion ON actividades(accion)",
        "CREATE INDEX idx_actividades_usuario_fecha ON actividades(usuario_id, fecha_actividad)"
    ];

    foreach ($indices_actividades as $indice) {
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