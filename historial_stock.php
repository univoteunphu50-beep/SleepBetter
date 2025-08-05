<?php
session_start();
include("conexion.php");
include("funciones_stock.php");

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Movimientos de Stock - Sleep Better</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .movimiento-venta { color: #dc3545; }
        .movimiento-compra { color: #28a745; }
        .movimiento-ajuste { color: #ffc107; }
        .movimiento-devolucion { color: #17a2b8; }
        .movimiento-merma { color: #6c757d; }
        .movimiento-restock { color: #fd7e14; }
        
        .badge-tipo {
            font-size: 0.8em;
            padding: 0.3em 0.6em;
        }
        
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }
        
        .filtros {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-history"></i> Historial de Movimientos de Stock</h2>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>

                <!-- Filtros -->
                <div class="filtros">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="filtro-producto" class="form-label">Producto:</label>
                            <select id="filtro-producto" class="form-select">
                                <option value="">Todos los productos</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filtro-tipo" class="form-label">Tipo de Movimiento:</label>
                            <select id="filtro-tipo" class="form-select">
                                <option value="">Todos los tipos</option>
                                <option value="venta">Venta</option>
                                <option value="compra">Compra</option>
                                <option value="ajuste">Ajuste</option>
                                <option value="devolucion">Devolución</option>
                                <option value="merma">Merma</option>
                                <option value="restock">Restock</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filtro-fecha" class="form-label">Fecha:</label>
                            <input type="date" id="filtro-fecha" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" onclick="cargarHistorial()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de movimientos -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Movimientos de Stock</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Producto</th>
                                        <th>Tipo</th>
                                        <th>Cantidad Anterior</th>
                                        <th>Movimiento</th>
                                        <th>Cantidad Nueva</th>
                                        <th>Motivo</th>
                                        <th>Usuario</th>
                                        <th>Factura</th>
                                    </tr>
                                </thead>
                                <tbody id="historial-tbody">
                                    <!-- Los datos se cargarán aquí -->
                                </tbody>
                            </table>
                        </div>
                        <div id="sin-movimientos" class="text-center text-muted" style="display: none;">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>No se encontraron movimientos con los filtros aplicados.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Cargar productos al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            cargarProductos();
            cargarHistorial();
        });

        function cargarProductos() {
            fetch('productos/listar_productos.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('filtro-producto');
                    select.innerHTML = '<option value="">Todos los productos</option>';
                    
                    data.forEach(producto => {
                        const option = document.createElement('option');
                        option.value = producto.id;
                        option.textContent = producto.nombre;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error al cargar productos:', error);
                });
        }

        function cargarHistorial() {
            const producto = document.getElementById('filtro-producto').value;
            const tipo = document.getElementById('filtro-tipo').value;
            const fecha = document.getElementById('filtro-fecha').value;

            const params = new URLSearchParams();
            if (producto) params.append('producto', producto);
            if (tipo) params.append('tipo', tipo);
            if (fecha) params.append('fecha', fecha);

            fetch(`obtener_historial_stock.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    mostrarHistorial(data);
                })
                .catch(error => {
                    console.error('Error al cargar historial:', error);
                    mostrarHistorial([]);
                });
        }

        function mostrarHistorial(movimientos) {
            const tbody = document.getElementById('historial-tbody');
            const sinMovimientos = document.getElementById('sin-movimientos');

            if (movimientos.length === 0) {
                tbody.innerHTML = '';
                sinMovimientos.style.display = 'block';
                return;
            }

            sinMovimientos.style.display = 'none';
            tbody.innerHTML = '';

            movimientos.forEach(movimiento => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${formatearFecha(movimiento.fecha_movimiento)}</td>
                    <td>${movimiento.nombre_producto}</td>
                    <td>
                        <span class="badge badge-tipo movimiento-${movimiento.tipo_movimiento}">
                            ${getTipoText(movimiento.tipo_movimiento)}
                        </span>
                    </td>
                    <td class="text-center">${movimiento.cantidad_anterior}</td>
                    <td class="text-center ${getClaseMovimiento(movimiento.tipo_movimiento)}">
                        ${getSimboloMovimiento(movimiento.tipo_movimiento)}${movimiento.cantidad_movimiento}
                    </td>
                    <td class="text-center">${movimiento.cantidad_nueva}</td>
                    <td>${movimiento.motivo || '-'}</td>
                    <td>${movimiento.usuario || '-'}</td>
                    <td class="text-center">
                        ${movimiento.numero_factura ? `<span class="badge bg-info">#${movimiento.numero_factura}</span>` : '-'}
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function formatearFecha(fecha) {
            return new Date(fecha).toLocaleString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function getTipoText(tipo) {
            const tipos = {
                'venta': 'Venta',
                'compra': 'Compra',
                'ajuste': 'Ajuste',
                'devolucion': 'Devolución',
                'merma': 'Merma',
                'restock': 'Restock'
            };
            return tipos[tipo] || tipo;
        }

        function getClaseMovimiento(tipo) {
            const clases = {
                'venta': 'text-danger',
                'compra': 'text-success',
                'ajuste': 'text-warning',
                'devolucion': 'text-info',
                'merma': 'text-secondary',
                'restock': 'text-warning'
            };
            return clases[tipo] || '';
        }

        function getSimboloMovimiento(tipo) {
            const simbolos = {
                'venta': '-',
                'compra': '+',
                'ajuste': '=',
                'devolucion': '+',
                'merma': '-',
                'restock': '+'
            };
            return simbolos[tipo] || '';
        }
    </script>
</body>
</html> 