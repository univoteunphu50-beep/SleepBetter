<?php
// Versión de debug de auth_check.php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Establecer header JSON desde el principio
header('Content-Type: application/json');

try {
    session_start();
    
    $debug = [];
    $debug[] = "Session iniciada";
    
    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['usuario_id'])) {
        $debug[] = "Usuario NO autenticado";
        
        // Si es una petición AJAX, devolver error JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['error' => 'No autenticado', 'redirect' => 'login.php', 'debug' => $debug]);
            exit;
        }
        
        // Si es una petición normal, redirigir al login
        header('Location: ../login.php');
        exit;
    }
    
    $debug[] = "Usuario autenticado: " . $_SESSION['usuario_id'];
    
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
    
    $debug[] = "Funciones definidas correctamente";
    $debug[] = "Usuario actual: " . json_encode(getCurrentUser());
    
    // Si llegamos aquí, todo está bien
    echo json_encode(['success' => true, 'debug' => $debug]);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Exception: ' . $e->getMessage()]);
} catch (Error $e) {
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?> 