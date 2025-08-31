// Cuando todo el contenido del DOM haya cargado
document.addEventListener('DOMContentLoaded', () => {
  // Obtengo el input de búsqueda
  const searchInput = document.getElementById('searchInput');
  // Select para ordenar por fecha
  const dateFilter = document.getElementById('dateFilter');
  // Todas las filas de la tabla con la clase 'record-row'
  const recordRows = Array.from(document.querySelectorAll('.record-row'));
  // Contenedor principal donde están los reportes
  const contenedor = document.getElementById('tabla-reportes');

  // Función para normalizar el texto, eliminando tildes y convirtiendo a minúscula
  function normalize(text) {
    return text.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  }

  // Esta función extrae la fecha de una fila y la convierte a un objeto Date
  function obtenerFecha(row) {
    const fechaText = row.querySelector('[data-label="Fecha"]').textContent.trim();
    const partes = fechaText.split('/'); // Formato original: DD/MM/YYYY
    const fechaISO = `${partes[2]}-${partes[1].padStart(2, '0')}-${partes[0].padStart(2, '0')}`;
    return new Date(fechaISO);
  }

  // Función que filtra los registros por texto y los ordena por fecha
  function filtrarYOrdenar() {
    const searchTerm = normalize(searchInput.value);
    const order = dateFilter.value;

    // Filtro las filas según lo que escribo
    const visibles = recordRows.filter(row => {
      const text = normalize(row.textContent);
      const coincide = text.includes(searchTerm);
      row.style.display = coincide ? '' : 'none'; // Muestro o escondo según búsqueda
      return coincide;
    });

    // Si hay que ordenar (recientes o antiguos)
    if (order === 'recent' || order === 'oldest') {
      visibles.sort((a, b) => {
        const fechaA = obtenerFecha(a);
        const fechaB = obtenerFecha(b);
        return order === 'recent' ? fechaB - fechaA : fechaA - fechaB;
      });

      // Reordeno las filas en el contenedor según el orden aplicado
      visibles.forEach(row => contenedor.appendChild(row));
    }
  }

  // Evento para cuando escribo algo en el input
  searchInput.addEventListener('input', filtrarYOrdenar);
  // Evento para cuando cambio el select de fechas
  dateFilter.addEventListener('change', filtrarYOrdenar);
});


// -------- FUNCIONES PARA EL MODAL DE DETALLES --------

// Abre el modal cambiando su estilo a "block"
function abrirModal() {
  document.getElementById("modalDetalles").style.display = "block";
}

// Cierra el modal ocultándolo
function cerrarModal() {
  document.getElementById("modalDetalles").style.display = "none";
}

// Si hago clic fuera del modal, también se cierra
window.onclick = function(event) {
  const modal = document.getElementById("modalDetalles");
  if (event.target === modal) {
    modal.style.display = "none";
  }
};


// -------- FUNCIÓN QUE CONFIRMA EL ENVÍO DE COMENTARIO --------
function confirmarEnvioComentario() {
  enviarComentario(); // Llama a la función para enviar el comentario

  // Cierra el modal del comentario manualmente
  const modal = document.getElementById('modalComentario');
  if (modal) {
    modal.style.display = 'none'; // Lo oculto (como lo abrí sin Bootstrap)
  }

  // Alerta de éxito usando SweetAlert
  Swal.fire({
    text: '¡Reporte enviado con éxito!',
    icon: 'success',
    timer: 1500,
    showConfirmButton: false,
    customClass: {
      popup: 'rounded-4'
    }
  });
}

// Envía el comentario y limpia el input
function enviarComentario() {
  const comentario = document.getElementById("comentarioInput").value;
  console.log("Comentario enviado:", comentario); // Solo lo imprimo en consola

  // Limpia el textarea
  document.getElementById("comentarioInput").value = '';
}


// -------- CAMBIO DE COLOR DEL SELECT SEGÚN EL ESTADO --------
document.addEventListener('DOMContentLoaded', () => {
  const estadoSelect = document.getElementById('estadoSelect');

  if (estadoSelect) {
    aplicarColorEstado(estadoSelect.value); // Aplico color al valor inicial

    // Cuando cambio el estado, actualizo el color
    estadoSelect.addEventListener('change', function () {
      aplicarColorEstado(this.value);
    });
  }

  // Función que aplica el color de clase según el valor del select
  function aplicarColorEstado(valor) {
    estadoSelect.classList.remove('estado-en_espera', 'estado-en_proceso', 'estado-atendido');

    if (valor === 'en_espera') {
      estadoSelect.classList.add('estado-en_espera');
    } else if (valor === 'en_proceso') {
      estadoSelect.classList.add('estado-en_proceso');
    } else if (valor === 'atendido') {
      estadoSelect.classList.add('estado-atendido');
    }
  }
});
