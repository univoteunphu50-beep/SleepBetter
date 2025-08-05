<?php
session_start();

echo "<h2>Estado de la sesión:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (isset($_SESSION['usuario_id'])) {
    echo "<p style='color: green;'>✅ Usuario autenticado: " . $_SESSION['usuario_nombre'] . "</p>";
    echo "<p><a href='index.php'>Ir al Dashboard</a></p>";
    echo "<p><a href='logout.php'>Cerrar Sesión</a></p>";
} else {
    echo "<p style='color: red;'>❌ Usuario NO autenticado</p>";
    echo "<p><a href='login.php'>Ir al Login</a></p>";
}
?> 