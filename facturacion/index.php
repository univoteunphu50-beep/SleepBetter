<?php
// Obtener el nombre del usuario logueado directamente en el HTML
session_start();
$nombreUsuario = '';
if (isset($_SESSION['usuario_nombre'])) {
  $nombreUsuario = $_SESSION['usuario_nombre'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Facturación - Sleep Better</title>
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
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
      background: rgba(255, 255, 255, 0.95);
      padding: 20px;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(10px);
    }

    .header img {
      height: 60px;
      margin-bottom: 10px;
    }

    .header h1 {
      color: #2c3e50;
      font-weight: 700;
      font-size: 2.5rem;
      margin: 0;
    }

    .tab-container {
      background: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(10px);
      overflow: hidden;
    }

    .tab-buttons {
      display: flex;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 0;
    }

    .tab-btn {
      flex: 1;
      padding: 20px;
      background: none;
      border: none;
      color: white;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      position: relative;
    }

    .tab-btn:hover {
      background: rgba(255, 255, 255, 0.1);
    }

    .tab-btn.active {
      background: rgba(255, 255, 255, 0.2);
    }

    .tab-btn.active::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: #fff;
    }

    .tab-content {
      display: none;
      padding: 30px;
    }

    .tab-content.active {
      display: block;
    }

    .form-section {
      background: #f8f9fa;
      padding: 25px;
      border-radius: 15px;
      margin-bottom: 25px;
      border: 1px solid #e9ecef;
    }

    .form-section h3 {
      color: #2c3e50;
      margin-bottom: 20px;
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .form-section h3 i {
      color: #667eea;
    }

    .form-row {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 20px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    .form-group label {
      font-weight: 500;
      margin-bottom: 8px;
      color: #495057;
    }

    .form-control {
      padding: 12px 15px;
      border: 2px solid #e9ecef;
      border-radius: 10px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: white;
    }
    
    .form-control[readonly] {
      background-color: #f8f9fa;
      color: #495057;
      cursor: not-allowed;
    }
    
    .form-text {
      font-size: 0.875rem;
      color: #6c757d;
      margin-top: 5px;
    }

    .form-control:focus {
      outline: none;
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-control:read-only {
      background: #f8f9fa;
      color: #6c757d;
    }

    .btn {
      padding: 12px 25px;
      border: none;
      border-radius: 10px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 1rem;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-primary {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-success {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white;
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
    }

    .btn-outline {
      background: transparent;
      border: 2px solid #667eea;
      color: #667eea;
    }

    .btn-outline:hover {
      background: #667eea;
      color: white;
    }

    .btn-danger {
      background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
      color: white;
    }

    .btn-danger:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
    }

    .table-container {
      background: white;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
    }

    .table th {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 15px;
      text-align: left;
      font-weight: 600;
    }

    .table td {
      padding: 12px 15px;
      border-bottom: 1px solid #e9ecef;
    }

    .table tbody tr:hover {
      background: #f8f9fa;
    }

    .totals-section {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 25px;
      border-radius: 15px;
      margin-top: 20px;
    }

    .totals-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
    }

    .total-item {
      text-align: center;
    }

    .total-item label {
      font-size: 0.9rem;
      opacity: 0.9;
      margin-bottom: 5px;
      display: block;
    }

    .total-item .value {
      font-size: 1.5rem;
      font-weight: 700;
    }

    .search-section {
      background: white;
      padding: 25px;
      border-radius: 15px;
      margin-bottom: 25px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .search-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      align-items: end;
    }

    .loading {
      text-align: center;
      padding: 40px;
      color: #6c757d;
    }

    .no-results {
      text-align: center;
      padding: 40px;
      color: #6c757d;
      font-style: italic;
    }

    .pagination {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-top: 20px;
    }

    .checkbox-group {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .checkbox-group input[type="checkbox"] {
      width: 18px;
      height: 18px;
      accent-color: #667eea;
    }

    .quantity-control {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .quantity-btn {
      width: 35px;
      height: 35px;
      border: none;
      border-radius: 50%;
      background: #667eea;
      color: white;
      cursor: pointer;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .quantity-btn:hover {
      background: #5a6fd8;
      transform: scale(1.1);
    }

    .quantity-input {
      width: 60px;
      text-align: center;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      padding: 8px;
    }

    .product-row {
      background: #f8f9fa;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 15px;
      border: 1px solid #e9ecef;
    }

    .product-grid {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr auto;
      gap: 15px;
      align-items: center;
    }

    .product-info {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .product-name {
      font-weight: 600;
      color: #2c3e50;
    }

    .product-id {
      font-size: 0.9rem;
      color: #6c757d;
    }

    .price-display {
      font-weight: 600;
      color: #28a745;
    }

    .total-display {
      font-weight: 700;
      color: #667eea;
      font-size: 1.1rem;
    }

    .remove-btn {
      background: #dc3545;
      color: white;
      border: none;
      border-radius: 50%;
      width: 35px;
      height: 35px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .remove-btn:hover {
      background: #c82333;
      transform: scale(1.1);
    }

    @media (max-width: 768px) {
      .product-grid {
        grid-template-columns: 1fr;
        gap: 10px;
      }
      
      .form-row {
        grid-template-columns: 1fr;
      }
      
      .totals-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  
  <div class="container">
    <div class="header">
      <img src="../logo_sleepbetter.jpg" alt="Logo Sleep Better">
      <h1>Facturación</h1>
    </div>

    <div class="tab-container">
      <div class="tab-buttons">
        <button class="tab-btn active" onclick="mostrarTab('facturar', this)">
          <i class="fas fa-receipt"></i> Crear Factura
        </button>
        <button class="tab-btn" onclick="mostrarTab('consultar', this)">
          <i class="fas fa-search"></i> Consultar Facturas
        </button>
      </div>

      <!-- TAB: Crear Factura -->
      <div id="facturar" class="tab-content active">
        <form id="form-factura">
          <!-- Datos del Cliente -->
          <div class="form-section">
            <h3><i class="fas fa-user"></i> Datos del Cliente</h3>
            <div class="form-row">
              <div class="form-group">
                <label>Cliente</label>
                <select class="form-control" id="cliente" required>
                  <option value="">Seleccione un cliente...</option>
                </select>
              </div>
              <div class="form-group">
                <label>Cédula</label>
                <input type="text" class="form-control" id="cliente-cedula" readonly>
              </div>
              <div class="form-group">
                <label>Teléfono</label>
                <input type="text" class="form-control" id="cliente-telefono" readonly>
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" id="cliente-email" readonly>
              </div>
              <div class="form-group">
                <label>Dirección</label>
                <input type="text" class="form-control" id="cliente-direccion" readonly>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Vendedor</label>
                <input type="text" class="form-control" id="vendedor" name="vendedor" value="<?php echo htmlspecialchars($nombreUsuario); ?>" required readonly style="background-color: #f8f9fa; color: #495057;">
                <small class="form-text text-muted">Se llena automáticamente con el usuario logueado</small>
              </div>
              <div class="form-group">
                <label>Fecha</label>
                <input type="date" class="form-control" id="fecha" name="fecha" required>
              </div>
            </div>
          </div>

          <!-- Productos -->
          <div class="form-section">
            <h3><i class="fas fa-boxes"></i> Productos</h3>
            <div id="productos-container">
              <!-- Los productos se agregarán dinámicamente aquí -->
            </div>
            <button type="button" class="btn btn-outline" onclick="agregarProducto()">
              <i class="fas fa-plus"></i> Agregar Producto
            </button>
          </div>

          <!-- Totales -->
          <div class="totals-section">
            <div class="totals-grid">
              <div class="total-item">
                <label>Subtotal</label>
                <div class="value" id="subtotal">$0.00</div>
              </div>
              <div class="total-item">
                <label>ITBIS (18%)</label>
                <div class="value" id="itbis">$0.00</div>
              </div>
              <div class="total-item">
                <label>Total</label>
                <div class="value" id="total">$0.00</div>
              </div>
            </div>
          </div>

          <div style="text-align: right; margin-top: 20px;">
            <button type="submit" class="btn btn-success">
              <i class="fas fa-save"></i> Guardar Factura
            </button>
          </div>
        </form>
      </div>

      <!-- TAB: Consultar Facturas -->
      <div id="consultar" class="tab-content">
        <div class="search-section">
          <h3><i class="fas fa-search"></i> Buscar Facturas</h3>
          <div class="search-grid">
            <div class="form-group">
              <label>Cliente</label>
              <input type="text" class="form-control" id="filtro-cliente" placeholder="Buscar por cliente...">
            </div>
            <div class="form-group">
              <label>Producto</label>
              <input type="text" class="form-control" id="filtro-producto" placeholder="Buscar por producto...">
            </div>
            <div class="form-group">
              <label>Fecha</label>
              <input type="date" class="form-control" id="filtro-fecha">
            </div>
            <div class="form-group">
              <button type="button" class="btn btn-primary" onclick="buscarFacturas()">
                <i class="fas fa-search"></i> Buscar
              </button>
            </div>
            <div class="form-group">
              <button type="button" class="btn btn-success" onclick="imprimirTodasFacturas()">
                <i class="fas fa-file-pdf"></i> Imprimir PDF
              </button>
            </div>
          </div>
        </div>

        <div class="table-container">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Cliente</th>
                <th>Productos</th>
                <th>Total</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody id="tabla-facturas">
              <!-- Las facturas se cargarán dinámicamente aquí -->
            </tbody>
          </table>
        </div>

        <div id="loading" class="loading" style="display: none;">
          <i class="fas fa-spinner fa-spin"></i> Cargando facturas...
        </div>

        <div id="no-results" class="no-results" style="display: none;">
          <i class="fas fa-info-circle"></i> No se encontraron facturas
        </div>

        <div class="pagination">
          <button class="btn btn-outline" onclick="paginaAnterior()">
            <i class="fas fa-chevron-left"></i> Anterior
          </button>
          <button class="btn btn-outline" onclick="paginaSiguiente()">
            Siguiente <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="facturacion_new.js?v=<?php echo time(); ?>"></script>
</body>
</html>
