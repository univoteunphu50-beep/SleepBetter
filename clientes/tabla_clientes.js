let clientes = [];
let indice = 0;

function cargarClientes() {
  fetch("consulta_clientes.php")
    .then(response => response.json())
    .then(data => {
      clientes = data;
      mostrarCliente();
    });
}

function mostrarCliente() {
  const cliente = clientes[indice];
  if (cliente) {
    document.getElementById("resultado").innerHTML = `
      <p><strong>Cédula:</strong> ${cliente.cedula}</p>
      <p><strong>Nombre:</strong> ${cliente.nombre}</p>
      <p><strong>Teléfono:</strong> ${cliente.telefono}</p>
      <p><strong>Correo:</strong> ${cliente.email}</p>
      <p><strong>Dirección:</strong> ${cliente.direccion}</p>
      <p><strong>Comentarios:</strong> ${cliente.comentarios}</p>
    `;
  } else {
    document.getElementById("resultado").innerHTML = "<p>No hay resultados.</p>";
  }
}

function buscarCliente(texto) {
  const textoLower = texto.toLowerCase();
  const resultados = clientes.filter(c =>
    c.cedula.toLowerCase().includes(textoLower) ||
    c.nombre.toLowerCase().includes(textoLower) ||
    c.telefono.toLowerCase().includes(textoLower) ||
    c.email.toLowerCase().includes(textoLower)
  );
  if (resultados.length > 0) {
    clientes = resultados;
    indice = 0;
    mostrarCliente();
  } else {
    document.getElementById("resultado").innerHTML = "<p>No se encontraron coincidencias.</p>";
  }
}

document.addEventListener("DOMContentLoaded", () => {
  cargarClientes();

  document.getElementById("buscarCliente").addEventListener("input", (e) => {
    buscarCliente(e.target.value);
  });

  document.querySelector("#btn-consulta").addEventListener("click", () => {
    cargarClientes();
  });

  document.addEventListener("keydown", (e) => {
    if (document.getElementById("consulta").classList.contains("active")) {
      if (e.key === "ArrowRight") {
        if (indice < clientes.length - 1) {
          indice++;
          mostrarCliente();
        }
      } else if (e.key === "ArrowLeft") {
        if (indice > 0) {
          indice--;
          mostrarCliente();
        }
      }
    }
  });
});

