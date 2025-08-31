// Este bloque se ejecuta cuando todo el DOM ha cargado
document.addEventListener('DOMContentLoaded', () => {
  // Obtengo los elementos del input de búsqueda, el filtro de fecha y las filas de los reportes
  const searchInput = document.getElementById('searchInput');
  const dateFilter = document.getElementById('dateFilter');
  const recordRows = Array.from(document.querySelectorAll('.record-row'));
  const contenedor = document.getElementById('tabla-reportes');

  // Esta función sirve para normalizar el texto, por ejemplo: quitar tildes y pasar todo a minúsculas
  function normalize(text) {
    return text.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  }

  // Esta función extrae la fecha desde la fila del reporte y la convierte al formato que JS entiende
  function obtenerFecha(row) {
    const fechaText = row.querySelector('[data-label="Fecha"]').textContent.trim();
    const partes = fechaText.split('/'); // El formato original es DD/MM/YYYY
    const fechaISO = `${partes[2]}-${partes[1].padStart(2, '0')}-${partes[0].padStart(2, '0')}`;
    return new Date(fechaISO);
  }

  // Esta función filtra los reportes por búsqueda y ordena por fecha si se elige la opción
  function filtrarYOrdenar() {
    const searchTerm = normalize(searchInput.value); // Lo que escribe el usuario
    const order = dateFilter.value; // Valor seleccionado en el filtro

    // Recorre cada fila para mostrar solo las que coinciden con lo buscado
    const visibles = recordRows.filter(row => {
      const text = normalize(row.textContent); // Normalizo el texto de la fila
      const coincide = text.includes(searchTerm); // Reviso si incluye lo que se busca
      row.style.display = coincide ? '' : 'none'; // Muestro u oculto según coincida
      return coincide;
    });

    // Si hay que ordenar por fecha (reciente o más antiguo)
    if (order === 'recent' || order === 'oldest') {
      visibles.sort((a, b) => {
        const fechaA = obtenerFecha(a);
        const fechaB = obtenerFecha(b);
        return order === 'recent' ? fechaB - fechaA : fechaA - fechaB;
      });

      // Reordeno las filas visibles en el DOM
      visibles.forEach(row => contenedor.appendChild(row));
    }
  }

  // Escucho cuando el usuario escribe en el input o cambia el filtro
  searchInput.addEventListener('input', filtrarYOrdenar);
  dateFilter.addEventListener('change', filtrarYOrdenar);
});

// -------- MODAL --------

// Función para abrir el modal de detalles
function abrirModal() {
  document.getElementById("modalDetalles").style.display = "block";
}

// Función para cerrar el modal
function cerrarModal() {
  document.getElementById("modalDetalles").style.display = "none";
}

// Si el usuario hace clic fuera del modal, también se cierra
window.onclick = function(event) {
  const modal = document.getElementById("modalDetalles");
  if (event.target === modal) {
    modal.style.display = "none";
  }
};

// -------- ELIMINAR REGISTRO --------

// Función que muestra una confirmación con SweetAlert antes de eliminar un registro
function eliminarRegistro() {
  Swal.fire({
    title: '¿Está usted seguro de eliminar los datos?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí',
    cancelButtonText: 'No',
    confirmButtonColor: '#0d6efd',
    cancelButtonColor: '#6c757d',
    customClass: {
      popup: 'rounded-4',
      confirmButton: 'px-4 py-2',
      cancelButton: 'px-4 py-2'
    }
  }).then((result) => {
    // Si el usuario confirma, muestro mensaje de éxito
    if (result.isConfirmed) {
      Swal.fire({
        text: '¡El reporte ha sido eliminado con éxito.!',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
      });
    }
  });
}

// -------- EDITAR REGISTRO --------

// Variable que uso para saber si ya se está editando o no
let editando = false;

// Función para activar o guardar la edición de un reporte
function editarOConfirmar() {
  const prioridadInput = document.getElementById("inputPrioridad");
  const observacionTextarea = document.getElementById("textareaObservaciones");

  if (!editando) {
    // Primer clic: habilita los campos para editar
    prioridadInput.disabled = false;
    observacionTextarea.disabled = false;
    editando = true;
    document.getElementById("btnEditar").innerText = "Guardar"; // Cambio el texto del botón
  } else {
    // Segundo clic: muestra confirmación antes de guardar
    Swal.fire({
      title: '¿Está usted seguro de modificar los datos?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí',
      cancelButtonText: 'No',
      confirmButtonColor: '#0d6efd',
      cancelButtonColor: '#6c757d'
    }).then((result) => {
      if (result.isConfirmed) {
        // Si confirma, muestro mensaje y vuelvo a bloquear los campos
        Swal.fire({
          text: '¡Reporte modificado con éxito!',
          icon: 'success',
          timer: 1500,
          showConfirmButton: false
        });

        prioridadInput.disabled = true;
        observacionTextarea.disabled = true;
        document.getElementById("btnEditar").innerText = "Modificar";
        editando = false;
      }
    });
  }
}
