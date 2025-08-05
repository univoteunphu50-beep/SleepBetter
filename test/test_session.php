<?php
session_start();
header('Content-Type: application/json');

$response = [
    'session_exists' => isset($_SESSION),
    'user_id' => $_SESSION['usuario_id'] ?? null,
    'user_name' => $_SESSION['usuario_nombre'] ?? null,
    'user_role' => $_SESSION['usuario_rol'] ?? null,
    'session_data' => $_SESSION
];

echo json_encode($response);
?> 