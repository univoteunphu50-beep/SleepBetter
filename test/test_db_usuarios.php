<?php
include("conexion.php");

echo "<h2>Verificando estructura de la tabla usuarios</h2>";

// Verificar si la tabla existe
$result = $conn->query("SHOW TABLES LIKE 'usuarios'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✅ La tabla 'usuarios' existe</p>";
    
    // Mostrar estructura de la tabla
    echo "<h3>Estructura de la tabla:</h3>";
    $result = $conn->query("DESCRIBE usuarios");
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Llave</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Contar usuarios
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    $count = $result->fetch_assoc()['total'];
    echo "<p><strong>Total de usuarios:</strong> $count</p>";
    
    // Mostrar algunos usuarios de ejemplo
    echo "<h3>Usuarios en la base de datos:</h3>";
    $result = $conn->query("SELECT id, usuario, nombre, email, rol, activo, fecha_creacion FROM usuarios LIMIT 5");
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Activo</th><th>Fecha</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['usuario'] . "</td>";
        echo "<td>" . $row['nombre'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['rol'] . "</td>";
        echo "<td>" . $row['activo'] . "</td>";
        echo "<td>" . $row['fecha_creacion'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} else {
    echo "<p style='color: red;'>❌ La tabla 'usuarios' NO existe</p>";
    
    // Crear la tabla si no existe
    echo "<h3>Creando tabla usuarios...</h3>";
    $sql = "CREATE TABLE usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        rol ENUM('admin', 'gerente', 'vendedor') DEFAULT 'vendedor',
        activo TINYINT(1) DEFAULT 1,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>✅ Tabla 'usuarios' creada exitosamente</p>";
        
        // Insertar usuario administrador por defecto
        $password_hash = password_hash('password', PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (usuario, password, nombre, email, rol) VALUES ('admin', '$password_hash', 'Administrador', 'admin@sleepbetter.com', 'admin')";
        
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>✅ Usuario administrador creado</p>";
        } else {
            echo "<p style='color: red;'>❌ Error al crear usuario administrador: " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Error al crear tabla: " . $conn->error . "</p>";
    }
}

$conn->close();
?> 