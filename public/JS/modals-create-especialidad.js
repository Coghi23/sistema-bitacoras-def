document.addEventListener("DOMContentLoaded", () => {
    // Manejo de selección de institución en crear
    document.querySelectorAll("#modalAgregarEspecialidad .dropdown-menu .dropdown-item").forEach(item => {
        item.addEventListener("click", function() {
            document.getElementById("inputInstitucion").value = this.textContent;
        });
    });

    // Botón "Crear" en el modal de crear especialidad
    document.querySelector("#modalAgregarEspecialidad .btn-crear").addEventListener("click", () => {
        limpiarAdvertencias();

        const nombreEspecialidadInput = document.querySelector("#modalAgregarEspecialidad input[placeholder='Ingrese el nombre de la Especialidad']");
        const institucionInput = document.getElementById("inputInstitucion");
        const nombreEspecialidad = nombreEspecialidadInput.value.trim();
        const institucion = institucionInput.value.trim();

        let valido = true;

        if (!nombreEspecialidad) {
            mostrarAdvertencia(nombreEspecialidadInput, "Ingrese el nombre de la especialidad");
            valido = false;
        }
        if (!institucion) {
            mostrarAdvertencia(institucionInput, "Seleccione una institución");
            valido = false;
        }

        if (!valido) return;

        // Agregar la nueva especialidad a la tabla
        const tbody = document.querySelector("table tbody");
        const tr = document.createElement("tr");

        tr.innerHTML = `
            <td class="text-center">${nombreEspecialidad}</td>
            <td class="text-center">${institucion}</td>
            <td class="text-center">
                <button class="btn btn-link text-info p-0 me-2 btn-editar">
                    <i class="bi bi-pencil" style="font-size: 1.8rem;"></i>
                </button>
                <button class="btn btn-link text-info p-0 btn-eliminar">
                    <i class="bi bi-trash" style="font-size: 1.8rem;"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        // Limpiar campos del modal
        nombreEspecialidadInput.value = "";
        institucionInput.value = "";

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalAgregarEspecialidad'));
        modal.hide();

        // Mostrar mensaje éxito usando clases visible y oculto
        const mensajeExito = document.getElementById('mensajeExito');
        mensajeExito.classList.add('visible');
        mensajeExito.classList.remove('oculto');

        // Ocultar mensaje después de 3 segundos con transición
        setTimeout(() => {
            mensajeExito.classList.remove('visible');
            setTimeout(() => {
                mensajeExito.classList.add('oculto');
            }, 400); // 400 ms igual que la transición CSS
        }, 3000);
    });

    // Delegación para eliminar especialidad
    document.querySelector("table tbody").addEventListener("click", function(e) {
        if (e.target.closest(".btn-eliminar")) {
            const row = e.target.closest("tr");
            if (row) row.remove();
        }
    });

    // Delegación para editar (pasar valores al modal de editar)
    document.querySelector("table tbody").addEventListener("click", function(e) {
        if (e.target.closest(".btn-editar")) {
            const btn = e.target.closest(".btn-editar");
            const row = btn.closest("tr");
            const nombre = row.querySelectorAll("td")[0].textContent.trim();
            const institucion = row.querySelectorAll("td")[1].textContent.trim();

            document.getElementById("editarNombreEspecialidad").value = nombre;
            document.getElementById("editarInputInstitucion").value = institucion;

            // Guardar la fila para actualizarla después
            document.getElementById("modalEditarEspecialidad").currentRow = row;
        }
    });

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
