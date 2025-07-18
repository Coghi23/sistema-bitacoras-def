document.addEventListener("DOMContentLoaded", () => {
  const mensajeConfirmacionEliminar = document.getElementById("mensajeConfirmacionEliminar");
  const mensajeExitoEliminar = document.getElementById("mensajeExitoEliminar");
  let filaParaEliminar = null;

  // Mostrar mensaje confirmación al hacer clic en botón eliminar
  document.querySelector("table tbody").addEventListener("click", (e) => {
    if (e.target.closest(".btn-eliminar") || e.target.closest(".btn-link.text-info.p-0:not(.btn-editar)")) {
      e.preventDefault();
      filaParaEliminar = e.target.closest("tr");
      mensajeConfirmacionEliminar.classList.remove("oculto");
      mensajeConfirmacionEliminar.classList.add("visible");
    }
  });

  // Botón "No" cierra el mensaje sin eliminar
  document.getElementById("btnCancelarEliminar").addEventListener("click", () => {
    mensajeConfirmacionEliminar.classList.remove("visible");
    setTimeout(() => {
      mensajeConfirmacionEliminar.classList.add("oculto");
      filaParaEliminar = null;
    }, 400);
  });

  // Botón "Sí" cierra confirmación y muestra mensaje éxito (sin eliminar fila)
  document.getElementById("btnConfirmarEliminar").addEventListener("click", () => {
    mensajeConfirmacionEliminar.classList.remove("visible");
    setTimeout(() => {
      mensajeConfirmacionEliminar.classList.add("oculto");

      // Mostrar mensaje éxito
      mensajeExitoEliminar.classList.remove("oculto");
      mensajeExitoEliminar.classList.add("visible");

      setTimeout(() => {
        mensajeExitoEliminar.classList.remove("visible");
        setTimeout(() => {
          mensajeExitoEliminar.classList.add("oculto");
          filaParaEliminar = null;
        }, 400);
      }, 3000);
    }, 400);
  });
});
