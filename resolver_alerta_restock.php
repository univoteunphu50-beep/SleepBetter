<?php
header('Content-Type: application/json');
include("conexion.php");
include("funciones_stock.php");

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $id_alerta = $input['id_alerta'] ?? 0;
    $accion = $input['accion'] ?? '';
    $usuario = $_SESSION['usuario_nombre'] ?? 'Sistema';
    
    if (!$id_alerta) {
        throw new Exception('ID de alerta no proporcionado');
    }
    
    if (!in_array($accion, ['resolver', 'ignorar'])) {
        throw new Exception('Acción no válida');
    }
    
    $success = false;
    
    if ($accion === 'resolver') {
        $success = resolverAlertaRestock($id_alerta, $usuario);
    } else {
        $success = ignorarAlertaRestock($id_alerta, $usuario);
    }
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Alerta procesada exitosamente']);
    } else {
        throw new Exception('Error al procesar la alerta');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 