<?php
include("auth_check.php");
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sleep Better - Panel de Administración</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      color: #333;
    }

    .container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar */
    .sidebar {
      width: 280px;
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      box-shadow: 2px 0 20px rgba(0, 0, 0, 0.1);
      padding: 2rem 0;
      position: fixed;
      height: 100vh;
      overflow-y: auto;
    }

    .logo-section {
      text-align: center;
      padding: 0 2rem 2rem;
      border-bottom: 1px solid #e0e0e0;
      margin-bottom: 2rem;
    }

    .logo-section img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 1rem;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .logo-section h1 {
      font-size: 1.5rem;
      color: #333;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .logo-section p {
      color: #666;
      font-size: 0.9rem;
    }

    .user-info {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 1rem;
      margin: 0 1rem 1rem;
      border-radius: 12px;
      text-align: center;
    }

    .user-info h3 {
      font-size: 1rem;
      margin-bottom: 0.5rem;
    }

    .user-info p {
      font-size: 0.8rem;
      opacity: 0.9;
      margin-bottom: 0.5rem;
    }

    .logout-btn {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 8px;
      cursor: pointer;
      font-size: 0.8rem;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .logout-btn:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: translateY(-2px);
    }

    .nav-menu {
      padding: 0 1rem;
    }

    .nav-item {
      margin-bottom: 0.5rem;
    }

    .nav-link {
      display: flex;
      align-items: center;
      padding: 1rem 1.5rem;
      color: #555;
      text-decoration: none;
      border-radius: 12px;
      transition: all 0.3s ease;
      font-weight: 500;
    }

    .nav-link:hover {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      transform: translateX(5px);
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .nav-link.active {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .nav-link i {
      margin-right: 12px;
      width: 20px;
      text-align: center;
    }

    /* Main Content */
    .main-content {
      flex: 1;
      margin-left: 280px;
      padding: 2rem;
    }

    .content-header {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      padding: 2rem;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      margin-bottom: 2rem;
    }

    .content-header h2 {
      color: #2c3e50;
      font-size: 2rem;
      margin-bottom: 1rem;
    }

    .content-header p {
      color: #666;
      font-size: 1.1rem;
    }

    .iframe-container {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      height: calc(100vh - 200px);
    }

    .iframe-container iframe {
      width: 100%;
      height: 100%;
      border: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
      }

      .sidebar.open {
        transform: translateX(0);
      }

      .main-content {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
      <div class="logo-section">
        <img src="logo_sleepbetter.jpg" alt="Logo Sleep Better">
        <h1>Sleep Better</h1>
        <p>Panel de Administración</p>
      </div>

      <div class="user-info">
        <h3><i class="fas fa-user"></i> <?php echo htmlspecialchars($currentUser['nombre']); ?></h3>
        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($currentUser['email']); ?></p>
        <p><i class="fas fa-user-tag"></i> <?php echo ucfirst($currentUser['rol']); ?></p>
        <a href="logout.php" class="logout-btn">
          <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
        </a>
      </div>

      <nav class="nav-menu">
        <div class="nav-item">
          <a href="#" class="nav-link active" onclick="cargarPagina('clientes/index.html', this)">
            <i class="fas fa-users"></i>
            Gestión de Clientes
          </a>
        </div>
        <div class="nav-item">
          <a href="#" class="nav-link" onclick="cargarPagina('productos/index.html', this)">
            <i class="fas fa-box"></i>
            Gestión de Productos
          </a>
        </div>
        <div class="nav-item">
          <a href="#" class="nav-link" onclick="cargarPagina('facturacion/index.html', this)">
            <i class="fas fa-receipt"></i>
            Facturación
          </a>
        </div>

      </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <div class="content-header">
        <h2>Bienvenido, <?php echo htmlspecialchars($currentUser['nombre']); ?>!</h2>
        <p>Selecciona una opción del menú para comenzar a gestionar el sistema.</p>
      </div>

      <div class="iframe-container">
        <iframe id="content-frame" src="clientes/index.html" frameborder="0"></iframe>
      </div>
    </div>
  </div>

  <script>
    function cargarPagina(url, element) {
      // Remover clase active de todos los enlaces
      document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
      });
      
      // Agregar clase active al enlace clickeado
      element.classList.add('active');
      
      // Cargar la página en el iframe con timestamp para evitar caché
      const timestamp = new Date().getTime();
      document.getElementById('content-frame').src = url + '?t=' + timestamp;
    }

    // Función para manejar errores del iframe
    document.getElementById('content-frame').onerror = function() {
      this.src = 'error.html';
    };
  </script>
</body>
</html> 