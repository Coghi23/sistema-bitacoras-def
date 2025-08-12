<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio de Sesión</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('Css/Login.css') }}">
</head>
<body>
     <div class="container d-flex align-items-center justify-content-center h-100"> 
        <div class="login-card row">
            <div class="col-md-6 d-flex flex-column align-items-center justify-content-center px-4 py-2">
                <div class="logo-container mb-4">
                    <img src="{{ asset('img/logo1.png') }}" alt="Logo 1" class="logo">
                    <div class="divider"></div>
                    <img src="{{ asset('img/logo2.png') }}" alt="Logo 2" class="logo">
                    <div class="divider"></div>
                    <img src="{{ asset('img/logo3.png') }}" alt="Logo 3" class="logo">
                </div>

                <h4 class="inicio">Inicio de Sesión</h4>
                <div class="underline"></div>
                <br>

                <form id="login-form">
                    <div class="mb-3 position-relative">
                        <label><i class="bi bi-person-fill icon"> |</i> Correo Electrónico</label>

                        <div id="errorCorreo" class="mensaje-error3 d-none">
                            <div class="icono">!</div>
                            <span id="textoErrorCorreo">Error</span>
                        </div>

                        <input type="email" id="correoLogin" class="form-control" placeholder="ejemplo@gmail.com">
                    </div>

                    <div class="mb-3 position-relative">
                        <label><i class="bi bi-lock"> |</i> Contraseña</label>

                        <div id="errorPassword" class="mensaje-error3 d-none">
                            <div class="icono">!</div>
                            <span id="textoErrorPassword">Error</span>
                        </div>

                        <input type="password" id="passwordLogin" class="form-control" placeholder="Contraseña">
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="recordar">
                        <label class="form-check-label" for="recordar">Recuérdame</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2 position-relative">
                        <div id="errorGeneral" class="mensaje-error d-none">
                            <div class="icono">!</div>
                            <span id="textoErrorGeneral">Por favor ingrese todos los datos</span>
                        </div>

                        <button type="button" class="btn-custom" onclick="validarLogin()">Inicio de sesión</button>
                        <a href="{{ route('password.request') }}" id="forgot-link" class="olvidé-contraseña">¿Olvidaste la contraseña?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/login.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>