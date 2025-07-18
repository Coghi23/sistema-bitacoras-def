
document.addEventListener("DOMContentLoaded", () => {
  // Dropdown selector
  window.seleccionarOpcion = function(element, inputClass) {
    const input = element.closest('.dynamic-group').querySelector(`.${inputClass}`);
    input.value = element.textContent;
  };

  // Estado para edición/eliminación
  let filaSeleccionada = null;
  let filaParaEliminar = null;

  // Mensajes flotantes
  const mensajeExito = document.getElementById("mensajeExito");
  const mensajeExitoMod = document.getElementById("mensajeExitoModificacion");
  const mensajeConfirmacionEliminar = document.getElementById("mensajeConfirmacionEliminar");
  const mensajeExitoEliminar = document.getElementById("mensajeExitoEliminar");

  // --- CREAR SUBAREA ---
  document.querySelector("#modalAgregarSubarea .btn-crear").addEventListener("click", () => {
    limpiarAdvertencias();

    const nombreInput = document.getElementById("nombreSubarea");
    const especialidadInput = document.querySelector("#modalAgregarSubarea .especialidad-input");
    const profesorInput = document.querySelector("#modalAgregarSubarea .profesor-input");
    const institucionInput = document.querySelector("#modalAgregarSubarea .institucion-input");

    const nombre = nombreInput.value.trim();
    const especialidad = especialidadInput.value.trim();
    const profesor = profesorInput.value.trim();
    const institucion = institucionInput.value.trim();

    let valido = true;
    if (!nombre) { mostrarAdvertencia(nombreInput, "Ingrese el nombre de la sub área"); valido = false; }
    if (!especialidad) { mostrarAdvertencia(especialidadInput, "Seleccione una especialidad"); valido = false; }
    if (!profesor) { mostrarAdvertencia(profesorInput, "Seleccione un profesor"); valido = false; }
    if (!institucion) { mostrarAdvertencia(institucionInput, "Seleccione una institución"); valido = false; }
    if (!valido) return;

    // Crear fila
    const tbody = document.querySelector("table tbody");
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td class="text-center">${profesor}</td>
      <td class="text-center">${nombre}</td>
      <td class="text-center">${especialidad}</td>
      <td class="text-center">${institucion}</td>
      <td class="text-center">
        <button class="btn btn-link text-info p-0 me-2 btn-editar"><i class="bi bi-pencil" style="font-size: 1.8rem;"></i></button>
        <button class="btn btn-link text-info p-0 btn-eliminar"><i class="bi bi-trash" style="font-size: 1.8rem;"></i></button>
      </td>
    `;
    tbody.appendChild(tr);

    // Limpiar campos
    nombreInput.value = "";
    especialidadInput.value = "";
    profesorInput.value = "";
    institucionInput.value = "";

    // Cerrar modal
    bootstrap.Modal.getInstance(document.getElementById('modalAgregarSubarea')).hide();

    // Mostrar mensaje éxito
    mostrarMensajeExito(mensajeExito, "¡Sub-area creada con éxito!");
  });

  // --- EDITAR SUBAREA ---
  document.querySelector("table tbody").addEventListener("click", function(e) {
    // Editar
    if (e.target.closest(".btn-editar")) {
      filaSeleccionada = e.target.closest("tr");
      const tds = filaSeleccionada.querySelectorAll("td");
      document.getElementById("editarProfesor").value = tds[0].textContent.trim();
      document.getElementById("editarNombreSubarea").value = tds[1].textContent.trim();
      document.getElementById("editarEspecialidad").value = tds[2].textContent.trim();
      document.getElementById("editarInstitucion").value = tds[3].textContent.trim();
      bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarSubarea')).show();
    }
    // Eliminar
    else if (e.target.closest(".btn-eliminar")) {
      filaParaEliminar = e.target.closest("tr");
      mostrarMensajeConfirmacionEliminar();
    }
  });

  // Botón modificar en modal editar
  document.querySelector("#modalEditarSubarea .btn-modificar").addEventListener("click", () => {
    limpiarAdvertencias();

    const nombreInput = document.getElementById("editarNombreSubarea");
    const especialidadInput = document.getElementById("editarEspecialidad");
    const profesorInput = document.getElementById("editarProfesor");
    const institucionInput = document.getElementById("editarInstitucion");

    const nombre = nombreInput.value.trim();
    const especialidad = especialidadInput.value.trim();
    const profesor = profesorInput.value.trim();
    const institucion = institucionInput.value.trim();

    let valido = true;
    if (!nombre) { mostrarAdvertencia(nombreInput, "Ingrese el nombre de la sub área"); valido = false; }
    if (!especialidad) { mostrarAdvertencia(especialidadInput, "Seleccione una especialidad"); valido = false; }
    if (!profesor) { mostrarAdvertencia(profesorInput, "Seleccione un profesor"); valido = false; }
    if (!institucion) { mostrarAdvertencia(institucionInput, "Seleccione una institución"); valido = false; }
    if (!valido) return;

    // Actualizar fila
    const tds = filaSeleccionada.querySelectorAll("td");
    tds[0].textContent = profesor;
    tds[1].textContent = nombre;
    tds[2].textContent = especialidad;
    tds[3].textContent = institucion;

    // Cerrar modal
    bootstrap.Modal.getInstance(document.getElementById('modalEditarSubarea')).hide();

    // Mostrar mensaje éxito edición
    mostrarMensajeExito(mensajeExitoMod, "Sub-area modificada con éxito!");
  });

  // --- ELIMINAR SUBAREA ---
  document.getElementById("btnCancelarEliminar").addEventListener("click", () => {
    mensajeConfirmacionEliminar.classList.remove("visible");
    setTimeout(() => mensajeConfirmacionEliminar.classList.add("oculto"), 400);
    filaParaEliminar = null;
  });

  document.getElementById("btnConfirmarEliminar").addEventListener("click", () => {
    mensajeConfirmacionEliminar.classList.remove("visible");
    setTimeout(() => mensajeConfirmacionEliminar.classList.add("oculto"), 400);

    // Eliminar la fila
    if (filaParaEliminar) filaParaEliminar.remove();
    filaParaEliminar = null;

    // Mostrar mensaje éxito
    mostrarMensajeExito(mensajeExitoEliminar, "Sub-area eliminada con éxito");
  });

  // --- UTILIDADES ---
  function mostrarAdvertencia(input, mensaje) {
    const fieldBlock = input.closest('.mb-3, .mb-4');
    if (!fieldBlock) return;
    let label = fieldBlock.querySelector('label');
    if (!label) return;

    let labelRow;
    if (label.parentElement.classList.contains('label-alerta-row')) {
      labelRow = label.parentElement;
    } else {
      labelRow = document.createElement('div');
      labelRow.className = 'label-alerta-row';
      labelRow.appendChild(label.cloneNode(true));
      label.parentElement.replaceChild(labelRow, label);
      label = labelRow.querySelector('label');
    }
    if (labelRow.querySelector('.alert-validacion')) return;
    const alerta = document.createElement('div');
    alerta.className = 'alert-validacion';
    alerta.innerHTML = `
      <span class="icono-alerta">
        <i class="bi bi-exclamation-circle-fill"></i>
      </span>
      <span style="font-family: 'Poppins', sans-serif; font-size: 13px;">${mensaje}</span>
    `;
    labelRow.appendChild(alerta);
    input.classList.add('input-alerta');
  }

  function limpiarAdvertencias() {
    document.querySelectorAll('.alert-validacion').forEach(e => e.remove());
    document.querySelectorAll('.input-alerta').forEach(e => e.classList.remove('input-alerta'));
  }

  function mostrarMensajeExito(element, texto) {
    const mensaje = element.querySelector("p");
    if (mensaje) mensaje.textContent = texto;
    element.classList.remove("oculto");
    setTimeout(() => element.classList.add("visible"), 10);
    setTimeout(() => {
      element.classList.remove("visible");
      setTimeout(() => element.classList.add("oculto"), 400);
    }, 2500);
  }

  function mostrarMensajeConfirmacionEliminar() {
    mensajeConfirmacionEliminar.classList.remove("oculto");
    setTimeout(() => mensajeConfirmacionEliminar.classList.add("visible"), 10);
  }
});