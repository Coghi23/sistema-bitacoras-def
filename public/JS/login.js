document.addEventListener('DOMContentLoaded', function () {
  // Correos válidos simulados
  const correosValidos = ['usuario1@gmail.com', 'admin@ejemplo.com', 'prueba@correo.com'];

  // --------------------- VALIDAR RECUPERACIÓN ---------------------
  const btnRecuperar = document.getElementById('BtnAfirmacion');
  if (btnRecuperar) {
    btnRecuperar.addEventListener('click', () => {
      const correoInput = document.getElementById('correoInput');
      const mensajeVacio = document.getElementById('mensajeVacio');
      const mensajeNoExiste = document.getElementById('mensajeNoExiste');
      const correo = correoInput.value.trim().toLowerCase();

      mensajeVacio.classList.add('d-none');
      mensajeNoExiste.classList.add('d-none');

      if (correo === '') {
        mensajeVacio.classList.remove('d-none');
        setTimeout(() => mensajeVacio.classList.add('d-none'), 1500);
        return;
      }

      if (!correosValidos.includes(correo)) {
        mensajeNoExiste.classList.remove('d-none');
        setTimeout(() => mensajeNoExiste.classList.add('d-none'), 2000);
        return;
      }

      Swal.fire({
        icon: 'success',
        title: '¡Solicitud realizada con éxito!',
        showConfirmButton: false,
        timer: 1500,
        backdrop: 'rgba(0,0,0,0.4)',
        scrollbarPadding: false,
        customClass: {
          popup: 'rounded-4'
        }
      });
    });

    // Limpiar errores al escribir
    document.getElementById('correoInput').addEventListener('input', () => {
      document.getElementById('mensajeVacio').classList.add('d-none');
      document.getElementById('mensajeNoExiste').classList.add('d-none');
    });
  }

  // --------------------- VALIDAR CAMBIO DE CONTRASEÑA ---------------------
  const formCambio = document.getElementById('login-form');
  if (formCambio) {
    formCambio.addEventListener('submit', function (event) {
      event.preventDefault();

      const correo = document.getElementById('correoInput').value.trim().toLowerCase();
      const nuevaPass = document.getElementById('nuevaPassInput')?.value.trim();
      const confirmPass = document.getElementById('confirmPassInput')?.value.trim();

      const errorCorreo = document.getElementById('errorCorreo');
      const textoErrorCorreo = document.getElementById('textoErrorCorreo');
      const errorNuevaPass = document.getElementById('errorNuevaPass');
      const textoErrorNuevaPass = document.getElementById('textoErrorNuevaPass');
      const errorConfirmPass = document.getElementById('errorConfirmPass');
      const textoErrorConfirmPass = document.getElementById('textoErrorConfirmPass');
      const mensajeGeneral = document.getElementById('mensajeGeneral');
      const textoMensajeGeneral = document.getElementById('textoMensajeGeneral');

      [errorCorreo, errorNuevaPass, errorConfirmPass, mensajeGeneral].forEach(el => el?.classList.add('d-none'));

      let hayError = false;

      if (!correo || !nuevaPass || !confirmPass) {
        textoMensajeGeneral.textContent = 'Por favor ingrese todos los datos';
        mensajeGeneral.classList.remove('d-none');
        setTimeout(() => mensajeGeneral.classList.add('d-none'), 2000);
        hayError = true;
      }

      if (correo && !correosValidos.includes(correo)) {
        textoErrorCorreo.textContent = 'Datos incorrectos';
        errorCorreo.classList.remove('d-none');
        setTimeout(() => errorCorreo.classList.add('d-none'), 2000);
        hayError = true;
      }

      if (nuevaPass && nuevaPass.length < 10) {
        textoErrorNuevaPass.textContent = 'La contraseña debe tener mínimo 10 caracteres';
        errorNuevaPass.classList.remove('d-none');
        setTimeout(() => errorNuevaPass.classList.add('d-none'), 2000);
        hayError = true;
      }

      if (confirmPass && confirmPass !== nuevaPass) {
        textoErrorConfirmPass.textContent = 'Datos incorrectos';
        errorConfirmPass.classList.remove('d-none');
        setTimeout(() => errorConfirmPass.classList.add('d-none'), 2000);
        hayError = true;
      }

      if (!hayError) {
        Swal.fire({
          icon: 'success',
          title: '¡Contraseña cambiada con éxito!',
          showConfirmButton: false,
          timer: 1500,
          backdrop: 'rgba(0,0,0,0.4)',
          scrollbarPadding: false
        });
        this.reset();
      }
    });

    document.getElementById('correoInput')?.addEventListener('input', () => {
      document.getElementById('errorCorreo')?.classList.add('d-none');
      document.getElementById('mensajeGeneral')?.classList.add('d-none');
    });

    document.getElementById('nuevaPassInput')?.addEventListener('input', () => {
      document.getElementById('errorNuevaPass')?.classList.add('d-none');
      document.getElementById('mensajeGeneral')?.classList.add('d-none');
    });

    document.getElementById('confirmPassInput')?.addEventListener('input', () => {
      const confirm = document.getElementById('confirmPassInput').value.trim();
      const nueva = document.getElementById('nuevaPassInput').value.trim();
      if (confirm === nueva) {
        document.getElementById('errorConfirmPass')?.classList.add('d-none');
      }
      document.getElementById('mensajeGeneral')?.classList.add('d-none');
    });
  }
});


    function validarLogin() {
      const correo = document.getElementById('correoLogin').value.trim().toLowerCase();
      const password = document.getElementById('passwordLogin').value.trim();

      const errorGeneral = document.getElementById('errorGeneral');
      const textoErrorGeneral = document.getElementById('textoErrorGeneral');

      const errorCorreo = document.getElementById('errorCorreo');
      const errorPassword = document.getElementById('errorPassword');
      const textoErrorCorreo = document.getElementById('textoErrorCorreo');
      const textoErrorPassword = document.getElementById('textoErrorPassword');

  
      errorCorreo.classList.add('d-none');
      errorPassword.classList.add('d-none');
      errorGeneral.classList.add('d-none');


      const usuariosValidos = [
        { correo: 'usuario1@gmail.com', password: '123456' },
        { correo: 'admin@ejemplo.com', password: 'admin123' },
        { correo: 'prueba@correo.com', password: 'abc123' }
      ];


      if (correo === '' && password === '') {
        textoErrorGeneral.textContent = 'Por favor ingrese todos los datos';
        errorGeneral.classList.remove('d-none');
        setTimeout(() => errorGeneral.classList.add('d-none'), 2000);
        return;
      }

      if (correo === '') {
        textoErrorCorreo.textContent = 'Por favor ingrese los datos';
        errorCorreo.classList.remove('d-none');
        setTimeout(() => errorCorreo.classList.add('d-none'), 1500);
        return;
      }

      if (password === '') {
        textoErrorPassword.textContent = 'Por favor ingrese los datos';
        errorPassword.classList.remove('d-none');
        setTimeout(() => errorPassword.classList.add('d-none'), 1500);
        return;
      }

      const usuarioValido = usuariosValidos.find(
        user => user.correo === correo && user.password === password
      );

      if (!usuarioValido) {
        textoErrorCorreo.textContent = 'Datos incorrectos';
        textoErrorPassword.textContent = 'Datos incorrectos';
        errorCorreo.classList.remove('d-none');
        errorPassword.classList.remove('d-none');
        setTimeout(() => {
          errorCorreo.classList.add('d-none');
          errorPassword.classList.add('d-none');
        }, 2000);
        return;
      }


      const popupExito = document.getElementById('popup-exito');
      if (popupExito) {
        popupExito.classList.remove('d-none');
        setTimeout(() => popupExito.classList.add('d-none'), 2000);
      }
    }



function confirmarEnvioComentario() {

  const modal = document.getElementById('modalComentario');
  if (modal) {
    modal.style.display = 'none'; 
  }

  
  Swal.fire({
    text: '¡Solicitud realizada con éxito!',
    icon: 'success',
    timer: 1500,
    showConfirmButton: false,
    customClass: {
      popup: 'rounded-4'
    }
  });


  const radios = document.querySelectorAll('input[name="prioridad"]');
  radios.forEach(radio => radio.checked = false);

  document.querySelector('#observaciones textarea').value = '';
}


function confirmarEnvioCambioContraseña() {
  const correo = document.getElementById('correoInput').value.trim().toLowerCase();
  const nuevaPass = document.getElementById('nuevaPassInput').value.trim();
  const confirmPass = document.getElementById('confirmPassInput').value.trim();

  const errorCorreo = document.getElementById('errorCorreo');
  const textoErrorCorreo = document.getElementById('textoErrorCorreo');

  const errorNuevaPass = document.getElementById('errorNuevaPass');
  const textoErrorNuevaPass = document.getElementById('textoErrorNuevaPass');

  const errorConfirmPass = document.getElementById('errorConfirmPass');
  const textoErrorConfirmPass = document.getElementById('textoErrorConfirmPass');

  const mensajeGeneral = document.getElementById('mensajeGeneral');
  const textoMensajeGeneral = document.getElementById('textoMensajeGeneral');


  [errorCorreo, errorNuevaPass, errorConfirmPass, mensajeGeneral].forEach(el => el.classList.add('d-none'));

  let hayError = false;


  if (!correo && !nuevaPass && !confirmPass) {
    textoMensajeGeneral.textContent = 'Por favor ingrese todos los datos';
    mensajeGeneral.classList.remove('d-none');
    setTimeout(() => mensajeGeneral.classList.add('d-none'), 2000);
    return;
  }


  const correosValidos = ['usuario1@gmail.com', 'admin@ejemplo.com', 'prueba@correo.com'];
  if (!correo) {
    textoErrorCorreo.textContent = 'Debe ingresar el correo';
    errorCorreo.classList.remove('d-none');
    hayError = true;
  } else if (!correosValidos.includes(correo)) {
    textoErrorCorreo.textContent = 'Correo no registrado';
    errorCorreo.classList.remove('d-none');
    hayError = true;
  }

  if (!nuevaPass) {
    textoErrorNuevaPass.textContent = 'Debe ingresar una contraseña';
    errorNuevaPass.classList.remove('d-none');
    hayError = true;
  } else if (nuevaPass.length < 10) {
    textoErrorNuevaPass.textContent = 'La contraseña debe tener mínimo 10 caracteres';
    errorNuevaPass.classList.remove('d-none');
    hayError = true;
  }

  if (!confirmPass) {
    textoErrorConfirmPass.textContent = 'Debe confirmar la contraseña';
    errorConfirmPass.classList.remove('d-none');
    hayError = true;
  } else if (nuevaPass !== confirmPass) {
    textoErrorConfirmPass.textContent = 'Las contraseñas no coinciden';
    errorConfirmPass.classList.remove('d-none');
    hayError = true;
  }

 
  if (hayError) return;


  mensajeGeneral.classList.add('d-none');

  Swal.fire({
    icon: 'success',
    title: '¡Contraseña cambiada con éxito!',
    showConfirmButton: false,
    timer: 1800,
    backdrop: 'rgba(0,0,0,0.4)',
    scrollbarPadding: false,
    customClass: {
      popup: 'rounded-4'
    }
  });

  document.getElementById('login-form').reset();
}





function confirmarEnvioComentario() {
  const correoInput = document.getElementById('correoInput');
  const correo = correoInput.value.trim().toLowerCase();

  const mensajeVacio = document.getElementById('mensajeVacio');
  const mensajeNoExiste = document.getElementById('mensajeNoExiste');


  mensajeVacio.classList.add('d-none');
  mensajeNoExiste.classList.add('d-none');


  const correosValidos = ['usuario1@gmail.com', 'admin@ejemplo.com', 'prueba@correo.com'];


  if (!correo) {
    mensajeVacio.classList.remove('d-none');
    setTimeout(() => mensajeVacio.classList.add('d-none'), 2000);
    return;
  }

  
  if (!correosValidos.includes(correo)) {
    mensajeNoExiste.classList.remove('d-none');
    setTimeout(() => mensajeNoExiste.classList.add('d-none'), 2000);
    return;
  }

  Swal.fire({
    icon: 'success',
    title: '¡Solicitud realizada con éxito!',
    showConfirmButton: false,
    timer: 2000,
    backdrop: 'rgba(0,0,0,0.4)',
    scrollbarPadding: false,
    customClass: {
      popup: 'rounded-4'
    }
  });

  
  correoInput.value = '';
}
