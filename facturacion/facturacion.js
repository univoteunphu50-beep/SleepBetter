// Variables globales
let productosDisponibles = [];
let clientesDisponibles = [];
let productosFactura = [];

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando facturación...');
    cargarClientes();
    cargarProductos();
    establecerFechaActual();
    agregarProducto(); // Agregar primer producto por defecto
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
    fetch('../productos/listar_productos.php')
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

// Agregar producto a la factura
function agregarProducto() {
    console.log('Agregando producto...');
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
                            ${productosDisponibles.map(p => `<option value="${p.id}" data-precio="${p.precio}" data-nombre="${p.nombre}">${p.nombre}</option>`).join('')}
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Precio Unitario</label>
                    <div class="price-display" id="precio-${productoId}">$0.00</div>
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
        
        document.getElementById(`precio-${productoId}`).textContent = `$${precio.toFixed(2)}`;
        document.getElementById(`precio-${productoId}`).dataset.precio = precio;
        document.getElementById(`precio-${productoId}`).dataset.nombre = nombre;
        
        actualizarTotales();
    } else {
        document.getElementById(`precio-${productoId}`).textContent = '$0.00';
        document.getElementById(`precio-${productoId}`).dataset.precio = 0;
        document.getElementById(`precio-${productoId}`).dataset.nombre = '';
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
    actualizarTotales();
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
            const vendedor = document.getElementById('vendedor').value;
            const fecha = document.getElementById('fecha').value;
            
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
                    limpiarFormulario();
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
    document.getElementById('form-factura').reset();
    document.getElementById('cliente-cedula').value = '';
    document.getElementById('cliente-telefono').value = '';
    document.getElementById('cliente-email').value = '';
    document.getElementById('cliente-direccion').value = '';
    establecerFechaActual();
    
    // Limpiar productos
    document.getElementById('productos-container').innerHTML = '';
    productosFactura = [];
    agregarProducto();
    actualizarTotales();
}

console.log('Archivo facturacion.js cargado');
