//////////////////////////////////////////BOTONES OPCIONES////////////////////////////////////////////////////////////////////////////////////////
const btn = document.getElementById("btn");
const contenido = document.getElementById("contenido-problema");

function leftClick() {
  btn.style.left = "0";
  contenido.classList.remove("active");
    document.getElementById("btnEnviarOrden").classList.remove("d-none");
}

function rightClick() {
  btn.style.left = "50%";
  contenido.classList.add("active");
  document.getElementById("btnEnviarOrden").classList.add("d-none");
}



/////////////////////CONFIRMACIÓN/////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function confirmarEnvioComentario() {

  // Cerrar modal "a mano" ocultándolo
  const modal = document.getElementById('modalComentario');
  if (modal) {
    modal.style.display = 'none'; // O removeClass('show'), según cómo lo abras
  }

  // Mostrar alerta de éxito
  Swal.fire({
    text: '¡Bitácora enviada con éxito!',
    icon: 'success',
    timer: 1500,
    showConfirmButton: false,
    customClass: {
      popup: 'rounded-4'
    }
  });

    // Limpiar radio buttons
  const radios = document.querySelectorAll('input[name="prioridad"]');
  radios.forEach(radio => radio.checked = false);

  // Limpiar textarea
  document.querySelector('#observaciones textarea').value = '';
}


/////////////////////////////////////DATOS NO ENCONTRADOS////////////////////////////////////////////////////////////////////////
function validarDatos() {
  const radios = document.querySelectorAll('input[name="prioridad"]');
  const textarea = document.querySelector('#observaciones textarea');
  const mensajeError = document.getElementById('mensajeError');

  let radioSeleccionado = false;
  radios.forEach(radio => {
    if (radio.checked) radioSeleccionado = true;
  });

  if (!radioSeleccionado || textarea.value.trim() === '') {
    // Mostrar mensaje de error
    mensajeError.classList.remove('d-none');

    // Ocultar después de 3 segundos
    setTimeout(() => {
      mensajeError.classList.add('d-none');
    }, 3000);
  } else {
    // Ocultar error si estaba visible
    mensajeError.classList.add('d-none');

    // Mostrar el modal de confirmación
    const modalProblema = new bootstrap.Modal(document.getElementById('modalProblema'));
    modalProblema.show();
  }
}

////////////////////////////VUELVE AL ESTADO EN ORDEN////////////////////////////////////////////////
function limpiarFormularioProblema() {
  // Limpiar radio buttons
  const radios = document.querySelectorAll('input[name="prioridad"]');
  radios.forEach(radio => radio.checked = false);

  // Limpiar textarea
  const textarea = document.querySelector('#observaciones textarea');
  if (textarea) textarea.value = '';

  // Ocultar el contenido de problema y volver al estado inicial
  const contenido = document.getElementById("contenido-problema");
  contenido.classList.remove("active");

  const btn = document.getElementById("btn");
  btn.style.left = "0";

  const btnEnviarOrden = document.getElementById("btnEnviarOrden");
  btnEnviarOrden.classList.remove("d-none");
}
