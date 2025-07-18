document.addEventListener("DOMContentLoaded", () => {
  const grupos = [
    {
      containerId: "especialidades",
      inputId: "inputEspecialidad",
      opciones: ["Mantenimiento Industrial", "Electrónica", "Contabilidad"]
    },
    {
      containerId: "instituciones",
      inputId: "inputInstitucion",
      opciones: ["Covao", "Covao Nocturno", "Academias HHC"]
    }
  ];

  // Mensajes y sus textos
  const mensajeExito = document.getElementById("mensajeExito");
  const mensajeExitoTexto = mensajeExito.querySelector("p");

  grupos.forEach(({ containerId, inputId, opciones }) => {
    const container = document.getElementById(containerId);
    inicializarGrupo(container, inputId, opciones);
  });

  function inicializarGrupo(container, baseInputId, opciones) {
    const primeraFila = container.querySelector(".dynamic-group");
    const dropdownMenu = primeraFila.querySelector(".dropdown-menu");

    // Limpiar menú anterior
    dropdownMenu.innerHTML = "";

    opciones.forEach(opcion => {
      const li = document.createElement("li");
      const a = document.createElement("a");
      a.classList.add("dropdown-item");
      a.textContent = opcion;
      a.onclick = () => seleccionarValor(opcion, primeraFila, container, baseInputId, opciones);
      li.appendChild(a);
      dropdownMenu.appendChild(li);
    });
  }

  function seleccionarValor(valor, fila, container, baseInputId, opciones) {
    const input = fila.querySelector("input");
    input.value = valor;

    const filas = container.querySelectorAll(".dynamic-group");
    const esUltima = fila === filas[filas.length - 1];

    if (esUltima && valor.trim() !== "") {
      agregarNuevaFila(container, baseInputId, opciones);
    }
  }

  function agregarNuevaFila(container, baseInputId, opciones) {
    const nuevaFila = document.createElement("div");
    nuevaFila.classList.add("input-group", "dynamic-group", "mt-2");

    const input = document.createElement("input");
    input.type = "text";
    input.classList.add("form-control");
    input.placeholder = "Seleccione una opción";
    input.readOnly = true;

    const dropdownBtn = document.createElement("button");
    dropdownBtn.classList.add("bi-plus");
    dropdownBtn.type = "button";
    dropdownBtn.setAttribute("data-bs-toggle", "dropdown");
    dropdownBtn.setAttribute("aria-expanded", "false");

    const dropdownMenu = document.createElement("ul");
    dropdownMenu.classList.add("dropdown-menu", "custom-dropdown");

    opciones.forEach(opcion => {
      const li = document.createElement("li");
      const a = document.createElement("a");
      a.classList.add("dropdown-item");
      a.textContent = opcion;
      a.onclick = () => seleccionarValor(opcion, nuevaFila, container, baseInputId, opciones);
      li.appendChild(a);
      dropdownMenu.appendChild(li);
    });

    const eliminarBtn = document.createElement("button");
    eliminarBtn.classList.add("btn", "btn-danger");
    eliminarBtn.innerHTML = '<i class="bi bi-x"></i>';
    eliminarBtn.onclick = () => container.removeChild(nuevaFila);

    nuevaFila.appendChild(input);
    nuevaFila.appendChild(dropdownBtn);
    nuevaFila.appendChild(dropdownMenu);
    nuevaFila.appendChild(eliminarBtn);

    container.appendChild(nuevaFila);
  }

  // Validación visual actualizada
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

  // Mostrar mensaje éxito con animación
  function mostrarMensajeExito(elementoMensaje) {
    elementoMensaje.classList.remove("oculto");
    setTimeout(() => elementoMensaje.classList.add("visible"), 10);
    setTimeout(() => {
      elementoMensaje.classList.remove("visible");
      setTimeout(() => elementoMensaje.classList.add("oculto"), 400);
    }, 2500);
  }

  // BOTÓN CREAR -> Agrega fila a la tabla con mensajes nuevos
  document.querySelector(".btn-crear").addEventListener("click", () => {
    limpiarAdvertencias();

    const nombreSeccionInput = document.querySelector("#modalAgregarSeccion input[placeholder='Ingrese el nombre de la Seccion']");
    const especialidadInput = document.querySelector("#especialidades .dynamic-group input");
    const institucionInput = document.querySelector("#instituciones .dynamic-group input");

    const nombreSeccion = nombreSeccionInput.value.trim();
    const especialidades = Array.from(document.querySelectorAll("#especialidades input")).map(i => i.value).filter(Boolean);
    const instituciones = Array.from(document.querySelectorAll("#instituciones input")).map(i => i.value).filter(Boolean);

    let valido = true;

    if (!nombreSeccion) {
      mostrarAdvertencia(nombreSeccionInput, "Ingrese todos los datos");
      valido = false;
    }
    if (especialidades.length === 0) {
      mostrarAdvertencia(especialidadInput, "Ingrese todos los datos");
      valido = false;
    }
    if (instituciones.length === 0) {
      mostrarAdvertencia(institucionInput, "Ingrese todos los datos");
      valido = false;
    }

    if (!valido) return;

    const tbody = document.querySelector("table tbody");
    const tr = document.createElement("tr");

    tr.innerHTML = `
      <td class="text-center">${nombreSeccion}</td>
      <td class="text-center">${especialidades.join(", ")}</td>
      <td class="text-center">${instituciones.join(", ")}</td>
      <td class="text-center">
        <button class="btn btn-link text-info p-0 me-2 btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditarSeccion">
          <i class="bi bi-pencil" style="font-size: 1.8rem;"></i>
        </button>
        <button class="btn btn-link text-info p-0 btn-eliminar">
          <i class="bi bi-trash" style="font-size: 1.8rem;"></i>
        </button>
      </td>
    `;

    tbody.appendChild(tr);

    // Limpiar campos del modal
    nombreSeccionInput.value = "";
    document.querySelectorAll("#especialidades, #instituciones").forEach(container => {
      container.innerHTML = `
        <div class="input-group dynamic-group">
          <input type="text" class="form-control" placeholder="Seleccione una opción" readonly>
          <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
          <ul class="dropdown-menu custom-dropdown"></ul>
        </div>
      `;
    });

    // Re-inicializar opciones
    grupos.forEach(({ containerId, inputId, opciones }) => {
      const container = document.getElementById(containerId);
      inicializarGrupo(container, inputId, opciones);
    });

    // Cerrar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalAgregarSeccion'));
    modal.hide();

    // Mostrar mensaje de éxito
    mensajeExitoTexto.textContent = "Sección creada con éxito!";
    mostrarMensajeExito(mensajeExito);
  });
});
