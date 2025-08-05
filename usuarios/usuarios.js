// Variables globales
let usuarios = [];
let modoEdicion = false;

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    cargarUsuarios();
    configurarEventos();
});

// Configurar eventos
function configurarEventos() {
    // Evento de búsqueda
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            buscarUsuarios();
        });
    }

    // Evento del formulario
    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            guardarUsuario();
        });
    }

    // Evento del botón Nuevo Usuario
    const nuevoUsuarioBtn = document.getElementById('nuevoUsuarioBtn');
    if (nuevoUsuarioBtn) {
        nuevoUsuarioBtn.addEventListener('click', function() {
            abrirModal('nuevo');
        });
    }

    // Evento del botón Actualizar
    const actualizarBtn = document.getElementById('actualizarBtn');
    if (actualizarBtn) {
        actualizarBtn.addEventListener('click', function() {
            cargarUsuarios();
        });
    }

    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModal();
        }
    });
}

// Cargar usuarios
async function cargarUsuarios() {
    mostrarLoading(true);
    
    try {
        const response = await fetch('listar_usuarios.php');
        const data = await response.json();
        
        if (data.success) {
            usuarios = data.usuarios;
            mostrarUsuarios(usuarios);
        } else {
            mostrarAlerta('Error al cargar usuarios: ' + data.error, 'error');
        }
    } catch (error) {
        mostrarAlerta('Error de conexión: ' + error.message, 'error');
    } finally {
        mostrarLoading(false);
    }
}

// Mostrar usuarios en la tabla
function mostrarUsuarios(usuariosFiltrados) {
    const tbody = document.getElementById('usuariosTableBody');
    const table = document.getElementById('usuariosTable');
    
    if (!tbody || !table) {
        console.error('No se encontraron los elementos de la tabla');
        return;
    }
    
    if (usuariosFiltrados.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: #6c757d;">No se encontraron usuarios</td></tr>';
        table.style.display = 'table';
        return;
    }
    
    tbody.innerHTML = usuariosFiltrados.map(usuario => `
        <tr>
            <td>${usuario.id}</td>
            <td>${escapeHtml(usuario.usuario)}</td>
            <td>${escapeHtml(usuario.nombre)}</td>
            <td>${escapeHtml(usuario.email)}</td>
            <td>
                <span class="status-badge ${getRolClass(usuario.rol)}">
                    ${getRolText(usuario.rol)}
                </span>
            </td>
            <td>
                <span class="status-badge ${usuario.activo == 1 ? 'status-active' : 'status-inactive'}">
                    ${usuario.activo == 1 ? 'Activo' : 'Inactivo'}
                </span>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-primary btn-sm" onclick="editarUsuario(${usuario.id})" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(${usuario.id})" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    
    table.style.display = 'table';
}

// Buscar usuarios
function buscarUsuarios() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    
    if (searchTerm === '') {
        mostrarUsuarios(usuarios);
        return;
    }
    
    const usuariosFiltrados = usuarios.filter(usuario => 
        usuario.nombre.toLowerCase().includes(searchTerm) ||
        usuario.email.toLowerCase().includes(searchTerm) ||
        usuario.usuario.toLowerCase().includes(searchTerm) ||
        usuario.rol.toLowerCase().includes(searchTerm)
    );
    
    mostrarUsuarios(usuariosFiltrados);
}

// Abrir modal para agregar/editar usuario
function abrirModal(modo, usuario = null) {
    modoEdicion = modo === 'editar';
    const modal = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const title = document.querySelector('#userModal h2');
    
    // Limpiar formulario
    form.reset();
    
    if (modoEdicion && usuario) {
        title.textContent = 'Editar Usuario';
        document.getElementById('userId').value = usuario.id;
        document.getElementById('nombre').value = usuario.nombre;
        document.getElementById('email').value = usuario.email;
        document.getElementById('rol').value = usuario.rol;
        document.getElementById('estado').value = usuario.activo;
        document.getElementById('password').placeholder = 'Dejar vacío para mantener la actual';
        document.getElementById('password').required = false;
    } else {
        title.textContent = 'Nuevo Usuario';
        document.getElementById('userId').value = '';
        document.getElementById('password').placeholder = 'Contraseña';
        document.getElementById('password').required = true;
    }
    
    modal.style.display = 'block';
}

// Cerrar modal
function cerrarModal() {
    document.getElementById('userModal').style.display = 'none';
    modoEdicion = false;
    document.getElementById('password').required = true;
}

// Editar usuario
async function editarUsuario(id) {
    const usuario = usuarios.find(u => u.id == id);
    if (usuario) {
        abrirModal('editar', usuario);
    }
}

// Guardar usuario
async function guardarUsuario() {
    const formData = new FormData(document.getElementById('userForm'));
    
    try {
        const url = modoEdicion ? 'actualizar_usuario.php' : 'guardar_usuario.php';
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarAlerta(data.message, 'success');
            cerrarModal();
            cargarUsuarios();
        } else {
            mostrarAlerta('Error: ' + data.error, 'error');
        }
    } catch (error) {
        mostrarAlerta('Error de conexión: ' + error.message, 'error');
    }
}

// Eliminar usuario
async function eliminarUsuario(id) {
    if (!confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('id', id);
        
        const response = await fetch('eliminar_usuario.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            mostrarAlerta(data.message, 'success');
            cargarUsuarios();
        } else {
            mostrarAlerta('Error: ' + data.error, 'error');
        }
    } catch (error) {
        mostrarAlerta('Error de conexión: ' + error.message, 'error');
    }
}

// Mostrar alerta
function mostrarAlerta(mensaje, tipo) {
    const alertContainer = document.getElementById('alertContainer');
    const alertClass = tipo === 'success' ? 'alert-success' : 'alert-error';
    
    alertContainer.innerHTML = `
        <div class="alert ${alertClass}">
            <span>${mensaje}</span>
            <button onclick="this.parentElement.remove()" style="float: right; background: none; border: none; font-size: 18px; cursor: pointer;">×</button>
        </div>
    `;
    
    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        const alert = alertContainer.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Mostrar/ocultar loading
function mostrarLoading(mostrar) {
    const loading = document.getElementById('loading');
    const table = document.getElementById('usuariosTable');
    
    if (mostrar) {
        loading.style.display = 'block';
        table.style.display = 'none';
    } else {
        loading.style.display = 'none';
    }
}

// Función para escapar HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Función para obtener clase CSS del rol
function getRolClass(rol) {
    switch (rol) {
        case 'admin': return 'status-admin';
        case 'vendedor': return 'status-vendedor';
        default: return 'status-default';
    }
}

// Función para obtener texto del rol
function getRolText(rol) {
    switch (rol) {
        case 'admin': return 'Administrador';
        case 'vendedor': return 'Vendedor';
        default: return rol;
    }
}

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('userModal');
    if (event.target === modal) {
        cerrarModal();
    }
}

// Exponer funciones globalmente para acceso desde el iframe
window.abrirModal = abrirModal;
window.cerrarModal = cerrarModal;
window.editarUsuario = editarUsuario;
window.eliminarUsuario = eliminarUsuario;
window.cargarUsuarios = cargarUsuarios; 