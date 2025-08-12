<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer Contraseña</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('Css/Login.css') }}">
</head>
<body>
<div class="login-wrapper">
    <div class="login-card row">
        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center px-4 py-2">
            <div class="logo-container mb-4">
                <img src="{{ asset('img/logo1.png') }}" alt="Logo 1" class="logo">
                <div class="divider"></div>
                <img src="{{ asset('img/logo2.png') }}" alt="Logo 2" class="logo">
                <div class="divider"></div>
                <img src="{{ asset('img/logo3.png') }}" alt="Logo 3" class="logo">
            </div>
            <h5 class="inicio2">
                <i class="bi bi-arrow-left-circle-fill" style="cursor: pointer;" onclick="location.href='{{ route('login') }}'"> | </i>
                Cambio de contraseña
            </h5>
            <div class="underline2"></div>
        </div>

        <form id="login-form" novalidate>
            <div class="mb-3 position-relative">
                <label><i class="bi bi-person-fill"></i> | Correo Electrónico</label>
                <div id="errorCorreo" class="mensaje-error5 d-none" style="margin-bottom: 5px;">
                    <div class="icono">!</div>
                    <span id="textoErrorCorreo"></span>
                </div>
                <input type="email" id="correoInput" class="form-control input-limitado" placeholder="ejemplo@gmail.com" required />
            </div>

            <div class="mb-3 position-relative">
                <label class="label-amarillo">
                    <img src="{{ asset('img/candado.png') }}" width="20" /> | Nueva Contraseña
                </label>
                <div id="errorNuevaPass" class="mensaje-error5 d-none" style="margin-bottom: 5px;">
                    <div class="icono">!</div>
                    <span id="textoErrorNuevaPass"></span>
                </div>
                <input type="password" id="nuevaPassInput" class="form-control input-limitado" placeholder="Contraseña" required />
            </div>

            <div class="mb-3 position-relative">
                <label class="label-amarillo">
                    <img src="{{ asset('img/candado2.png') }}" width="20" /> | Confirmar Contraseña
                </label>
                <div id="errorConfirmPass" class="mensaje-error5 d-none" style="margin-bottom: 5px;">
                    <div class="icono">!</div>
                    <span id="textoErrorConfirmPass"></span>
                </div>
                <input type="password" id="confirmPassInput" class="form-control input-limitado" placeholder="Contraseña" required />
            </div>

            <div id="mensajeGeneral" class="mensaje-error4 d-none mb-3 d-flex align-items-center justify-content-center">
                <div class="icono">!</div>
                <span id="textoMensajeGeneral"></span>
            </div>

            <div class="col-md-6 d-flex flex-column align-items-center justify-content-center px-5 py-2">
                <div class="d-flex justify-content-center mb-2">
                    <button type="button" class="btn-custom3" id="BtnAfirmacion" onclick="confirmarEnvioCambioContraseña()">
                        Cambiar Contraseña
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

    <script src="{{ asset('js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>
</html>
