<?php
// Configuración del token secreto
$SECRET_TOKEN = "SleepBetter2024Admin"; // Cambia este token por uno más seguro
$TOKEN_PARAM = "token";

// Verificar si se proporcionó el token correcto
if (!isset($_GET[$TOKEN_PARAM]) || $_GET[$TOKEN_PARAM] !== $SECRET_TOKEN) {
    http_response_code(404);
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Página no encontrada</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                text-align: center;
                padding: 50px;
                background: #f5f5f5;
            }
            .error {
                color: #721c24;
                background-color: #f8d7da;
                border: 1px solid #f5c6cb;
                padding: 20px;
                border-radius: 5px;
                margin: 20px auto;
                max-width: 500px;
            }
        </style>
    </head>
    <body>
        <div class="error">
            <h1>404 - Página no encontrada</h1>
            <p>La página que buscas no existe.</p>
        </div>
    </body>
    </html>';
    exit;
}

// Incluir conexión a la base de datos
include("conexion.php");

$mensaje = '';
$tipo_mensaje = '';

// Procesar el formulario si se envió
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Validaciones
    if (empty($usuario) || empty($password) || empty($nombre) || empty($email)) {
        $mensaje = "Todos los campos son obligatorios";
        $tipo_mensaje = "error";
    } elseif ($password !== $confirm_password) {
        $mensaje = "Las contraseñas no coinciden";
        $tipo_mensaje = "error";
    } elseif (strlen($password) < 6) {
        $mensaje = "La contraseña debe tener al menos 6 caracteres";
        $tipo_mensaje = "error";
    } else {
        // Verificar si el usuario ya existe
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $mensaje = "El usuario ya existe en el sistema";
            $tipo_mensaje = "error";
        } else {
            // Verificar si el email ya existe
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $mensaje = "El email ya está registrado en el sistema";
                $tipo_mensaje = "error";
            } else {
                // Hash de la contraseña
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Insertar el nuevo administrador
                $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, nombre, email, rol, activo) VALUES (?, ?, ?, ?, 'admin', 1)");
                $stmt->bind_param("ssss", $usuario, $password_hash, $nombre, $email);
                
                if ($stmt->execute()) {
                    $mensaje = "¡Administrador creado exitosamente! El usuario ya puede iniciar sesión.";
                    $tipo_mensaje = "success";
                    
                    // Limpiar el formulario después del éxito
                    $usuario = $nombre = $email = '';
                } else {
                    $mensaje = "Error al crear el administrador: " . $stmt->error;
                    $tipo_mensaje = "error";
                }
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Administrador - Sleep Better</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 500px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            margin-bottom: 20px;
        }

        .logo img {
            height: 60px;
            margin-bottom: 10px;
        }

        .header h1 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
        }

        .header p {
            color: #6c757d;
            margin-top: 10px;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #495057;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .form-control {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .security-notice {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px solid #ffeaa7;
            font-size: 0.9rem;
        }

        .security-notice h4 {
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .password-requirements {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 5px;
            font-size: 0.8rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="logo_sleepbetter.jpg" alt="Logo Sleep Better">
            </div>
            <h1>Crear Administrador</h1>
            <p>Formulario para crear un nuevo usuario administrador</p>
        </div>

        <?php if ($mensaje): ?>
            <div class="message <?php echo $tipo_mensaje; ?>">
                <i class="fas fa-<?php echo $tipo_mensaje === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="usuario" name="usuario" class="form-control" 
                           placeholder="Ingrese el nombre de usuario" required 
                           value="<?php echo htmlspecialchars($usuario ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre Completo</label>
                <div class="input-group">
                    <i class="fas fa-user-tie"></i>
                    <input type="text" id="nombre" name="nombre" class="form-control" 
                           placeholder="Ingrese el nombre completo" required 
                           value="<?php echo htmlspecialchars($nombre ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-control" 
                           placeholder="Ingrese el email" required 
                           value="<?php echo htmlspecialchars($email ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Ingrese la contraseña" required>
                </div>
                <div class="password-requirements">
                    <i class="fas fa-info-circle"></i> Mínimo 6 caracteres
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                           placeholder="Confirme la contraseña" required>
                </div>
            </div>

            <button type="submit" class="btn">
                <i class="fas fa-user-plus"></i> Crear Administrador
            </button>
        </form>

        <div class="security-notice">
            <h4><i class="fas fa-shield-alt"></i> Nota de Seguridad</h4>
            <p>Este enlace es secreto y solo debe ser compartido con personas autorizadas. 
            Después de crear el administrador, se recomienda cambiar el token de seguridad.</p>
        </div>

        <div class="footer">
            <p>&copy; 2024 Sleep Better. Todos los derechos reservados.</p>
        </div>
    </div>

    <script>
        // Validación en tiempo real de las contraseñas
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });

        document.getElementById('password').addEventListener('input', function() {
            const confirmPassword = document.getElementById('confirm_password');
            if (confirmPassword.value) {
                if (this.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Las contraseñas no coinciden');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }
        });
    </script>
</body>
</html> 