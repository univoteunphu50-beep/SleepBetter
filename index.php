<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}
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
      font-size: 1.2rem;
      width: 20px;
      text-align: center;
    }

    /* Main Content */
    .main-content {
      flex: 1;
      margin-left: 280px;
      background: #f8f9fa;
      min-height: 100vh;
    }

    .header {
      background: white;
      padding: 1.5rem 2rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .header h2 {
      color: #333;
      font-weight: 600;
    }

    .header .user-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
    }

    .logout-btn {
      background: #dc3545;
      color: white;
      border: none;
      padding: 8px 16px;
      border-radius: 6px;
      cursor: pointer;
      font-size: 0.9rem;
      transition: background 0.3s ease;
    }

    .logout-btn:hover {
      background: #c82333;
    }

    .content-area {
      padding: 2rem;
      height: calc(100vh - 80px);
    }

    .iframe-container {
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      height: 100%;
      overflow: hidden;
    }

    iframe {
      width: 100%;
      height: 100%;
      border: none;
      border-radius: 15px;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        z-index: 1000;
      }

      .sidebar.open {
        transform: translateX(0);
      }

      .main-content {
        margin-left: 0;
      }

      .mobile-toggle {
        display: block;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #333;
        cursor: pointer;
      }

      .header {
        padding: 1rem;
      }
    }

    .mobile-toggle {
      display: none;
    }

    /* Loading animation */
    .loading {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
      color: #667eea;
    }

    .spinner {
      width: 40px;
      height: 40px;
      border: 4px solid #f3f3f3;
      border-top: 4px solid #667eea;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Welcome message */
    .welcome-message {
      text-align: center;
      padding: 3rem;
      color: #666;
    }

    .welcome-message h3 {
      font-size: 2rem;
      margin-bottom: 1rem;
      color: #333;
    }

    .welcome-message p {
      font-size: 1.1rem;
      line-height: 1.6;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .stat-card {
      background: white;
      padding: 1.5rem;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: transform 0.3s ease;
    }

    .stat-card:hover {
      transform: translateY(-5px);
    }

    .stat-card i {
      font-size: 2rem;
      color: #667eea;
      margin-bottom: 1rem;
    }

    .stat-card h4 {
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
      color: #333;
    }

    .stat-card p {
      color: #666;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
      <div class="logo-section">
        <img src="logo_sleepbetter.jpg" alt="Sleep Better Logo">
        <h1>Sleep Better</h1>
        <p>Panel de Administración</p>
      </div>
      
      <div class="nav-menu">
        <div class="nav-item">
          <a href="#" class="nav-link active" onclick="cargarPagina('clientes/index.html', this)">
            <i class="fas fa-users"></i>
            <span>Gestión de Clientes</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="#" class="nav-link" onclick="cargarPagina('productos/index.html', this)">
            <i class="fas fa-box"></i>
            <span>Gestión de Productos</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="#" class="nav-link" onclick="cargarPagina('facturacion/index.php', this)">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Facturación</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="#" class="nav-link" onclick="cargarPagina('usuarios/index.php', this)">
            <i class="fas fa-user-cog"></i>
            <span>Gestión de Usuarios</span>
          </a>
        </div>
        <div class="nav-item">
          <a href="#" class="nav-link" onclick="cargarPagina('historial_stock.php', this)">
            <i class="fas fa-history"></i>
            <span>Historial de Stock</span>
          </a>
        </div>
                    <div class="nav-item">
              <a href="#" class="nav-link" onclick="cargarPagina('alertas_restock.php', this)">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Alertas de Restock</span>
              </a>
            </div>
            <div class="nav-item">
              <a href="#" class="nav-link" onclick="cargarPagina('registro_actividades.php', this)">
                <i class="fas fa-history"></i>
                <span>Registro de Actividades</span>
              </a>
            </div>

      </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
      <header class="header">
        <button class="mobile-toggle" onclick="toggleSidebar()">
          <i class="fas fa-bars"></i>
        </button>
        <h2 id="page-title">Panel de Administración</h2>
        <div class="user-info">
          <div class="user-avatar"><?php echo substr($_SESSION['usuario_nombre'], 0, 1); ?></div>
          <span><?php echo $_SESSION['usuario_nombre']; ?></span>
          <button class="logout-btn" onclick="logout()">
            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
          </button>
        </div>
      </header>

      <div class="content-area">
        <div class="iframe-container">
          <div id="welcome-content" class="welcome-message">
            <h3>¡Bienvenido a Sleep Better!</h3>
            <p>Selecciona una opción del menú lateral para comenzar a gestionar tu negocio.</p>
            
            <div class="stats-grid">
              <div class="stat-card">
                <i class="fas fa-users"></i>
                <h4>Clientes</h4>
                <p>Gestiona tu base de datos de clientes</p>
              </div>
              <div class="stat-card">
                <i class="fas fa-box"></i>
                <h4>Productos</h4>
                <p>Administra tu inventario de productos</p>
              </div>
              <div class="stat-card">
                <i class="fas fa-file-invoice-dollar"></i>
                <h4>Facturación</h4>
                <p>Crea y gestiona facturas</p>
              </div>
              <div class="stat-card">
                <i class="fas fa-user-cog"></i>
                <h4>Usuarios</h4>
                <p>Administra usuarios del sistema</p>
              </div>

            </div>
          </div>
          
          <iframe id="frame" style="display: none;"></iframe>
        </div>
      </div>
    </main>
  </div>

  <script>
    function cargarPagina(ruta, element) {
      // Remover clase active de todos los enlaces
      document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
      });
      
      // Agregar clase active al enlace clickeado
      if (element) {
        element.classList.add('active');
      }
      
      // Ocultar contenido de bienvenida
      document.getElementById('welcome-content').style.display = 'none';
      
      // Mostrar iframe
      const iframe = document.getElementById('frame');
      iframe.style.display = 'block';
      
      // Actualizar título de la página
        const titles = {
    'clientes/index.html': 'Gestión de Clientes',
    'productos/index.html': 'Gestión de Productos',
    'facturacion/index.php': 'Facturación',
    'usuarios/index.php': 'Gestión de Usuarios',
    'historial_stock.php': 'Historial de Stock',
    'alertas_restock.php': 'Alertas de Restock',
    'registro_actividades.php': 'Registro de Actividades'
  };
      
      document.getElementById('page-title').textContent = titles[ruta] || 'Panel de Administración';
      
      // Cargar la página en el iframe con parámetro de caché
      const timestamp = new Date().getTime();
      // Agregar parámetro adicional para facturación para forzar recarga
      if (ruta.includes('facturacion')) {
        iframe.src = ruta + '?t=' + timestamp + '&v=2';
      } else {
        iframe.src = ruta + '?t=' + timestamp;
      }
      
      // Cerrar sidebar en móvil
      if (window.innerWidth <= 768) {
        document.getElementById('sidebar').classList.remove('open');
      }
    }

    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('open');
    }

    function logout() {
      if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
        window.location.href = 'logout.php';
      }
    }

    // Cerrar sidebar al hacer clic fuera en móvil
    document.addEventListener('click', function(e) {
      if (window.innerWidth <= 768) {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.querySelector('.mobile-toggle');
        
        if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
          sidebar.classList.remove('open');
        }
      }
    });

    // Cargar página inicial
    window.addEventListener('load', function() {
      // Cargar clientes por defecto
      cargarPagina('clientes/index.html', document.querySelector('.nav-link'));
    });
  </script>
</body>
</html> 