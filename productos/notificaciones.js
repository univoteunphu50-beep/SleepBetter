
function mostrarPopup(mensaje, tipo = 'success') {
    const popup = document.createElement('div');
    popup.innerText = mensaje;
    popup.style.position = 'fixed';
    popup.style.bottom = '20px';
    popup.style.left = '20px';
    popup.style.padding = '10px 20px';
    popup.style.backgroundColor = tipo === 'success' ? '#4CAF50' : '#f44336';
    popup.style.color = '#fff';
    popup.style.borderRadius = '5px';
    popup.style.boxShadow = '0 2px 6px rgba(0,0,0,0.3)';
    popup.style.zIndex = '9999';
    popup.style.opacity = '0.95';
    document.body.appendChild(popup);

    setTimeout(() => {
        popup.remove();
    }, 3000);
}
