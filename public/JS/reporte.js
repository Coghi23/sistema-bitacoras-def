// Espero a que el DOM esté completamente cargado antes de ejecutar cualquier cosa
document.addEventListener('DOMContentLoaded', () => {
  // Obtengo referencias a los elementos del DOM que voy a usar
  const searchInput = document.getElementById('searchInput'); // Input de búsqueda
  const dateFilter = document.getElementById('dateFilter');   // Select del filtro por fecha
  const recordRows = Array.from(document.querySelectorAll('.record-row')); // Todas las filas de la tabla
  const contenedor = document.getElementById('tabla-reportes'); // Contenedor principal de las filas

  // Función para normalizar texto: lo paso a minúsculas y le quito tildes
  function normalize(text) {
    return text.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
  }

  // Función para convertir la fecha de la fila a un objeto Date
  function obtenerFecha(row) {
    const fechaText = row.querySelector('[data-label="Fecha"]').textContent.trim(); // Ej: "5/5/2025"
    const partes = fechaText.split('/'); // Separo por día/mes/año
    // Formateo la fecha en formato ISO (YYYY-MM-DD) para que JS lo entienda
    const fechaISO = `${partes[2]}-${partes[1].padStart(2, '0')}-${partes[0].padStart(2, '0')}`;
    return new Date(fechaISO); // Retorno la fecha como objeto
  }

  // Función principal que filtra y ordena las filas
  function filtrarYOrdenar() {
    const searchTerm = normalize(searchInput.value); // Obtengo el valor a buscar, ya normalizado
    const order = dateFilter.value; // Valor del select (recent o oldest)

    // Filtro las filas que coinciden con el término de búsqueda
    const visibles = recordRows.filter(row => {
      const text = normalize(row.textContent); // Normalizo el texto completo de la fila
      const coincide = text.includes(searchTerm); // Verifico si incluye el término
      row.style.display = coincide ? '' : 'none'; // Si coincide la muestro, si no la oculto
      return coincide;
    });

    // Si el filtro de fecha está seleccionado, ordeno los resultados visibles
    if (order === 'recent' || order === 'oldest') {
      visibles.sort((a, b) => {
        const fechaA = obtenerFecha(a); // Fecha del primer elemento
        const fechaB = obtenerFecha(b); // Fecha del segundo elemento
        return order === 'recent' ? fechaB - fechaA : fechaA - fechaB; // Ordeno según corresponda
      });

      // Reordeno las filas en el DOM para que se vean en el nuevo orden
      visibles.forEach(row => contenedor.appendChild(row));
    }
  }

  // Eventos que disparan la función de filtrado y ordenamiento
  searchInput.addEventListener('input', filtrarYOrdenar);  // Cada vez que escribo
  dateFilter.addEventListener('change', filtrarYOrdenar);  // Cuando selecciono un orden
});


// ==========================
//        FUNCIONES MODAL
// ==========================

// Esta función se llama cuando se da clic en "Ver Más"
function abrirModal() {
  document.getElementById("modalDetalles").style.display = "block"; // Muestro el modal
}

// Esta función se llama para cerrar el modal (cuando le doy clic al ícono de cerrar)
function cerrarModal() {
  document.getElementById("modalDetalles").style.display = "none"; // Oculto el modal
}

// Esta función detecta si hago clic fuera del modal para cerrarlo automáticamente
window.onclick = function(event) {
  const modal = document.getElementById("modalDetalles");
  if (event.target === modal) {
    modal.style.display = "none"; // Cierro si el clic fue fuera del contenido
  }
};
