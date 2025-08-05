// Script para forzar que el campo vendedor siempre tenga un valor
(function() {
    'use strict';
    
    // Función para establecer vendedor por defecto
    function establecerVendedorPorDefecto() {
        const vendedorInput = document.getElementById('vendedor');
        if (vendedorInput) {
            if (!vendedorInput.value || vendedorInput.value.trim() === '') {
                // Intentar obtener el usuario logueado
                fetch('obtener_usuario.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.nombre) {
                            vendedorInput.value = data.nombre;
                            console.log('✅ Vendedor establecido automáticamente:', data.nombre);
                        } else {
                            vendedorInput.value = 'Vendedor';
                            console.log('✅ Vendedor establecido por defecto: Vendedor');
                        }
                    })
                    .catch(error => {
                        console.error('Error al obtener usuario:', error);
                        vendedorInput.value = 'Vendedor';
                        console.log('✅ Vendedor establecido por defecto: Vendedor');
                    });
            }
        }
    }
    
    // Función para forzar valor del vendedor
    function forzarVendedor() {
        const vendedorInput = document.getElementById('vendedor');
        if (vendedorInput) {
            if (!vendedorInput.value || vendedorInput.value.trim() === '') {
                vendedorInput.value = 'Vendedor';
                console.log('🔧 Vendedor forzado a: Vendedor');
            }
        }
    }
    
    // Ejecutar cuando se carga la página
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            establecerVendedorPorDefecto();
            
            // Forzar después de un delay
            setTimeout(forzarVendedor, 1000);
            setTimeout(forzarVendedor, 2000);
        });
    } else {
        establecerVendedorPorDefecto();
        setTimeout(forzarVendedor, 1000);
        setTimeout(forzarVendedor, 2000);
    }
    
    // Observar cambios en el DOM para detectar cuando se limpia el formulario
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                const vendedorInput = document.getElementById('vendedor');
                if (vendedorInput && (!vendedorInput.value || vendedorInput.value.trim() === '')) {
                    setTimeout(forzarVendedor, 100);
                }
            }
        });
    });
    
    // Iniciar observación cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            const vendedorInput = document.getElementById('vendedor');
            if (vendedorInput) {
                observer.observe(vendedorInput, { attributes: true, attributeFilter: ['value'] });
            }
        });
    } else {
        const vendedorInput = document.getElementById('vendedor');
        if (vendedorInput) {
            observer.observe(vendedorInput, { attributes: true, attributeFilter: ['value'] });
        }
    }
    
    // Exponer funciones globalmente para debugging
    window.forzarVendedor = forzarVendedor;
    window.establecerVendedorPorDefecto = establecerVendedorPorDefecto;
    
})(); 