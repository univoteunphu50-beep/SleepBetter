<?php
session_start();
header('Content-Type: application/json');

$response = array();

if (isset($_SESSION['usuario_nombre'])) {
    $response['success'] = true;
    $response['nombre'] = $_SESSION['usuario_nombre'];
    $response['rol'] = $_SESSION['usuario_rol'] ?? '';
} else {
    $response['success'] = false;
    $response['message'] = 'No hay usuario logueado';
}

echo json_encode($response);
?> 