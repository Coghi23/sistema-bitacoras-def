document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('login-form');
  const popup = document.getElementById('popup-exito');

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    popup.classList.remove('d-none');

    setTimeout(() => {
      popup.classList.add('d-none');
      
      window.location.href = 'index3.html';
    }, 1500);
  });

});


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
}5

function confirmarEnvioComentario() {
  // Cerrar modal si existe
  const modal = document.getElementById('modalComentario');
  if (modal) {
    modal.style.display = 'none';
  }

  // Mostrar alerta de éxito
  Swal.fire({
    text: 'Solicitud enviada con éxito!',
    icon: 'success',
    timer: 1500,
    showConfirmButton: false,
    customClass: {
      popup: 'rounded-4'
    }
  });

  // Limpiar campo de correo
  const emailInput = document.querySelector('#login-form input[type="email"]');
  if (emailInput) {
    emailInput.value = '';
  }
}
