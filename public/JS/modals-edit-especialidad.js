document.addEventListener("DOMContentLoaded", () => {
  // Referencias a modales y mensajes
  const modalEditar = new bootstrap.Modal(document.getElementById("modalEditarEspecialidad"));
  const mensajeConfirmacion = document.getElementById("mensajeConfirmacion");
  const mensajeExitoModificacion = document.getElementById("mensajeExitoModificacion");

  // Campos del modal editar
  const inputNombreEditar = document.getElementById("editarNombreEspecialidad");
  const inputInstitucionEditar = document.getElementById("editarInputInstitucion");

  // Botones del mensaje confirmación
  const btnConfirmarModificacion = document.getElementById("btnConfirmarModificacion");
  const btnCancelarModificacion = document.getElementById("btnCancelarModificacion");

  // Guardar referencia a la fila que se editará
  let filaActual = null;

  // Abrir modal editar y cargar datos al hacer click en botón editar
  document.querySelector("table tbody").addEventListener("click", (e) => {
    const btnEditar = e.target.closest(".btn-editar");
    if (!btnEditar) return;

    const fila = btnEditar.closest("tr");
    if (!fila) return;

    filaActual = fila;

    // Cargar datos al modal
    inputNombreEditar.value = fila.querySelectorAll("td")[0].textContent.trim();
    inputInstitucionEditar.value = fila.querySelectorAll("td")[1].textContent.trim();

    modalEditar.show();
  });

// Mostrar mensaje confirmación
function mostrarConfirmacion() {
  mensajeConfirmacion.classList.remove("oculto");  // quitar oculto para que no tenga display:none
  mensajeConfirmacion.classList.add("visible");
}

// Ocultar mensaje confirmación
function ocultarConfirmacion() {
  mensajeConfirmacion.classList.remove("visible");
  setTimeout(() => {
    mensajeConfirmacion.classList.add("oculto"); // agregar oculto para esconder completamente
  }, 400); // esperar la transición
}

// Mostrar mensaje éxito
function mostrarMensajeExito() {
  mensajeExitoModificacion.classList.remove("oculto");
  mensajeExitoModificacion.classList.add("visible");

  setTimeout(() => {
    mensajeExitoModificacion.classList.remove("visible");
    setTimeout(() => {
      mensajeExitoModificacion.classList.add("oculto");
    }, 400);
  }, 3000);
}

  // Cuando se clickea en "Guardar Cambios"
  document.getElementById("btnGuardarCambios").addEventListener("click", () => {
    // Validaciones simples
    const nombre = inputNombreEditar.value.trim();
    const institucion = inputInstitucionEditar.value.trim();

    if (!nombre) {
      alert("Ingrese el nombre de la especialidad");
      return;
    }
    if (!institucion) {
      alert("Seleccione una institución");
      return;
    }

    // Mostrar mensaje de confirmación antes de guardar
    mostrarConfirmacion();
  });

  // Botón "Sí" en mensaje confirmación
  btnConfirmarModificacion.addEventListener("click", () => {
    if (!filaActual) {
      ocultarConfirmacion();
      return;
    }

    // Actualizar fila en la tabla con los nuevos datos
    filaActual.querySelectorAll("td")[0].textContent = inputNombreEditar.value.trim();
    filaActual.querySelectorAll("td")[1].textContent = inputInstitucionEditar.value.trim();

    // Ocultar mensaje confirmación
    ocultarConfirmacion();

    // Cerrar modal editar
    modalEditar.hide();

    // Mostrar mensaje éxito
    mostrarMensajeExito();
  });

  // Botón "No" en mensaje confirmación
  btnCancelarModificacion.addEventListener("click", () => {
    ocultarConfirmacion();
  });

  // Funciones para cambiar la institución en modal editar (por los <a> con onclick)
  window.setEditarInstitucion = function(nombreInstitucion) {
    inputInstitucionEditar.value = nombreInstitucion;
  };

});
