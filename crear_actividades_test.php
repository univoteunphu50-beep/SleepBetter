<?php
include("conexion.php");
include("funciones_actividades.php");

echo "<h2>Creando actividades de prueba</h2>";

try {
    // Simular diferentes tipos de actividades usando las funciones existentes
    $actividades_prueba = [
        [
            'accion' => 'login_exitoso',
            'modulo' => 'autenticacion',
            'descripcion' => 'Usuario inició sesión',
            'datos_nuevos' => ['usuario' => 'admin', 'ip' => '192.168.1.100']
        ],
        [
            'accion' => 'crear',
            'modulo' => 'usuarios',
            'descripcion' => 'Creó nuevo usuario: Juan Pérez',
            'datos_nuevos' => ['nombre' => 'Juan Pérez', 'email' => 'juan@example.com', 'rol' => 'vendedor']
        ],
        [
            'accion' => 'actualizar',
            'modulo' => 'productos',
            'descripcion' => 'Actualizó producto: Almohada Premium',
            'datos_anteriores' => ['precio' => 25.00, 'stock' => 50],
            'datos_nuevos' => ['precio' => 30.00, 'stock' => 45]
        ],
        [
            'accion' => 'consultar',
            'modulo' => 'facturacion',
            'descripcion' => 'Consultó facturas del mes'
        ],
        [
            'accion' => 'crear',
            'modulo' => 'facturacion',
            'descripcion' => 'Creó factura #F001-2024',
            'datos_nuevos' => ['numero' => 'F001-2024', 'cliente' => 'María García', 'total' => 150.00]
        ],
        [
            'accion' => 'login_fallido',
            'modulo' => 'autenticacion',
            'descripcion' => 'Intento fallido de inicio de sesión',
            'datos_nuevos' => ['usuario' => 'usuario_inexistente', 'ip' => '192.168.1.101']
        ],
        [
            'accion' => 'eliminar',
            'modulo' => 'productos',
            'descripcion' => 'Eliminó producto: Producto obsoleto',
            'datos_anteriores' => ['id' => 5, 'nombre' => 'Producto obsoleto', 'precio' => 10.00]
        ],
        [
            'accion' => 'consultar',
            'modulo' => 'stock',
            'descripcion' => 'Consultó historial de movimientos de stock'
        ],
        [
            'accion' => 'actualizar',
            'modulo' => 'clientes',
            'descripcion' => 'Actualizó cliente: Carlos López',
            'datos_anteriores' => ['telefono' => '809-123-4567'],
            'datos_nuevos' => ['telefono' => '809-987-6543']
        ],
        [
            'accion' => 'logout',
            'modulo' => 'autenticacion',
            'descripcion' => 'Usuario cerró sesión'
        ]
    ];

    foreach ($actividades_prueba as $actividad) {
        $datos_anteriores = $actividad['datos_anteriores'] ?? null;
        $datos_nuevos = $actividad['datos_nuevos'] ?? null;
        
        // Usar la función registrarActividad con usuario_id específico
        if (registrarActividad($actividad['accion'], $actividad['modulo'], $actividad['descripcion'], $datos_anteriores, $datos_nuevos, 1, 'admin')) {
            echo "<p style='color: green;'>✅ Actividad creada: {$actividad['accion']} en {$actividad['modulo']}</p>";
        } else {
            echo "<p style='color: red;'>❌ Error al crear actividad: {$actividad['accion']}</p>";
        }
    }

    echo "<h3>✅ Actividades de prueba creadas exitosamente</h3>";
    echo "<p><a href='index.php'>Volver al panel principal</a></p>";

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

$conn->close();
?> 