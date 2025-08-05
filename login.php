<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include("conexion.php");
    include("funciones_actividades.php");
    
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (!empty($usuario) && !empty($password)) {
        $stmt = $conn->prepare("SELECT id, usuario, password, nombre, email, rol FROM usuarios WHERE usuario = ? AND activo = 1");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verificar password (password por defecto: 'password')
            if (password_verify($password, $user['password']) || $password === 'password') {
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                $_SESSION['usuario_rol'] = $user['rol'];
                $_SESSION['usuario_email'] = $user['email'];
                
                // Registrar login exitoso
                registrarLogin($user['id'], $user['nombre'], true);
                
                header('Location: index.php');
                exit;
            } else {
                $error = "Contraseña incorrecta";
                // Registrar login fallido
                registrarLogin(0, $usuario, false);
            }
        } else {
            $error = "Usuario no encontrado";
            // Registrar login fallido
            registrarLogin(0, $usuario, false);
        }
        
        $stmt->close();
    } else {
        $error = "Por favor complete todos los campos";
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sleep Better</title>
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
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo img {
            height: 80px;
            margin-bottom: 15px;
        }

        .logo h1 {
            color: #2c3e50;
            font-weight: 700;
            font-size: 2rem;
            margin: 0;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
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

        .btn-login {
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

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        .demo-info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px solid #bee5eb;
        }

        .demo-info h4 {
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .demo-info p {
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .footer {
            margin-top: 30px;
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="logo_sleepbetter.jpg" alt="Logo Sleep Better">
            <h1>Sleep Better</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" id="usuario" name="usuario" class="form-control" 
                           placeholder="Ingrese su usuario" required 
                           value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Ingrese su contraseña" required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Iniciar Sesión
            </button>
        </form>

        <div class="demo-info">
            <h4><i class="fas fa-info-circle"></i> Información de Demo</h4>
            <p><strong>Usuario:</strong> admin</p>
            <p><strong>Contraseña:</strong> password</p>
            <p><strong>Rol:</strong> Administrador</p>
        </div>

        <div class="footer">
            <p>&copy; 2024 Sleep Better. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html> 