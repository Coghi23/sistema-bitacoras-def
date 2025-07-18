document.addEventListener("DOMContentLoaded", () => {
    // Referencias a los mensajes
    const mensajeExito = document.getElementById("mensajeExito");
    const mensajeExitoModificacion = document.getElementById("mensajeExitoModificacion");
    const mensajeConfirmacion = document.getElementById("mensajeConfirmacion");

    const mensajeExitoTexto = mensajeExito.querySelector("p");
    const mensajeExitoModificacionTexto = mensajeExitoModificacion.querySelector("p");
    const mensajeConfirmacionTexto = mensajeConfirmacion.querySelector("p");

    const btnConfirmarModificacion = document.getElementById("btnConfirmarModificacion");
    const btnCancelarModificacion = document.getElementById("btnCancelarModificacion");

    let filaParaModificar = null;

    // Función para mostrar mensajes de éxito
    function mostrarMensajeExito(elementoMensaje) {
        elementoMensaje.classList.remove("oculto");
        setTimeout(() => elementoMensaje.classList.add("visible"), 10);
        setTimeout(() => {
            elementoMensaje.classList.remove("visible");
            setTimeout(() => elementoMensaje.classList.add("oculto"), 400);
        }, 2500);
    }

    // Mostrar y ocultar confirmación
    function mostrarConfirmacion() {
        mensajeConfirmacion.classList.remove("oculto");
        setTimeout(() => mensajeConfirmacion.classList.add("visible"), 10);
    }

    function ocultarConfirmacion() {
        mensajeConfirmacion.classList.remove("visible");
        setTimeout(() => mensajeConfirmacion.classList.add("oculto"), 400);
    }

    // Botón "Crear" institución
    document.querySelector("#modalAgregarInstitucion .btn-crear").addEventListener("click", () => {
        limpiarAdvertencias();

        const input = document.querySelector("#modalAgregarInstitucion input[placeholder='Ingrese el nombre de la Institución']");
        const nombre = input.value.trim();
        let valido = true;

        if (!nombre) {
            mostrarAdvertencia(input, "Ingrese el nombre de la institución");
            valido = false;
        }

        if (!valido) return;

        const tbody = document.querySelector("table tbody");
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td class="text-center">${nombre}</td>
            <td class="text-center">
                <button class="btn btn-link text-info p-0 me-2 btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditarInstitucion" data-nombre="${nombre}"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-link text-info p-0 btn-eliminar"><i class="bi bi-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);

        input.value = "";
        bootstrap.Modal.getInstance(document.getElementById("modalAgregarInstitucion")).hide();

        mensajeExitoTexto.textContent = "Institución creada con éxito!";
        mostrarMensajeExito(mensajeExito);
    });

    // Delegación para eliminar (pendiente implementar confirmación si se desea)
    document.querySelector("table tbody").addEventListener("click", function (e) {
        if (e.target.closest(".btn-eliminar")) {
            const fila = e.target.closest("tr");
            if (fila) fila.remove();
            // Aquí podrías agregar confirmación y mensaje de éxito si querés
        }
    });

    // Delegación para edición
    document.querySelector("table tbody").addEventListener("click", function (e) {
        if (e.target.closest(".btn-editar")) {
            const btn = e.target.closest(".btn-editar");
            const nombre = btn.getAttribute("data-nombre") || btn.parentElement.parentElement.querySelector("td").textContent.trim();
            document.getElementById("editarNombreInstitucion").value = nombre;
            document.getElementById("modalEditarInstitucion").currentRow = btn.closest("tr");
        }
    });

    // Botón "Guardar cambios" -> lanza confirmación
    document.getElementById("btnGuardarCambios").addEventListener("click", () => {
        limpiarAdvertencias();

        const inputEditar = document.getElementById("editarNombreInstitucion");
        const nuevoNombre = inputEditar.value.trim();

        if (!nuevoNombre) {
            mostrarAdvertencia(inputEditar, "Ingrese el nombre de la institución");
            return;
        }

        filaParaModificar = document.getElementById("modalEditarInstitucion").currentRow;
        mensajeConfirmacionTexto.textContent = "¿Está usted seguro de modificar esta institución?";
        mostrarConfirmacion();
    });

    // Botón "Sí" en confirmación
    btnConfirmarModificacion.addEventListener("click", () => {
        if (filaParaModificar) {
            const inputEditar = document.getElementById("editarNombreInstitucion");
            const nuevoNombre = inputEditar.value.trim();

            filaParaModificar.querySelector("td").textContent = nuevoNombre;
            filaParaModificar.querySelector(".btn-editar").setAttribute("data-nombre", nuevoNombre);

            bootstrap.Modal.getInstance(document.getElementById("modalEditarInstitucion")).hide();

            mensajeExitoModificacionTexto.textContent = "Institución modificada con éxito!";
            mostrarMensajeExito(mensajeExitoModificacion);

            ocultarConfirmacion();
            filaParaModificar = null;
        }
    });

    // Botón "No" en confirmación
    btnCancelarModificacion.addEventListener("click", () => {
        ocultarConfirmacion();
        filaParaModificar = null;
    });

    // --- Funciones de validación visual ---
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
});
