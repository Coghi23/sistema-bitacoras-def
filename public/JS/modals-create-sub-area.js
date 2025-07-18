// Función auxiliar para capturar datos de inputs
function obtenerValorInput(selector) {
    return document.querySelector(selector)?.value?.trim();
}

// Función principal para crear sub-área
document.addEventListener('DOMContentLoaded', () => {
    const btnCrear = document.querySelector('#modalAgregarSubarea .btn-crear');
    const tabla = document.querySelector('.table tbody');

    btnCrear.addEventListener('click', () => {
        // Obtener valores del modal
        const nombreSubarea = obtenerValorInput('#modalAgregarSubarea input[placeholder="Ingrese el nombre de la Sub Área"]');
        const especialidad = obtenerValorInput('#modalAgregarSubarea .especialidad-input');
        const profesor = obtenerValorInput('#modalAgregarSubarea .profesor-input');
        const institucion = obtenerValorInput('#modalAgregarSubarea .institucion-input');

        // Validar campos
        if (!nombreSubarea || !especialidad || !profesor || !institucion) {
            alert('Por favor complete todos los campos antes de crear la Sub Área.');
            return;
        }

        // Crear nueva fila HTML
        const nuevaFila = document.createElement('tr');
        nuevaFila.innerHTML = `
            <td class="text-center">${profesor}</td>
            <td class="text-center">${nombreSubarea}</td>
            <td class="text-center">${especialidad}</td>
            <td class="text-center">${institucion}</td>
            <td class="text-center">
                <button class="btn btn-link text-info p-0 me-2">
                    <i class="bi bi-pencil" style="font-size: 1.8rem;"></i>
                </button>
                <button class="btn btn-link text-info p-0">
                    <i class="bi bi-trash" style="font-size: 1.8rem;"></i>
                </button>
            </td>
        `;

        // Agregar a la tabla
        tabla.appendChild(nuevaFila);

        // Cerrar el modal
        const modalElement = document.getElementById('modalAgregarSubarea');
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();

        // Limpiar campos
        modalElement.querySelectorAll('input').forEach(input => input.value = '');

        // Opcional: mostrar mensaje de éxito
        alert('Sub Área creada exitosamente.');
    });
});
// JS/modals-create-sub-area.js

document.addEventListener("DOMContentLoaded", () => {
    // Función para seleccionar opción del dropdown y asignarla al input correcto por clase
    window.seleccionarOpcion = function(element, inputClass) {
        const input = element.closest('.dynamic-group').querySelector(`.${inputClass}`);
        input.value = element.textContent;
    };

    // Botón "Crear" en el modal de crear sub área
    document.querySelector("#modalAgregarSubarea .btn-crear").addEventListener("click", () => {
        limpiarAdvertencias();

        const nombreInput = document.querySelector("#modalAgregarSubarea input[placeholder='Ingrese el nombre de la Sub Área']");
        const especialidadInput = document.querySelector("#modalAgregarSubarea .especialidad-input");
        const profesorInput = document.querySelector("#modalAgregarSubarea .profesor-input");
        const institucionInput = document.querySelector("#modalAgregarSubarea .institucion-input");

        const nombre = nombreInput.value.trim();
        const especialidad = especialidadInput.value.trim();
        const profesor = profesorInput.value.trim();
        const institucion = institucionInput.value.trim();

        let valido = true;

        if (!nombre) {
            mostrarAdvertencia(nombreInput, "Ingrese el nombre de la sub área");
            valido = false;
        }
        if (!especialidad) {
            mostrarAdvertencia(especialidadInput, "Seleccione una especialidad");
            valido = false;
        }
        if (!profesor) {
            mostrarAdvertencia(profesorInput, "Seleccione un profesor");
            valido = false;
        }
        if (!institucion) {
            mostrarAdvertencia(institucionInput, "Seleccione una institución");
            valido = false;
        }

        if (!valido) return;

        // Agregar la nueva sub área a la tabla
        const tbody = document.querySelector("table tbody");
        const tr = document.createElement("tr");

        tr.innerHTML = `
            <td class="text-center">${profesor}</td>
            <td class="text-center">${nombre}</td>
            <td class="text-center">${especialidad}</td>
            <td class="text-center">${institucion}</td>
            <td class="text-center">
                <button class="btn btn-link text-info p-0 me-2 btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditarSubarea">
                    <i class="bi bi-pencil" style="font-size: 1.8rem;"></i>
                </button>
                <button class="btn btn-link text-info p-0 btn-eliminar">
                    <i class="bi bi-trash" style="font-size: 1.8rem;"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);

        // Limpiar campos del modal
        nombreInput.value = "";
        especialidadInput.value = "";
        profesorInput.value = "";
        institucionInput.value = "";

        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalAgregarSubarea'));
        modal.hide();
    });

    // Delegación para eliminar sub área
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
            const profesor = row.querySelectorAll("td")[0].textContent.trim();
            const nombre = row.querySelectorAll("td")[1].textContent.trim();
            const especialidad = row.querySelectorAll("td")[2].textContent.trim();
            const institucion = row.querySelectorAll("td")[3].textContent.trim();

            document.getElementById("editarProfesor").value = profesor;
            document.getElementById("editarNombreSubarea").value = nombre;
            document.getElementById("editarEspecialidad").value = especialidad;
            document.getElementById("editarInstitucion").value = institucion;

            // Guardar la fila para actualizarla después
            document.getElementById("modalEditarSubarea").currentRow = row;
        }
    });

    function mostrarAdvertencia(input, mensaje) {
        // Busca el contenedor de campo (mb-3 o mb-4), donde está label+input
        const fieldBlock = input.closest('.mb-3, .mb-4');
        if (!fieldBlock) return;

        // Busca o crea un wrapper para label y alerta
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

        // Si ya hay una alerta, no la duplica
        if (labelRow.querySelector('.alert-validacion')) return;

        // Crea la alerta
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