<?php
session_start();
include("funciones_actividades.php");

// Registrar logout antes de destruir la sesión
if (isset($_SESSION['usuario_id'])) {
    registrarLogout($_SESSION['usuario_id'], $_SESSION['usuario_nombre']);
}

// Destruir la sesión
session_destroy();

// Redirigir al login
header('Location: login.php');
exit;
?> 