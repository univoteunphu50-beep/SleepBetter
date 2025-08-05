<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    // Si es una petición AJAX, devolver error JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No autenticado', 'redirect' => 'login.php']);
        exit;
    }
    
    // Si es una petición normal, redirigir al login
    header('Location: ../login.php');
    exit;
}

// Función para obtener información del usuario actual
function getCurrentUser() {
    return [
        'id' => $_SESSION['usuario_id'],
        'nombre' => $_SESSION['usuario_nombre'],
        'rol' => $_SESSION['usuario_rol'],
        'email' => $_SESSION['usuario_email']
    ];
}

// Función para verificar permisos
function hasPermission($requiredRole) {
    $user = getCurrentUser();
    $roleHierarchy = [
        'admin' => 3,
        'gerente' => 2,
        'vendedor' => 1
    ];
    
    $userLevel = $roleHierarchy[$user['rol']] ?? 0;
    $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;
    
    return $userLevel >= $requiredLevel;
} 