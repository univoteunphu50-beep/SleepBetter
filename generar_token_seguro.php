<?php
/**
 * Generador de Tokens Seguros para Sleep Better
 * 
 * Este script genera tokens seguros para el sistema de creación de administradores.
 * Úsalo para generar un nuevo token y reemplazar el token por defecto.
 */

// Configuración
$token_length = 32; // Longitud del token
$use_special_chars = true; // Incluir caracteres especiales
$use_numbers = true; // Incluir números
$use_uppercase = true; // Incluir mayúsculas
$use_lowercase = true; // Incluir minúsculas

/**
 * Genera un token seguro
 */
function generateSecureToken($length = 32, $use_special = true, $use_numbers = true, $use_upper = true, $use_lower = true) {
    $chars = '';
    
    if ($use_lower) {
        $chars .= 'abcdefghijklmnopqrstuvwxyz';
    }
    
    if ($use_upper) {
        $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    
    if ($use_numbers) {
        $chars .= '0123456789';
    }
    
    if ($use_special) {
        $chars .= '!@#$%^&*()_+-=[]{}|;:,.<>?';
    }
    
    if (empty($chars)) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    }
    
    $token = '';
    $char_length = strlen($chars);
    
    for ($i = 0; $i < $length; $i++) {
        $token .= $chars[random_int(0, $char_length - 1)];
    }
    
    return $token;
}

/**
 * Genera múltiples tokens para elegir
 */
function generateMultipleTokens($count = 5, $length = 32) {
    $tokens = [];
    
    for ($i = 0; $i < $count; $i++) {
        $tokens[] = generateSecureToken($length, $use_special_chars, $use_numbers, $use_uppercase, $use_lowercase);
    }
    
    return $tokens;
}

// Generar tokens
$tokens = generateMultipleTokens(5, $token_length);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Tokens Seguros - Sleep Better</title>
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
            color: #333;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
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
            font-size: 2rem;
            margin: 0;
        }

        .header p {
            color: #6c757d;
            margin-top: 10px;
        }

        .token-section {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .token-section h3 {
            color: #495057;
            margin-bottom: 15px;
            font-size: 1.2rem;
        }

        .token-item {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .token-item:hover {
            border-color: #667eea;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
        }

        .token-text {
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            color: #495057;
            word-break: break-all;
            flex: 1;
            margin-right: 10px;
        }

        .copy-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .copy-btn:hover {
            background: #5a6fd8;
            transform: translateY(-1px);
        }

        .instructions {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .instructions h3 {
            color: #1976d2;
            margin-bottom: 15px;
        }

        .instructions ol {
            margin-left: 20px;
        }

        .instructions li {
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            margin: 10px 0;
            overflow-x: auto;
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
            margin: 5px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .security-notice {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px solid #ffeaa7;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                <img src="logo_sleepbetter.jpg" alt="Logo Sleep Better">
            </div>
            <h1>Generador de Tokens Seguros</h1>
            <p>Genera tokens seguros para el sistema de creación de administradores</p>
        </div>

        <div class="instructions">
            <h3><i class="fas fa-info-circle"></i> Instrucciones de uso</h3>
            <ol>
                <li>Selecciona uno de los tokens generados abajo</li>
                <li>Copia el token seleccionado</li>
                <li>Abre el archivo <code>crear_admin_secreto.php</code></li>
                <li>Reemplaza la línea del token por defecto con tu nuevo token</li>
                <li>Guarda el archivo</li>
                <li>Usa el nuevo token en la URL: <code>crear_admin_secreto.php?token=TU_NUEVO_TOKEN</code></li>
            </ol>
        </div>

        <div class="token-section">
            <h3><i class="fas fa-key"></i> Tokens Generados</h3>
            <p>Selecciona uno de estos tokens seguros:</p>
            
            <?php foreach ($tokens as $index => $token): ?>
            <div class="token-item">
                <span class="token-text"><?php echo htmlspecialchars($token); ?></span>
                <button class="copy-btn" onclick="copyToken('<?php echo htmlspecialchars($token); ?>')">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="token-section">
            <h3><i class="fas fa-code"></i> Código para reemplazar</h3>
            <p>Reemplaza esta línea en <code>crear_admin_secreto.php</code>:</p>
            <div class="code-block">
$SECRET_TOKEN = "<?php echo $tokens[0]; ?>"; // Token seguro generado
            </div>
        </div>

        <div class="security-notice">
            <h4><i class="fas fa-shield-alt"></i> Notas de Seguridad</h4>
            <ul>
                <li>Guarda el token en un lugar seguro</li>
                <li>No compartas el token públicamente</li>
                <li>Cambia el token regularmente</li>
                <li>Usa HTTPS en producción</li>
                <li>Considera eliminar el archivo después de usarlo</li>
            </ul>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="crear_admin_secreto.php?token=<?php echo urlencode($tokens[0]); ?>" class="btn">
                <i class="fas fa-user-plus"></i> Probar con el primer token
            </a>
            <button onclick="location.reload()" class="btn">
                <i class="fas fa-sync-alt"></i> Generar nuevos tokens
            </button>
        </div>

        <div class="footer">
            <p>&copy; 2024 Sleep Better. Todos los derechos reservados.</p>
        </div>
    </div>

    <script>
        function copyToken(token) {
            navigator.clipboard.writeText(token).then(function() {
                // Mostrar notificación de éxito
                const btn = event.target.closest('.copy-btn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Copiado';
                btn.style.background = '#28a745';
                
                setTimeout(function() {
                    btn.innerHTML = originalText;
                    btn.style.background = '#667eea';
                }, 2000);
            }).catch(function(err) {
                console.error('Error al copiar: ', err);
                alert('Error al copiar el token. Cópialo manualmente.');
            });
        }

        // Generar nuevos tokens al hacer clic en el botón
        document.addEventListener('DOMContentLoaded', function() {
            // Agregar funcionalidad para generar nuevos tokens
            const generateBtn = document.querySelector('button[onclick="location.reload()"]');
            if (generateBtn) {
                generateBtn.addEventListener('click', function() {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                });
            }
        });
    </script>
</body>
</html> 