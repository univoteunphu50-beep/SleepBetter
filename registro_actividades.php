<?php
session_start();
include("conexion.php");
include("funciones_actividades.php");

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Registrar la consulta de actividades
registrarConsulta('actividades', 'Consultó registro de actividades');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Actividades - Sleep Better</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .actividad-crear { color: #28a745; }
        .actividad-actualizar { color: #ffc107; }
        .actividad-eliminar { color: #dc3545; }
        .actividad-consultar { color: #17a2b8; }
        .actividad-login_exitoso { color: #28a745; }
        .actividad-login_fallido { color: #dc3545; }
        .actividad-logout { color: #6c757d; }
        
        .badge-accion {
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
        
        .card-estadistica {
            transition: all 0.3s ease;
        }
        
        .card-estadistica:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .datos-json {
            max-width: 300px;
            word-wrap: break-word;
            font-size: 0.8em;
            background: #f8f9fa;
            padding: 5px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-history"></i> Registro de Actividades</h2>
                    <div>
                        <button type="button" class="btn btn-warning me-2" onclick="exportarActividades()">
                            <i class="fas fa-download"></i> Exportar
                        </button>
                        <button type="button" class="btn btn-danger me-2" onclick="limpiarActividades()">
                            <i class="fas fa-trash"></i> Limpiar Antiguas
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card card-estadistica bg-primary text-white">
                            <div class="card-body text-center">
                                <h4 id="total-actividades">0</h4>
                                <p class="mb-0">Total</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card card-estadistica bg-success text-white">
                            <div class="card-body text-center">
                                <h4 id="usuarios-activos">0</h4>
                                <p class="mb-0">Usuarios Activos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card card-estadistica bg-info text-white">
                            <div class="card-body text-center">
                                <h4 id="logins-exitosos">0</h4>
                                <p class="mb-0">Logins Exitosos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card card-estadistica bg-warning text-white">
                            <div class="card-body text-center">
                                <h4 id="logins-fallidos">0</h4>
                                <p class="mb-0">Logins Fallidos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card card-estadistica bg-secondary text-white">
                            <div class="card-body text-center">
                                <h4 id="creaciones">0</h4>
                                <p class="mb-0">Creaciones</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card card-estadistica bg-dark text-white">
                            <div class="card-body text-center">
                                <h4 id="actualizaciones">0</h4>
                                <p class="mb-0">Actualizaciones</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="filtros">
                    <div class="row">
                        <div class="col-md-2">
                            <label for="filtro-usuario" class="form-label">Usuario:</label>
                            <select id="filtro-usuario" class="form-select">
                                <option value="">Todos los usuarios</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filtro-modulo" class="form-label">Módulo:</label>
                            <select id="filtro-modulo" class="form-select">
                                <option value="">Todos los módulos</option>
                                <option value="autenticacion">Autenticación</option>
                                <option value="usuarios">Usuarios</option>
                                <option value="clientes">Clientes</option>
                                <option value="productos">Productos</option>
                                <option value="facturacion">Facturación</option>
                                <option value="stock">Stock</option>
                                <option value="actividades">Actividades</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filtro-accion" class="form-label">Acción:</label>
                            <select id="filtro-accion" class="form-select">
                                <option value="">Todas las acciones</option>
                                <option value="login_exitoso">Login Exitoso</option>
                                <option value="login_fallido">Login Fallido</option>
                                <option value="logout">Logout</option>
                                <option value="crear">Crear</option>
                                <option value="actualizar">Actualizar</option>
                                <option value="eliminar">Eliminar</option>
                                <option value="consultar">Consultar</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filtro-fecha-desde" class="form-label">Fecha Desde:</label>
                            <input type="date" id="filtro-fecha-desde" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label for="filtro-fecha-hasta" class="form-label">Fecha Hasta:</label>
                            <input type="date" id="filtro-fecha-hasta" class="form-control">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" onclick="cargarActividades()">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabla de actividades -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Actividades del Sistema</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Usuario</th>
                                        <th>Módulo</th>
                                        <th>Acción</th>
                                        <th>Descripción</th>
                                        <th>IP</th>
                                        <th>Datos</th>
                                    </tr>
                                </thead>
                                <tbody id="actividades-tbody">
                                    <!-- Los datos se cargarán aquí -->
                                </tbody>
                            </table>
                        </div>
                        <div id="sin-actividades" class="text-center text-muted" style="display: none;">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>No se encontraron actividades con los filtros aplicados.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ver detalles -->
    <div class="modal fade" id="modalDetalles" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la Actividad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detalles-actividad">
                        <!-- Los detalles se cargarán aquí -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let modalDetalles = null;

        document.addEventListener('DOMContentLoaded', function() {
            modalDetalles = new bootstrap.Modal(document.getElementById('modalDetalles'));
            cargarUsuarios();
            cargarActividades();
            cargarEstadisticas();
        });

        function cargarUsuarios() {
            fetch('usuarios/listar_usuarios.php')
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('filtro-usuario');
                    select.innerHTML = '<option value="">Todos los usuarios</option>';
                    
                    data.forEach(usuario => {
                        const option = document.createElement('option');
                        option.value = usuario.id;
                        option.textContent = usuario.nombre;
                        select.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error al cargar usuarios:', error);
                });
        }

        function cargarActividades() {
            const usuario = document.getElementById('filtro-usuario').value;
            const modulo = document.getElementById('filtro-modulo').value;
            const accion = document.getElementById('filtro-accion').value;
            const fechaDesde = document.getElementById('filtro-fecha-desde').value;
            const fechaHasta = document.getElementById('filtro-fecha-hasta').value;

            const params = new URLSearchParams();
            if (usuario) params.append('usuario_id', usuario);
            if (modulo) params.append('modulo', modulo);
            if (accion) params.append('accion', accion);
            if (fechaDesde) params.append('fecha_desde', fechaDesde);
            if (fechaHasta) params.append('fecha_hasta', fechaHasta);

            fetch(`obtener_actividades.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    mostrarActividades(data);
                })
                .catch(error => {
                    console.error('Error al cargar actividades:', error);
                    mostrarActividades([]);
                });
        }

        function cargarEstadisticas() {
            const fechaDesde = document.getElementById('filtro-fecha-desde').value;
            const fechaHasta = document.getElementById('filtro-fecha-hasta').value;

            const params = new URLSearchParams();
            if (fechaDesde) params.append('fecha_desde', fechaDesde);
            if (fechaHasta) params.append('fecha_hasta', fechaHasta);

            fetch(`obtener_estadisticas_actividades.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total-actividades').textContent = data.total_actividades || 0;
                    document.getElementById('usuarios-activos').textContent = data.usuarios_activos || 0;
                    document.getElementById('logins-exitosos').textContent = data.logins_exitosos || 0;
                    document.getElementById('logins-fallidos').textContent = data.logins_fallidos || 0;
                    document.getElementById('creaciones').textContent = data.creaciones || 0;
                    document.getElementById('actualizaciones').textContent = data.actualizaciones || 0;
                })
                .catch(error => {
                    console.error('Error al cargar estadísticas:', error);
                });
        }

        function mostrarActividades(actividades) {
            const tbody = document.getElementById('actividades-tbody');
            const sinActividades = document.getElementById('sin-actividades');

            if (actividades.length === 0) {
                tbody.innerHTML = '';
                sinActividades.style.display = 'block';
                return;
            }

            sinActividades.style.display = 'none';
            tbody.innerHTML = '';

            actividades.forEach(actividad => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${formatearFecha(actividad.fecha_actividad)}</td>
                    <td>${actividad.usuario_nombre}</td>
                    <td>
                        <span class="badge bg-secondary">
                            ${getModuloText(actividad.modulo)}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-accion actividad-${actividad.accion}">
                            ${getAccionText(actividad.accion)}
                        </span>
                    </td>
                    <td>${actividad.descripcion || '-'}</td>
                    <td>
                        <small class="text-muted">${actividad.ip_address || '-'}</small>
                    </td>
                    <td>
                        ${(actividad.datos_anteriores || actividad.datos_nuevos) ? 
                            `<button class="btn btn-sm btn-outline-info" onclick="verDetalles(${actividad.id})">
                                <i class="fas fa-eye"></i>
                            </button>` : '-'
                        }
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function verDetalles(id) {
            fetch(`obtener_detalles_actividad.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    mostrarDetalles(data);
                    modalDetalles.show();
                })
                .catch(error => {
                    console.error('Error al cargar detalles:', error);
                });
        }

        function mostrarDetalles(actividad) {
            const container = document.getElementById('detalles-actividad');
            
            let datosHtml = '';
            if (actividad.datos_anteriores) {
                datosHtml += `<h6>Datos Anteriores:</h6><pre class="datos-json">${JSON.stringify(JSON.parse(actividad.datos_anteriores), null, 2)}</pre>`;
            }
            if (actividad.datos_nuevos) {
                datosHtml += `<h6>Datos Nuevos:</h6><pre class="datos-json">${JSON.stringify(JSON.parse(actividad.datos_nuevos), null, 2)}</pre>`;
            }

            container.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Usuario:</strong> ${actividad.usuario_nombre}</p>
                        <p><strong>Módulo:</strong> ${getModuloText(actividad.modulo)}</p>
                        <p><strong>Acción:</strong> ${getAccionText(actividad.accion)}</p>
                        <p><strong>Descripción:</strong> ${actividad.descripcion || '-'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Fecha:</strong> ${formatearFecha(actividad.fecha_actividad)}</p>
                        <p><strong>IP:</strong> ${actividad.ip_address || '-'}</p>
                        <p><strong>User Agent:</strong> <small>${actividad.user_agent || '-'}</small></p>
                    </div>
                </div>
                ${datosHtml}
            `;
        }

        function exportarActividades() {
            const usuario = document.getElementById('filtro-usuario').value;
            const modulo = document.getElementById('filtro-modulo').value;
            const accion = document.getElementById('filtro-accion').value;
            const fechaDesde = document.getElementById('filtro-fecha-desde').value;
            const fechaHasta = document.getElementById('filtro-fecha-hasta').value;

            const params = new URLSearchParams();
            if (usuario) params.append('usuario_id', usuario);
            if (modulo) params.append('modulo', modulo);
            if (accion) params.append('accion', accion);
            if (fechaDesde) params.append('fecha_desde', fechaDesde);
            if (fechaHasta) params.append('fecha_hasta', fechaHasta);

            window.open(`exportar_actividades.php?${params.toString()}`, '_blank');
        }

        function limpiarActividades() {
            if (!confirm('¿Estás seguro de que quieres limpiar las actividades antiguas (más de 90 días)?')) {
                return;
            }

            fetch('limpiar_actividades.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Actividades antiguas limpiadas exitosamente');
                    cargarActividades();
                    cargarEstadisticas();
                } else {
                    alert('Error al limpiar actividades: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al limpiar actividades');
            });
        }

        function formatearFecha(fecha) {
            return new Date(fecha).toLocaleString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }

        function getModuloText(modulo) {
            const modulos = {
                'autenticacion': 'Autenticación',
                'usuarios': 'Usuarios',
                'clientes': 'Clientes',
                'productos': 'Productos',
                'facturacion': 'Facturación',
                'stock': 'Stock',
                'actividades': 'Actividades'
            };
            return modulos[modulo] || modulo;
        }

        function getAccionText(accion) {
            const acciones = {
                'login_exitoso': 'Login Exitoso',
                'login_fallido': 'Login Fallido',
                'logout': 'Logout',
                'crear': 'Crear',
                'actualizar': 'Actualizar',
                'eliminar': 'Eliminar',
                'consultar': 'Consultar'
            };
            return acciones[accion] || accion;
        }
    </script>
</body>
</html> 