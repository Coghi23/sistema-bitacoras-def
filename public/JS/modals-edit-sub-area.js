document.addEventListener("DOMContentLoaded", () => {
  const gruposEditar = [
    {
      containerId: "especialidadesEditar",
      inputClass: "especialidad-input",
      opciones: ["Mantenimiento Industrial", "Electrónica", "Contabilidad"]
    },
    {
      containerId: "profesoresEditar",
      inputClass: "profesor-input",
      opciones: ["Ana Piedra"]
    },
    {
      containerId: "institucionesEditar",
      inputClass: "institucion-input",
      opciones: ["Covao", "Covao Nocturno", "Academias HHC"]
    }
  ];

  let filaSeleccionada = null;

  // Evento al hacer click en el botón de editar en la tabla
  document.querySelector("table").addEventListener("click", (e) => {
    if (e.target.closest(".btn-editar")) {
      const btn = e.target.closest(".btn-editar");
      filaSeleccionada = btn.closest("tr");

      const nombre = filaSeleccionada.children[1].textContent.trim();
      const especialidades = filaSeleccionada.children[2].textContent.split(",").map(s => s.trim());
      const profesores = filaSeleccionada.children[0].textContent.split(",").map(s => s.trim());
      const instituciones = filaSeleccionada.children[3].textContent.split(",").map(s => s.trim());

      document.querySelector("#modalEditarSubarea input[placeholder='Ingrese el nombre de la Sub Área']").value = nombre;

      const datos = [
        { id: "especialidadesEditar", valores: especialidades },
        { id: "profesoresEditar", valores: profesores },
        { id: "institucionesEditar", valores: instituciones }
      ];

      datos.forEach(({ id, valores }) => {
        const container = document.getElementById(id);
        container.innerHTML = ""; // Limpiar

        valores.forEach((valor, index) => {
          const fila = document.createElement("div");
          fila.classList.add("input-group", "dynamic-group", "mt-2");

          const input = document.createElement("input");
          input.type = "text";
          input.classList.add("form-control");
          input.value = valor;
          input.readOnly = true;

          const dropdownBtn = document.createElement("button");
          dropdownBtn.classList.add("btn-plus", "dropdown-toggle");
          dropdownBtn.type = "button";
          dropdownBtn.setAttribute("data-bs-toggle", "dropdown");
          dropdownBtn.setAttribute("aria-expanded", "false");

          const dropdownMenu = document.createElement("ul");
          dropdownMenu.classList.add("dropdown-menu", "custom-dropdown");

          const opciones = gruposEditar.find(g => g.containerId === id).opciones;

          opciones.forEach(op => {
            const li = document.createElement("li");
            const a = document.createElement("a");
            a.classList.add("dropdown-item");
            a.textContent = op;
            a.onclick = () => {
              input.value = op;
              const filas = container.querySelectorAll(".dynamic-group");
              const esUltima = fila === filas[filas.length - 1];
              if (esUltima && op.trim() !== "") {
                agregarNuevaFila(container, gruposEditar.find(g => g.containerId === id));
              }
            };
            li.appendChild(a);
            dropdownMenu.appendChild(li);
          });

          const eliminarBtn = document.createElement("button");
          eliminarBtn.classList.add("btn", "btn-danger");
          eliminarBtn.innerHTML = '<i class="bi bi-x"></i>';
          eliminarBtn.onclick = () => container.removeChild(fila);

          fila.appendChild(input);
          fila.appendChild(dropdownBtn);
          fila.appendChild(dropdownMenu);
          fila.appendChild(eliminarBtn);

          container.appendChild(fila);
        });

        // Si no hay filas, poner una vacía
        if (valores.length === 0) {
          const { inputClass, opciones } = gruposEditar.find(g => g.containerId === id);
          agregarNuevaFila(container, { inputClass, opciones });
        }
      });
    }
  });

  function agregarNuevaFila(container, { inputClass, opciones }) {
    const fila = document.createElement("div");
    fila.classList.add("input-group", "dynamic-group", "mt-2");

    const input = document.createElement("input");
    input.type = "text";
    input.classList.add("form-control", inputClass);
    input.placeholder = "Seleccione una opción";
    input.readOnly = true;

    const dropdownBtn = document.createElement("button");
    dropdownBtn.classList.add("btn-plus", "dropdown-toggle");
    dropdownBtn.type = "button";
    dropdownBtn.setAttribute("data-bs-toggle", "dropdown");
    dropdownBtn.setAttribute("aria-expanded", "false");

    const dropdownMenu = document.createElement("ul");
    dropdownMenu.classList.add("dropdown-menu", "custom-dropdown");

    opciones.forEach(op => {
      const li = document.createElement("li");
      const a = document.createElement("a");
      a.classList.add("dropdown-item");
      a.textContent = op;
      a.onclick = () => {
        input.value = op;
        const filas = container.querySelectorAll(".dynamic-group");
        const esUltima = fila === filas[filas.length - 1];
        if (esUltima && op.trim() !== "") {
          agregarNuevaFila(container, { inputClass, opciones });
        }
      };
      li.appendChild(a);
      dropdownMenu.appendChild(li);
    });

    const eliminarBtn = document.createElement("button");
    eliminarBtn.classList.add("btn", "btn-danger");
    eliminarBtn.innerHTML = '<i class="bi bi-x"></i>';
    eliminarBtn.onclick = () => container.removeChild(fila);

    fila.appendChild(input);
    fila.appendChild(dropdownBtn);
    fila.appendChild(dropdownMenu);
    fila.appendChild(eliminarBtn);

    container.appendChild(fila);
  }

  // BOTÓN MODIFICAR
  document.querySelector("#modalEditarSubarea .btn-modificar").addEventListener("click", () => {
    const nombreInput = document.querySelector("#modalEditarSubarea input[placeholder='Ingrese el nombre de la Sub Área']");
    const nombre = nombreInput.value.trim();

    const especialidades = Array.from(document.querySelectorAll("#especialidadesEditar input")).map(i => i.value).filter(Boolean);
    const profesores = Array.from(document.querySelectorAll("#profesoresEditar input")).map(i => i.value).filter(Boolean);
    const instituciones = Array.from(document.querySelectorAll("#institucionesEditar input")).map(i => i.value).filter(Boolean);

    if (!nombre || especialidades.length === 0 || profesores.length === 0 || instituciones.length === 0) {
      alert("Por favor complete todos los campos.");
      return;
    }

    // Actualizar la fila seleccionada
    filaSeleccionada.children[0].textContent = profesores.join(", ");
    filaSeleccionada.children[1].textContent = nombre;
    filaSeleccionada.children[2].textContent = especialidades.join(", ");
    filaSeleccionada.children[3].textContent = instituciones.join(", ");

    // Cerrar el modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarSubarea'));
    modal.hide();

    const mensajeExito = document.getElementById("mensajeExito");
    const mensajeExitoTexto = mensajeExito.querySelector("p");
    mensajeExitoTexto.textContent = "¡Sub-área modificada con éxito!";
    mensajeExito.classList.remove("oculto");
    setTimeout(() => mensajeExito.classList.add("visible"), 10);
    setTimeout(() => {
      mensajeExito.classList.remove("visible");
      setTimeout(() => mensajeExito.classList.add("oculto"), 400);
    }, 2500);
  });
});
