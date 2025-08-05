// Variables globales
let productosDisponibles = [];
let clientesDisponibles = [];
let productosFactura = [];

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando facturación...');
    cargarClientes();
    cargarProductos().then(() => {
        establecerFechaActual();
        establecerVendedorPorDefecto();
        agregarProducto(); // Agregar primer producto por defecto después de cargar productos
        
        // Asegurar que el vendedor tenga un valor después de un breve delay
        setTimeout(() => {
            establecerVendedorPorDefecto();
        }, 500);
        
        // Forzar que el vendedor tenga un valor después de 1 segundo
        setTimeout(() => {
            const vendedorInput = document.getElementById('vendedor');
            if (!vendedorInput.value || vendedorInput.value.trim() === '') {
                vendedorInput.value = 'Vendedor';
            }
        }, 1000);
    });
});

// Funciones de navegación
function mostrarTab(tabId, element) {
    console.log('Mostrando tab:', tabId);
    
    // Ocultar todos los tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remover clase active de todos los botones
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Mostrar tab seleccionado
    document.getElementById(tabId).classList.add('active');
    
    // Activar botón seleccionado
    if (element) {
        element.classList.add('active');
    }
}

// Cargar clientes
function cargarClientes() {
    console.log('Cargando clientes...');
    fetch('../clientes/listar_clientes.php')
        .then(response => response.json())
        .then(data => {
            console.log('Clientes cargados:', data);
            clientesDisponibles = data;
            const select = document.getElementById('cliente');
            select.innerHTML = '<option value="">Seleccione un cliente...</option>';
            
            data.forEach(cliente => {
                const option = document.createElement('option');
                option.value = cliente.cedula;
                option.textContent = cliente.cliente;
                option.dataset.telefono = cliente.telefono || '';
                option.dataset.email = cliente.email || '';
                option.dataset.direccion = cliente.direccion || '';
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar clientes:', error);
        });
}

// Cargar productos
function cargarProductos() {
    console.log('Cargando productos...');
    return fetch('../productos/listar_productos.php')
        .then(response => response.json())
        .then(data => {
            console.log('Productos cargados:', data);
            productosDisponibles = data;
        })
        .catch(error => {
            console.error('Error al cargar productos:', error);
        });
}

// Completar datos del cliente
document.addEventListener('DOMContentLoaded', function() {
    const clienteSelect = document.getElementById('cliente');
    if (clienteSelect) {
        clienteSelect.addEventListener('change', function() {
            const select = this;
            const option = select.options[select.selectedIndex];
            
            if (option && option.value) {
                document.getElementById('cliente-cedula').value = option.value;
                document.getElementById('cliente-telefono').value = option.dataset.telefono || '';
                document.getElementById('cliente-email').value = option.dataset.email || '';
                document.getElementById('cliente-direccion').value = option.dataset.direccion || '';
            } else {
                document.getElementById('cliente-cedula').value = '';
                document.getElementById('cliente-telefono').value = '';
                document.getElementById('cliente-email').value = '';
                document.getElementById('cliente-direccion').value = '';
            }
        });
    }
});

// Establecer fecha actual
function establecerFechaActual() {
    const fecha = new Date().toISOString().split('T')[0];
    const fechaInput = document.getElementById('fecha');
    if (fechaInput) {
        fechaInput.value = fecha;
    }
}

// Establecer vendedor por defecto
function establecerVendedorPorDefecto() {
    const vendedorInput = document.getElementById('vendedor');
    if (vendedorInput) {
        // Si el campo está vacío o solo tiene espacios, establecer un valor
        if (!vendedorInput.value || vendedorInput.value.trim() === '') {
            // Intentar obtener el usuario logueado
            fetch('obtener_usuario.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.nombre) {
                        vendedorInput.value = data.nombre;
                    } else {
                        // Si no hay usuario logueado, establecer un valor por defecto
                        vendedorInput.value = 'Vendedor';
                    }
                })
                .catch(error => {
                    console.error('Error al obtener usuario:', error);
                    vendedorInput.value = 'Vendedor';
                });
        }
    }
}

// Agregar producto a la factura
function agregarProducto() {
    console.log('Agregando producto...');
    console.log('Productos disponibles:', productosDisponibles);
    
    const container = document.getElementById('productos-container');
    if (!container) {
        console.error('No se encontró el contenedor de productos');
        return;
    }
    
    const productoId = Date.now(); // ID único para el producto
    
    const productoHTML = `
        <div class="product-row" data-producto-id="${productoId}">
            <div class="product-grid">
                <div class="product-info">
                    <div class="form-group">
                        <label>Producto</label>
                        <select class="form-control producto-select" onchange="seleccionarProducto(this, ${productoId})">
                            <option value="">Seleccione un producto...</option>
                            ${productosDisponibles.map(p => `<option value="${p.id}" data-precio="${p.precio}" data-nombre="${p.nombre}" data-stock="${p.stock}">${p.nombre} (Stock: ${p.stock})</option>`).join('')}
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Precio Unitario</label>
                    <div class="price-display" id="precio-${productoId}">$0.00</div>
                    <div class="stock-display" id="stock-${productoId}" style="font-size: 0.9rem; color: #6c757d; margin-top: 5px;">Stock: 0</div>
                </div>
                
                <div class="form-group">
                    <label>Cantidad</label>
                    <div class="quantity-control">
                        <button type="button" class="quantity-btn" onclick="cambiarCantidad(${productoId}, -1)">-</button>
                        <input type="number" class="quantity-input" id="cantidad-${productoId}" value="1" min="1" onchange="actualizarCantidad(${productoId})">
                        <button type="button" class="quantity-btn" onclick="cambiarCantidad(${productoId}, 1)">+</button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>ITBIS</label>
                    <div class="checkbox-group">
                        <input type="checkbox" id="itbis-${productoId}" onchange="actualizarTotales()">
                        <label for="itbis-${productoId}">Aplicar ITBIS</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Descuento (%)</label>
                    <input type="number" class="form-control" id="descuento-${productoId}" value="0" min="0" max="100" onchange="actualizarTotales()">
                </div>
                
                <div class="form-group">
                    <label>Total</label>
                    <div class="total-display" id="total-${productoId}">$0.00</div>
                </div>
                
                <div class="form-group">
                    <button type="button" class="remove-btn" onclick="eliminarProducto(${productoId})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', productoHTML);
    productosFactura.push(productoId);
    console.log('Producto agregado, ID:', productoId);
}

// Seleccionar producto
function seleccionarProducto(select, productoId) {
    const option = select.options[select.selectedIndex];
    if (option && option.value) {
        const precio = parseFloat(option.dataset.precio);
        const nombre = option.dataset.nombre;
        const stock = parseInt(option.dataset.stock) || 0;
        
        document.getElementById(`precio-${productoId}`).textContent = `$${precio.toFixed(2)}`;
        document.getElementById(`precio-${productoId}`).dataset.precio = precio;
        document.getElementById(`precio-${productoId}`).dataset.nombre = nombre;
        document.getElementById(`precio-${productoId}`).dataset.stock = stock;
        
        // Mostrar stock disponible
        const stockElement = document.getElementById(`stock-${productoId}`);
        if (stockElement) {
            stockElement.textContent = `Stock: ${stock}`;
            stockElement.style.color = stock > 0 ? '#28a745' : '#dc3545';
        }
        
        // Validar cantidad inicial
        const cantidadInput = document.getElementById(`cantidad-${productoId}`);
        if (cantidadInput && stock < parseInt(cantidadInput.value)) {
            cantidadInput.value = Math.min(cantidadInput.value, stock);
            mostrarAlertaStock(productoId, stock);
        }
        
        actualizarTotales();
    } else {
        document.getElementById(`precio-${productoId}`).textContent = '$0.00';
        document.getElementById(`precio-${productoId}`).dataset.precio = 0;
        document.getElementById(`precio-${productoId}`).dataset.nombre = '';
        document.getElementById(`precio-${productoId}`).dataset.stock = 0;
        
        const stockElement = document.getElementById(`stock-${productoId}`);
        if (stockElement) {
            stockElement.textContent = 'Stock: 0';
            stockElement.style.color = '#dc3545';
        }
        
        actualizarTotales();
    }
}

// Cambiar cantidad
function cambiarCantidad(productoId, delta) {
    const input = document.getElementById(`cantidad-${productoId}`);
    const nuevaCantidad = Math.max(1, parseInt(input.value) + delta);
    input.value = nuevaCantidad;
    actualizarTotales();
}

// Actualizar cantidad
function actualizarCantidad(productoId) {
    const input = document.getElementById(`cantidad-${productoId}`);
    if (input.value < 1) input.value = 1;
    
    // Validar stock disponible
    const precioElement = document.getElementById(`precio-${productoId}`);
    const stockDisponible = parseInt(precioElement.dataset.stock) || 0;
    const cantidadSolicitada = parseInt(input.value) || 0;
    
    if (cantidadSolicitada > stockDisponible) {
        input.value = stockDisponible;
        mostrarAlertaStock(productoId, stockDisponible);
    }
    
    actualizarTotales();
}

// Mostrar alerta de stock insuficiente
function mostrarAlertaStock(productoId, stockDisponible) {
    const precioElement = document.getElementById(`precio-${productoId}`);
    const nombreProducto = precioElement.dataset.nombre || 'Producto';
    
    // Crear o actualizar alerta
    let alerta = document.getElementById(`alerta-stock-${productoId}`);
    if (!alerta) {
        alerta = document.createElement('div');
        alerta.id = `alerta-stock-${productoId}`;
        alerta.className = 'alerta-stock';
        alerta.style.cssText = `
            background-color: #f8d7da;
            color: #721c24;
            padding: 8px 12px;
            border-radius: 5px;
            margin-top: 5px;
            font-size: 0.9rem;
            border: 1px solid #f5c6cb;
        `;
        
        const productoRow = document.querySelector(`[data-producto-id="${productoId}"]`);
        if (productoRow) {
            productoRow.appendChild(alerta);
        }
    }
    
    alerta.textContent = `⚠️ Stock insuficiente. Disponible: ${stockDisponible}`;
    
    // Ocultar alerta después de 3 segundos
    setTimeout(() => {
        if (alerta && alerta.parentNode) {
            alerta.remove();
        }
    }, 3000);
}

// Eliminar producto
function eliminarProducto(productoId) {
    const elemento = document.querySelector(`[data-producto-id="${productoId}"]`);
    if (elemento) {
        elemento.remove();
        productosFactura = productosFactura.filter(id => id !== productoId);
        actualizarTotales();
    }
}

// Actualizar totales
function actualizarTotales() {
    let subtotal = 0;
    let itbis = 0;
    
    productosFactura.forEach(productoId => {
        const precioElement = document.getElementById(`precio-${productoId}`);
        const cantidadElement = document.getElementById(`cantidad-${productoId}`);
        const descuentoElement = document.getElementById(`descuento-${productoId}`);
        const itbisElement = document.getElementById(`itbis-${productoId}`);
        const totalElement = document.getElementById(`total-${productoId}`);
        
        if (precioElement && cantidadElement && descuentoElement && itbisElement && totalElement) {
            const precio = parseFloat(precioElement.dataset.precio) || 0;
            const cantidad = parseInt(cantidadElement.value) || 0;
            const descuento = parseFloat(descuentoElement.value) || 0;
            const aplicarItbis = itbisElement.checked;
            
            const subtotalProducto = precio * cantidad * (1 - descuento / 100);
            const itbisProducto = aplicarItbis ? subtotalProducto * 0.18 : 0;
            const totalProducto = subtotalProducto + itbisProducto;
            
            totalElement.textContent = `$${totalProducto.toFixed(2)}`;
            
            subtotal += subtotalProducto;
            itbis += itbisProducto;
        }
    });
    
    const total = subtotal + itbis;
    
    document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('itbis').textContent = `$${itbis.toFixed(2)}`;
    document.getElementById('total').textContent = `$${total.toFixed(2)}`;
}

// Guardar factura
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-factura');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Enviando factura...');
            
            const cliente = document.getElementById('cliente').value;
            let vendedor = document.getElementById('vendedor').value;
            const fecha = document.getElementById('fecha').value;
            
            // Asegurar que el vendedor tenga un valor
            if (!vendedor || vendedor.trim() === '') {
                vendedor = 'Vendedor';
                document.getElementById('vendedor').value = vendedor;
            }
            
            if (!cliente) {
                alert('Debe seleccionar un cliente');
                return;
            }
            
            if (!vendedor) {
                alert('Debe ingresar el vendedor');
                return;
            }
            
            if (!fecha) {
                alert('Debe seleccionar una fecha');
                return;
            }
            
            // Recopilar productos
            const productos = [];
            productosFactura.forEach(productoId => {
                const precioElement = document.getElementById(`precio-${productoId}`);
                const cantidadElement = document.getElementById(`cantidad-${productoId}`);
                const descuentoElement = document.getElementById(`descuento-${productoId}`);
                const itbisElement = document.getElementById(`itbis-${productoId}`);
                
                if (precioElement && precioElement.dataset.precio > 0) {
                    productos.push({
                        id: productoId,
                        nombre: precioElement.dataset.nombre,
                        precio: parseFloat(precioElement.dataset.precio),
                        cantidad: parseInt(cantidadElement.value),
                        descuento: parseFloat(descuentoElement.value),
                        itebis: itbisElement.checked
                    });
                }
            });
            
            if (productos.length === 0) {
                alert('Debe agregar al menos un producto');
                return;
            }
            
            // Validar stock antes de enviar
            const productosSinStock = [];
            productos.forEach(producto => {
                const precioElement = document.getElementById(`precio-${producto.id}`);
                const stockDisponible = parseInt(precioElement.dataset.stock) || 0;
                
                if (producto.cantidad > stockDisponible) {
                    productosSinStock.push({
                        nombre: producto.nombre,
                        stockDisponible: stockDisponible,
                        cantidadSolicitada: producto.cantidad
                    });
                }
            });
            
            if (productosSinStock.length > 0) {
                let mensajeError = 'Stock insuficiente para los siguientes productos:\n\n';
                productosSinStock.forEach(producto => {
                    mensajeError += `• ${producto.nombre}: Disponible ${producto.stockDisponible}, Solicitado ${producto.cantidadSolicitada}\n`;
                });
                mensajeError += '\nPor favor, ajuste las cantidades antes de continuar.';
                alert(mensajeError);
                return;
            }
            
            // Enviar datos
            const formData = new FormData();
            formData.append('cliente', cliente);
            formData.append('vendedor', vendedor);
            formData.append('fecha', fecha);
            formData.append('productos', JSON.stringify(productos));
            
            fetch('facturar.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log('Respuesta:', data);
                if (data.includes('Factura guardada')) {
                    alert('Factura guardada exitosamente');
                    // Recargar la página automáticamente después de guardar
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    alert('Error al guardar la factura');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al guardar la factura');
            });
        });
    }
});

// Limpiar formulario
function limpiarFormulario() {
    // Limpiar solo los campos específicos en lugar de resetear todo el formulario
    document.getElementById('cliente').value = '';
    document.getElementById('cliente-cedula').value = '';
    document.getElementById('cliente-telefono').value = '';
    document.getElementById('cliente-email').value = '';
    document.getElementById('cliente-direccion').value = '';
    
    // NO limpiar el vendedor - mantener el valor actual
    // document.getElementById('vendedor').value = ''; // COMENTADO
    
    establecerFechaActual();
    
    // Limpiar productos
    document.getElementById('productos-container').innerHTML = '';
    productosFactura = [];
    agregarProducto();
    actualizarTotales();
}

// Limpiar formulario SIN tocar el vendedor
function limpiarFormularioSinVendedor() {
    // Limpiar solo los campos específicos en lugar de resetear todo el formulario
    document.getElementById('cliente').value = '';
    document.getElementById('cliente-cedula').value = '';
    document.getElementById('cliente-telefono').value = '';
    document.getElementById('cliente-email').value = '';
    document.getElementById('cliente-direccion').value = '';
    
    // NO limpiar el vendedor - mantener el valor actual
    // document.getElementById('vendedor').value = ''; // COMENTADO
    
    establecerFechaActual();
    
    // Limpiar productos
    document.getElementById('productos-container').innerHTML = '';
    productosFactura = [];
    agregarProducto();
    actualizarTotales();
    
    // Asegurar que el vendedor tenga un valor válido después de limpiar
    setTimeout(() => {
        const vendedorInput = document.getElementById('vendedor');
        if (!vendedorInput.value || vendedorInput.value.trim() === '') {
            establecerVendedorPorDefecto();
        }
    }, 100);
}

// Variables para consulta de facturas
let facturasCargadas = [];
let paginaActual = 1;
const facturasPorPagina = 10;

// Buscar facturas
function buscarFacturas() {
    console.log('Buscando facturas...');
    
    const cliente = document.getElementById('filtro-cliente').value;
    const producto = document.getElementById('filtro-producto').value;
    const fecha = document.getElementById('filtro-fecha').value;
    
    const params = new URLSearchParams();
    if (cliente) params.append('cliente', cliente);
    if (producto) params.append('producto', producto);
    if (fecha) params.append('fecha', fecha);
    
    mostrarLoading();
    
    fetch(`consultar_facturas.php?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            console.log('Facturas cargadas:', data);
            facturasCargadas = data;
            paginaActual = 1;
            renderizarFacturas();
            ocultarLoading();
        })
        .catch(error => {
            console.error('Error al cargar facturas:', error);
            ocultarLoading();
            mostrarNoResultados();
        });
}

// Renderizar facturas
function renderizarFacturas() {
    const tabla = document.getElementById('tabla-facturas');
    const inicio = (paginaActual - 1) * facturasPorPagina;
    const fin = inicio + facturasPorPagina;
    const facturasPagina = facturasCargadas.slice(inicio, fin);
    
    if (facturasPagina.length === 0) {
        mostrarNoResultados();
        return;
    }
    
    ocultarNoResultados();
    
    tabla.innerHTML = '';
    
    facturasPagina.forEach(factura => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${factura.id}</td>
            <td>${formatearFecha(factura.fecha)}</td>
            <td>${factura.cliente}</td>
            <td>${factura.productos}</td>
            <td>$${parseFloat(factura.total).toFixed(2)}</td>
            <td>
                <button class="btn btn-outline" onclick="verFactura(${factura.id})">
                    <i class="fas fa-eye"></i> Ver
                </button>
            </td>
        `;
        tabla.appendChild(row);
    });
}

// Navegación de páginas
function paginaAnterior() {
    if (paginaActual > 1) {
        paginaActual--;
        renderizarFacturas();
    }
}

function paginaSiguiente() {
    const totalPaginas = Math.ceil(facturasCargadas.length / facturasPorPagina);
    if (paginaActual < totalPaginas) {
        paginaActual++;
        renderizarFacturas();
    }
}

// Funciones de UI
function mostrarLoading() {
    document.getElementById('loading').style.display = 'block';
    document.getElementById('tabla-facturas').innerHTML = '';
}

function ocultarLoading() {
    document.getElementById('loading').style.display = 'none';
}

function mostrarNoResultados() {
    document.getElementById('no-results').style.display = 'block';
    document.getElementById('tabla-facturas').innerHTML = '';
}

function ocultarNoResultados() {
    document.getElementById('no-results').style.display = 'none';
}

// Formatear fecha
function formatearFecha(fecha) {
    if (!fecha) return 'Fecha no disponible';
    const date = new Date(fecha);
    if (isNaN(date.getTime())) return 'Fecha inválida';
    return date.toLocaleDateString('es-ES');
}

// Ver factura detallada
function verFactura(id) {
    console.log('Obteniendo detalles de factura:', id);
    
    fetch(`ver_factura.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            
            mostrarModalFactura(data);
        })
        .catch(error => {
            console.error('Error al obtener factura:', error);
            alert('Error al obtener los detalles de la factura');
        });
}

// Mostrar modal con detalles de factura
function mostrarModalFactura(factura) {
    const modalHTML = `
        <div id="modal-factura" style="
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        ">
            <div style="
                background: white;
                padding: 30px;
                border-radius: 15px;
                max-width: 800px;
                max-height: 90vh;
                overflow-y: auto;
                position: relative;
            ">
                <button onclick="cerrarModalFactura()" style="
                    position: absolute;
                    top: 15px;
                    right: 20px;
                    background: #dc3545;
                    color: white;
                    border: none;
                    border-radius: 50%;
                    width: 30px;
                    height: 30px;
                    cursor: pointer;
                    font-size: 16px;
                ">×</button>
                
                <div style="text-align: center; margin-bottom: 20px;">
                    <h2 style="color: #2c3e50; margin-bottom: 10px;">Factura #${factura.numero_factura || factura.id}</h2>
                    <p style="color: #6c757d;">Fecha: ${formatearFecha(factura.fecha_factura)}</p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <h4 style="color: #2c3e50; margin-bottom: 10px;">Datos del Cliente</h4>
                        <p><strong>Nombre:</strong> ${factura.cliente || 'No especificado'}</p>
                    </div>
                    <div>
                        <h4 style="color: #2c3e50; margin-bottom: 10px;">Datos de la Factura</h4>
                        <p><strong>Vendedor:</strong> ${factura.vendedor}</p>
                        <p><strong>Subtotal:</strong> $${parseFloat(factura.subtotal).toFixed(2)}</p>
                        <p><strong>ITBIS:</strong> $${parseFloat(factura.itbis).toFixed(2)}</p>
                        <p><strong>Total:</strong> $${parseFloat(factura.total).toFixed(2)}</p>
                    </div>
                </div>
                
                <div>
                    <h4 style="color: #2c3e50; margin-bottom: 15px;">Productos</h4>
                    <table style="
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    ">
                        <thead>
                            <tr style="background: #667eea; color: white;">
                                <th style="padding: 12px; text-align: left;">Producto</th>
                                <th style="padding: 12px; text-align: center;">Precio</th>
                                <th style="padding: 12px; text-align: center;">Cantidad</th>
                                <th style="padding: 12px; text-align: center;">Descuento</th>
                                <th style="padding: 12px; text-align: center;">ITBIS</th>
                                <th style="padding: 12px; text-align: center;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${factura.productos.map(producto => `
                                <tr style="border-bottom: 1px solid #e9ecef;">
                                    <td style="padding: 12px;">${producto.nombre}</td>
                                    <td style="padding: 12px; text-align: center;">$${parseFloat(producto.precio).toFixed(2)}</td>
                                    <td style="padding: 12px; text-align: center;">${producto.cantidad}</td>
                                    <td style="padding: 12px; text-align: center;">${parseFloat(producto.descuento).toFixed(2)}%</td>
                                    <td style="padding: 12px; text-align: center;">${producto.itebis ? 'Sí' : 'No'}</td>
                                    <td style="padding: 12px; text-align: center; font-weight: bold;">$${parseFloat(producto.total || 0).toFixed(2)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                
                <div style="text-align: center; margin-top: 20px;">
                    <button onclick="imprimirFactura(${factura.id})" style="
                        background: #28a745;
                        color: white;
                        border: none;
                        padding: 12px 25px;
                        border-radius: 10px;
                        cursor: pointer;
                        font-weight: 600;
                        margin-right: 10px;
                    "><i class="fas fa-file-pdf"></i> Imprimir PDF</button>
                    <button onclick="cerrarModalFactura()" style="
                        background: #667eea;
                        color: white;
                        border: none;
                        padding: 12px 25px;
                        border-radius: 10px;
                        cursor: pointer;
                        font-weight: 600;
                    ">Cerrar</button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
}

// Cerrar modal de factura
function cerrarModalFactura() {
    const modal = document.getElementById('modal-factura');
    if (modal) {
        modal.remove();
    }
}

// Imprimir factura específica
function imprimirFactura(id) {
    console.log('Imprimiendo factura:', id);
    window.open(`imprimir_factura.php?id=${id}`, '_blank');
}

// Imprimir todas las facturas filtradas
function imprimirTodasFacturas() {
    console.log('Imprimiendo todas las facturas');
    
    // Obtener los filtros actuales
    const cliente = document.getElementById('filtro-cliente').value;
    const producto = document.getElementById('filtro-producto').value;
    const fecha = document.getElementById('filtro-fecha').value;
    
    // Construir la URL con los filtros
    let url = 'imprimir_todas_facturas.php?';
    const params = [];
    
    if (cliente) params.push(`cliente=${encodeURIComponent(cliente)}`);
    if (producto) params.push(`producto=${encodeURIComponent(producto)}`);
    if (fecha) params.push(`fecha=${encodeURIComponent(fecha)}`);
    
    url += params.join('&');
    
    window.open(url, '_blank');
}

// Cargar facturas al cambiar al tab de consulta
document.addEventListener('DOMContentLoaded', function() {
    // Cargar facturas cuando se muestra el tab de consulta
    const consultarTab = document.querySelector('[onclick="mostrarTab(\'consultar\', this)"]');
    if (consultarTab) {
        consultarTab.addEventListener('click', function() {
            setTimeout(() => {
                buscarFacturas();
            }, 100);
        });
    }
});

console.log('Archivo facturacion_new.js cargado'); 