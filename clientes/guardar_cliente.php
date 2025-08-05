<?php
// Conectar a la base de datos
include("../conexion.php");
include("../db_helper.php");

// Capturar datos del formulario
$cedula = $_POST['cedula'];
$cliente = $_POST['nombre']; // El campo se llama 'nombre' en el formulario pero 'cliente' en la BD
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$direccion = $_POST['direccion'];
$comentarios = $_POST['comentarios'];

try {
    // Verificar si la cédula ya existe
    $existe = selectOne($conn, "SELECT cedula FROM clientes WHERE cedula = ?", [$cedula]);

    if ($existe) {
        echo '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error - Sleep Better</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                body {
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    margin: 0;
                    padding: 0;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .notification {
                    background: white;
                    border-radius: 15px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                    padding: 2rem;
                    text-align: center;
                    max-width: 400px;
                    animation: slideIn 0.5s ease-out;
                }
                @keyframes slideIn {
                    from { transform: translateY(-50px); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }
                .icon {
                    font-size: 3rem;
                    margin-bottom: 1rem;
                }
                .error { color: #e74c3c; }
                .success { color: #27ae60; }
                .title {
                    font-size: 1.5rem;
                    font-weight: 600;
                    margin-bottom: 1rem;
                }
                .message {
                    color: #666;
                    margin-bottom: 2rem;
                    line-height: 1.6;
                }
                .btn {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 8px;
                    font-size: 1rem;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    text-decoration: none;
                    display: inline-block;
                }
                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
                }
            </style>
        </head>
        <body>
            <div class="notification">
                <div class="icon error">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="title">Error</div>
                <div class="message">Ya existe un cliente con la cédula: <strong>' . $cedula . '</strong></div>
                <a href="index.html" class="btn">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </body>
        </html>';
        closeConnection($conn);
        exit;
    }

    // Insertar datos
    $sql = "INSERT INTO clientes (cedula, cliente, telefono, email, direccion, comentarios) VALUES (?, ?, ?, ?, ?, ?)";
    $params = [$cedula, $cliente, $telefono, $email, $direccion, $comentarios];
    
    $result = executeInsert($conn, $sql, $params);

    if ($result) {
        echo '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Éxito - Sleep Better</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                body {
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    margin: 0;
                    padding: 0;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .notification {
                    background: white;
                    border-radius: 15px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                    padding: 2rem;
                    text-align: center;
                    max-width: 400px;
                    animation: slideIn 0.5s ease-out;
                }
                @keyframes slideIn {
                    from { transform: translateY(-50px); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }
                .icon {
                    font-size: 3rem;
                    margin-bottom: 1rem;
                }
                .success { color: #27ae60; }
                .title {
                    font-size: 1.5rem;
                    font-weight: 600;
                    margin-bottom: 1rem;
                    color: #333;
                }
                .message {
                    color: #666;
                    margin-bottom: 2rem;
                    line-height: 1.6;
                }
                .client-info {
                    background: #f8f9fa;
                    border-radius: 8px;
                    padding: 1rem;
                    margin: 1rem 0;
                    text-align: left;
                }
                .client-info strong {
                    color: #333;
                }
                .btn {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 8px;
                    font-size: 1rem;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    text-decoration: none;
                    display: inline-block;
                    margin: 0 0.5rem;
                }
                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
                }
                .btn-secondary {
                    background: #6c757d;
                }
                .btn-secondary:hover {
                    background: #5a6268;
                }
            </style>
        </head>
        <body>
            <div class="notification">
                <div class="icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="title">¡Cliente Guardado!</div>
                <div class="message">El cliente ha sido registrado exitosamente en el sistema.</div>
                
                <div class="client-info">
                    <div><strong>Nombre:</strong> ' . $cliente . '</div>
                    <div><strong>Cédula:</strong> ' . $cedula . '</div>
                    <div><strong>Teléfono:</strong> ' . $telefono . '</div>
                </div>
                
                <div>
                    <a href="index.html" class="btn">
                        <i class="fas fa-users"></i> Ver Todos los Clientes
                    </a>
                    <a href="index.html" class="btn btn-secondary">
                        <i class="fas fa-plus"></i> Agregar Otro
                    </a>
                </div>
            </div>
            
            <script>
                // Redirigir automáticamente después de 3 segundos
                setTimeout(function() {
                    window.location.href = "index.html";
                }, 3000);
            </script>
        </body>
        </html>';
    } else {
        echo '<!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error - Sleep Better</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                body {
                    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    margin: 0;
                    padding: 0;
                    min-height: 100vh;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .notification {
                    background: white;
                    border-radius: 15px;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                    padding: 2rem;
                    text-align: center;
                    max-width: 400px;
                    animation: slideIn 0.5s ease-out;
                }
                @keyframes slideIn {
                    from { transform: translateY(-50px); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }
                .icon {
                    font-size: 3rem;
                    margin-bottom: 1rem;
                }
                .error { color: #e74c3c; }
                .title {
                    font-size: 1.5rem;
                    font-weight: 600;
                    margin-bottom: 1rem;
                }
                .message {
                    color: #666;
                    margin-bottom: 2rem;
                    line-height: 1.6;
                }
                .btn {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 8px;
                    font-size: 1rem;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    text-decoration: none;
                    display: inline-block;
                }
                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
                }
            </style>
        </head>
        <body>
            <div class="notification">
                <div class="icon error">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="title">Error</div>
                <div class="message">Hubo un problema al guardar el cliente. Por favor, inténtalo de nuevo.</div>
                <a href="index.html" class="btn">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </body>
        </html>';
    }

} catch (Exception $e) {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error - Sleep Better</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <style>
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                margin: 0;
                padding: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .notification {
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
                padding: 2rem;
                text-align: center;
                max-width: 400px;
                animation: slideIn 0.5s ease-out;
            }
            @keyframes slideIn {
                from { transform: translateY(-50px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
            .icon {
                font-size: 3rem;
                margin-bottom: 1rem;
            }
            .error { color: #e74c3c; }
            .title {
                font-size: 1.5rem;
                font-weight: 600;
                margin-bottom: 1rem;
            }
            .message {
                color: #666;
                margin-bottom: 2rem;
                line-height: 1.6;
            }
            .btn {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                font-size: 1rem;
                cursor: pointer;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-block;
            }
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }
        </style>
    </head>
    <body>
        <div class="notification">
            <div class="icon error">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="title">Error</div>
            <div class="message">Error al guardar cliente: ' . addslashes($e->getMessage()) . '</div>
            <a href="index.html" class="btn">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </body>
    </html>';
}

closeConnection($conn);
?>
