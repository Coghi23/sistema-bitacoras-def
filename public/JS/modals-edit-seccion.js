document.addEventListener('DOMContentLoaded', () => {
  const modalEditar = new bootstrap.Modal(document.getElementById('modalEditarSeccion'));
  const inputNombre = document.getElementById('editNombreSeccion');
  const especialidadesContainer = document.getElementById('especialidadesEditar');
  const institucionesContainer = document.getElementById('institucionesEditar');
  let filaEditando = null;

  // Opciones disponibles
  const opcionesEspecialidades = ['Contabilidad', 'Desarrollo Web', 'Electrónica', 'Mantenimiento Industrial'];
  const opcionesInstituciones = ['Covao', 'Covao Nocturno', 'Academias HHC'];

  // Función que crea una fila con dropdown y botón eliminar
  function crearFila(valor, container, opciones) {
    const fila = document.createElement('div');
    fila.className = 'input-group dynamic-group mt-2';

    const input = document.createElement('input');
    input.type = 'text';
    input.className = 'form-control';
    input.placeholder = 'Seleccione una opción';
    input.readOnly = true;
    if (valor) input.value = valor;

    const btnDropdown = document.createElement('button');
    btnDropdown.className = 'btn-plus dropdown-toggle';
    btnDropdown.type = 'button';
    btnDropdown.setAttribute('data-bs-toggle', 'dropdown');
    btnDropdown.setAttribute('aria-expanded', 'false');

    const ulDropdown = document.createElement('ul');
    ulDropdown.className = 'dropdown-menu custom-dropdown';

    opciones.forEach(opcion => {
      const li = document.createElement('li');
      const a = document.createElement('a');
      a.className = 'dropdown-item';
      a.href = '#';
      a.textContent = opcion;
      a.addEventListener('click', (e) => {
        e.preventDefault();
        input.value = opcion;

        const filas = container.querySelectorAll('.dynamic-group');
        const esUltima = fila === filas[filas.length - 1];

        if (esUltima && opcion.trim() !== '') {
          container.appendChild(crearFila('', container, opciones));
        }
      });
      li.appendChild(a);
      ulDropdown.appendChild(li);
    });

    const btnEliminar = document.createElement('button');
    btnEliminar.className = 'btn btn-danger';
    btnEliminar.type = 'button';
    btnEliminar.innerHTML = '<i class="bi bi-x"></i>';
    btnEliminar.addEventListener('click', () => {
      container.removeChild(fila);
    });

    fila.appendChild(input);
    fila.appendChild(btnDropdown);
    fila.appendChild(ulDropdown);
    fila.appendChild(btnEliminar);

    return fila;
  }

  // Llenar contenedor con valores existentes
  function llenarContainer(container, valores, opciones) {
    container.innerHTML = '';
    valores.forEach(valor => {
      container.appendChild(crearFila(valor, container, opciones));
    });

    // Si no hay valores, o no hay fila vacía, agrega una
    if (valores.length === 0 || !container.querySelector('.dynamic-group input[value=""]')) {
      container.appendChild(crearFila('', container, opciones));
    }
  }

  // Delegación para botón editar
  document.querySelector('table tbody').addEventListener('click', e => {
    if (e.target.closest('.btn-editar')) {
      filaEditando = e.target.closest('tr');

      const celdas = filaEditando.querySelectorAll('td');
      const nombre = celdas[0].textContent.trim();
      const especialidades = celdas[1].textContent.trim().split(',').map(v => v.trim()).filter(v => v);
      const instituciones = celdas[2].textContent.trim().split(',').map(v => v.trim()).filter(v => v);

      inputNombre.value = nombre;
      llenarContainer(especialidadesContainer, especialidades, opcionesEspecialidades);
      llenarContainer(institucionesContainer, instituciones, opcionesInstituciones);

      modalEditar.show();
    }
  });

  // Guardar cambios
  document.getElementById('btnGuardarCambios').addEventListener('click', () => {
    if (!filaEditando) return;

    const nombre = inputNombre.value.trim();
    if (!nombre) {
      alert('El nombre de la sección es obligatorio');
      return;
    }

    const especialidades = Array.from(especialidadesContainer.querySelectorAll('input'))
      .map(i => i.value.trim()).filter(v => v);
    const instituciones = Array.from(institucionesContainer.querySelectorAll('input'))
      .map(i => i.value.trim()).filter(v => v);

    if (especialidades.length === 0 || instituciones.length === 0) {
      alert('Debe seleccionar al menos una especialidad y una institución');
      return;
    }

    const celdas = filaEditando.querySelectorAll('td');
    celdas[0].textContent = nombre;
    celdas[1].textContent = especialidades.join(', ');
    celdas[2].textContent = instituciones.join(', ');

    modalEditar.hide();
    filaEditando = null;

    // Mostrar mensaje de éxito animado
    const mensaje = document.getElementById('mensajeExitoModificacion');
    mensaje.classList.remove('oculto');
    setTimeout(() => mensaje.classList.add('visible'), 10);
    setTimeout(() => {
      mensaje.classList.remove('visible');
      setTimeout(() => mensaje.classList.add('oculto'), 400);
    }, 2500);
  });
});
