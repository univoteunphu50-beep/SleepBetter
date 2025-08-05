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
    <title>Alertas de Restock - Sleep Better</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .alerta-critica {
            border-left: 4px solid #dc3545;
            background-color: #fff5f5;
        }
        
        .alerta-baja {
            border-left: 4px solid #ffc107;
            background-color: #fffbf0;
        }
        
        .stock-critico {
            color: #dc3545;
            font-weight: bold;
        }
        
        .stock-bajo {
            color: #ffc107;
            font-weight: bold;
        }
        
        .card-alerta {
            transition: all 0.3s ease;
        }
        
        .card-alerta:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .badge-estado {
            font-size: 0.8em;
        }
        
        .btn-accion {
            font-size: 0.9em;
            padding: 0.25rem 0.5rem;
        }
    </style>
</body>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-exclamation-triangle"></i> Alertas de Restock</h2>
                    <div>
                        <button type="button" class="btn btn-success me-2" onclick="verificarTodasAlertas()">
                            <i class="fas fa-sync-alt"></i> Verificar Alertas
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h4 id="contador-criticas">0</h4>
                                <p class="mb-0">Críticas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h4 id="contador-bajas">0</h4>
                                <p class="mb-0">Bajas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h4 id="contador-resueltas">0</h4>
                                <p class="mb-0">Resueltas</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h4 id="contador-total">0</h4>
                                <p class="mb-0">Total</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Filtros</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="filtro-estado" class="form-label">Estado:</label>
                                <select id="filtro-estado" class="form-select" onchange="cargarAlertas()">
                                    <option value="">Todos los estados</option>
                                    <option value="activa">Activas</option>
                                    <option value="resuelta">Resueltas</option>
                                    <option value="ignorada">Ignoradas</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtro-producto" class="form-label">Producto:</label>
                                <select id="filtro-producto" class="form-select" onchange="cargarAlertas()">
                                    <option value="">Todos los productos</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="filtro-fecha" class="form-label">Fecha:</label>
                                <input type="date" id="filtro-fecha" class="form-control" onchange="cargarAlertas()">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-primary w-100" onclick="cargarAlertas()">
                                    <i class="fas fa-search"></i> Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de alertas -->
                <div id="alertas-container">
                    <!-- Las alertas se cargarán aquí -->
                </div>

                <div id="sin-alertas" class="text-center text-muted" style="display: none;">
                    <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                    <h4>¡Excelente!</h4>
                    <p>No hay alertas de restock pendientes.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para resolver alerta -->
    <div class="modal fade" id="modalResolverAlerta" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Resolver Alerta de Restock</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que quieres marcar esta alerta como resuelta?</p>
                    <p><strong>Producto:</strong> <span id="modal-producto"></span></p>
                    <p><strong>Stock actual:</strong> <span id="modal-stock"></span></p>
                    <p><strong>Límite de restock:</strong> <span id="modal-restock"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="resolverAlerta()">Resolver</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let alertaActual = null;
        let modalResolver = null;

        document.addEventListener('DOMContentLoaded', function() {
            modalResolver = new bootstrap.Modal(document.getElementById('modalResolverAlerta'));
            cargarProductos();
            cargarAlertas();
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

        function cargarAlertas() {
            const estado = document.getElementById('filtro-estado').value;
            const producto = document.getElementById('filtro-producto').value;
            const fecha = document.getElementById('filtro-fecha').value;

            const params = new URLSearchParams();
            if (estado) params.append('estado', estado);
            if (producto) params.append('producto', producto);
            if (fecha) params.append('fecha', fecha);

            fetch(`obtener_alertas_restock.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    mostrarAlertas(data);
                    actualizarContadores(data);
                })
                .catch(error => {
                    console.error('Error al cargar alertas:', error);
                    mostrarAlertas([]);
                });
        }

        function mostrarAlertas(alertas) {
            const container = document.getElementById('alertas-container');
            const sinAlertas = document.getElementById('sin-alertas');

            if (alertas.length === 0) {
                container.innerHTML = '';
                sinAlertas.style.display = 'block';
                return;
            }

            sinAlertas.style.display = 'none';
            container.innerHTML = '';

            alertas.forEach(alerta => {
                const card = document.createElement('div');
                card.className = `card card-alerta mb-3 ${getClaseAlerta(alerta)}`;
                
                const esCritica = alerta.stock_actual <= 0;
                const esBaja = alerta.stock_actual > 0 && alerta.stock_actual <= alerta.restock_limite;
                
                card.innerHTML = `
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <h6 class="mb-1">${alerta.nombre_producto}</h6>
                                <small class="text-muted">Precio: $${parseFloat(alerta.precio).toFixed(2)}</small>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="badge badge-estado ${esCritica ? 'bg-danger' : 'bg-warning'}">
                                    ${esCritica ? 'Crítica' : 'Baja'}
                                </span>
                            </div>
                            <div class="col-md-2 text-center">
                                <span class="${esCritica ? 'stock-critico' : 'stock-bajo'}">
                                    Stock: ${alerta.stock_actual}
                                </span>
                            </div>
                            <div class="col-md-2 text-center">
                                <small class="text-muted">
                                    Restock: ${alerta.restock_limite}
                                </small>
                            </div>
                            <div class="col-md-2 text-center">
                                <small class="text-muted">
                                    ${formatearFecha(alerta.fecha_alerta)}
                                </small>
                            </div>
                            <div class="col-md-1 text-end">
                                ${alerta.estado === 'activa' ? `
                                    <button class="btn btn-success btn-accion" onclick="abrirModalResolver(${alerta.id}, '${alerta.nombre_producto}', ${alerta.stock_actual}, ${alerta.restock_limite})" title="Resolver">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-secondary btn-accion" onclick="ignorarAlerta(${alerta.id})" title="Ignorar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                ` : `
                                    <span class="badge ${alerta.estado === 'resuelta' ? 'bg-success' : 'bg-secondary'}">
                                        ${alerta.estado === 'resuelta' ? 'Resuelta' : 'Ignorada'}
                                    </span>
                                `}
                            </div>
                        </div>
                    </div>
                `;
                
                container.appendChild(card);
            });
        }

        function getClaseAlerta(alerta) {
            if (alerta.stock_actual <= 0) {
                return 'alerta-critica';
            } else if (alerta.stock_actual <= alerta.restock_limite) {
                return 'alerta-baja';
            }
            return '';
        }

        function actualizarContadores(alertas) {
            let criticas = 0, bajas = 0, resueltas = 0;
            
            alertas.forEach(alerta => {
                if (alerta.estado === 'activa') {
                    if (alerta.stock_actual <= 0) {
                        criticas++;
                    } else {
                        bajas++;
                    }
                } else if (alerta.estado === 'resuelta') {
                    resueltas++;
                }
            });
            
            document.getElementById('contador-criticas').textContent = criticas;
            document.getElementById('contador-bajas').textContent = bajas;
            document.getElementById('contador-resueltas').textContent = resueltas;
            document.getElementById('contador-total').textContent = alertas.length;
        }

        function abrirModalResolver(id, producto, stock, restock) {
            alertaActual = id;
            document.getElementById('modal-producto').textContent = producto;
            document.getElementById('modal-stock').textContent = stock;
            document.getElementById('modal-restock').textContent = restock;
            modalResolver.show();
        }

        function resolverAlerta() {
            if (!alertaActual) return;
            
            fetch('resolver_alerta_restock.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_alerta: alertaActual,
                    accion: 'resolver'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modalResolver.hide();
                    cargarAlertas();
                    mostrarMensaje('Alerta resuelta exitosamente', 'success');
                } else {
                    mostrarMensaje('Error al resolver alerta', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al resolver alerta', 'error');
            });
        }

        function ignorarAlerta(id) {
            if (!confirm('¿Estás seguro de que quieres ignorar esta alerta?')) {
                return;
            }
            
            fetch('resolver_alerta_restock.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id_alerta: id,
                    accion: 'ignorar'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cargarAlertas();
                    mostrarMensaje('Alerta ignorada exitosamente', 'success');
                } else {
                    mostrarMensaje('Error al ignorar alerta', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al ignorar alerta', 'error');
            });
        }

        function verificarTodasAlertas() {
            fetch('verificar_alertas_restock.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        cargarAlertas();
                        mostrarMensaje('Alertas verificadas exitosamente', 'success');
                    } else {
                        mostrarMensaje('Error al verificar alertas', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarMensaje('Error al verificar alertas', 'error');
                });
        }

        function formatearFecha(fecha) {
            return new Date(fecha).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function mostrarMensaje(mensaje, tipo) {
            const alertClass = tipo === 'success' ? 'alert-success' : 'alert-danger';
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 3000);
        }
    </script>
</body>
</html> 