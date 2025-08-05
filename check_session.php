<?php
session_start();
header('Content-Type: application/json');

$response = [
    'session_started' => true,
    'session_id' => session_id(),
    'user_id' => $_SESSION['usuario_id'] ?? 'NO SET',
    'user_name' => $_SESSION['usuario_nombre'] ?? 'NO SET',
    'user_role' => $_SESSION['usuario_rol'] ?? 'NO SET',
    'user_email' => $_SESSION['usuario_email'] ?? 'NO SET',
    'all_session_data' => $_SESSION
];

echo json_encode($response);
?> 