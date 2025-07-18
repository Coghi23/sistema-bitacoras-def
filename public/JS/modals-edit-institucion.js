document.addEventListener("DOMContentLoaded", () => {
    let filaParaModificar = null;

    const mensajeConfirmacion = document.getElementById("mensajeConfirmacion");
    const mensajeExitoModificacion = document.getElementById("mensajeExitoModificacion");
    const mensajeConfirmacionTexto = mensajeConfirmacion.querySelector("p");
    const mensajeExitoModificacionTexto = mensajeExitoModificacion.querySelector("p");
    const btnConfirmarModificacion = document.getElementById("btnConfirmarModificacion");
    const btnCancelarModificacion = document.getElementById("btnCancelarModificacion");

    // Delegación para botón editar
    document.querySelector("table tbody").addEventListener("click", function (e) {
        if (e.target.closest(".btn-editar")) {
            const btn = e.target.closest(".btn-editar");
            const nombre = btn.getAttribute("data-nombre") || btn.parentElement.parentElement.querySelector("td").textContent.trim();
            document.getElementById("editarNombreInstitucion").value = nombre;
            document.getElementById("modalEditarInstitucion").currentRow = btn.closest("tr");
        }
    });

    // Botón Guardar Cambios
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

    // Botón Confirmar Modificación
    btnConfirmarModificacion.addEventListener("click", () => {
        if (filaParaModificar) {
            const inputEditar = document.getElementById("editarNombreInstitucion");
            const nuevoNombre = inputEditar.value.trim();

            // Actualizar fila
            filaParaModificar.querySelector("td").textContent = nuevoNombre;
            filaParaModificar.querySelector(".btn-editar").setAttribute("data-nombre", nuevoNombre);

            // Ocultar modal
            bootstrap.Modal.getInstance(document.getElementById("modalEditarInstitucion")).hide();

            // Mostrar mensaje de éxito
            mensajeExitoModificacionTexto.textContent = "Institución modificada con éxito!";
            mostrarMensajeExito(mensajeExitoModificacion);

            ocultarConfirmacion();
            filaParaModificar = null;
        }
    });

    // Botón Cancelar Modificación
    btnCancelarModificacion.addEventListener("click", () => {
        ocultarConfirmacion();
        filaParaModificar = null;
    });

    // Mostrar confirmación
    function mostrarConfirmacion() {
        mensajeConfirmacion.classList.remove("oculto");
    }

    // Ocultar confirmación
    function ocultarConfirmacion() {
        mensajeConfirmacion.classList.add("oculto");
    }

    // Mostrar mensaje de éxito
    function mostrarMensajeExito(elemento) {
        elemento.classList.remove("oculto");
        setTimeout(() => {
            elemento.classList.add("oculto");
        }, 2500);
    }

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
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" style="vertical-align:middle; margin-right:2px;" viewBox="0 0 16 16" fill="currentColor">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.964 0L.165 13.233c-.457.778.091 1.767.982 1.767h13.707c.89 0 1.438-.99.982-1.767L8.982 1.566zm-1.196.868a.13.13 0 0 1 .23 0l6.853 11.667a.145.145 0 0 1-.115.227H1.146a.145.145 0 0 1-.115-.227L7.787 2.434zM8 5c-.535 0-.954.462-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 5zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                </svg>
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
