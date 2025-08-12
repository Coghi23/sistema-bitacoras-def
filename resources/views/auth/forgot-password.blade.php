<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar Contraseña</title>
    <!-- Bootstrap CSS -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/Login.css') }}">
</head>
<body>
 <div class="login-wrapper d-flex align-items-center justify-content-center vh-100">
    <div class="login-card row">

        <div class="col-md-6 d-flex flex-column align-items-center justify-content-center px-4 py-2">
            <div class="logo-container mb-4">
                <img src="{{ asset('img/logo1.png') }}" alt="Logo 1" class="logo" />
                <div class="divider"></div>
                <img src="{{ asset('img/logo2.png') }}" alt="Logo 2" class="logo" />
                <div class="divider"></div>
                <img src="{{ asset('img/logo3.png') }}" alt="Logo 3" class="logo" />
            </div>

            <h5 class="inicio2">
                <i class="bi bi-arrow-left-circle-fill" style="cursor: pointer;" onclick="location.href='{{ route('login') }}'"></i> | Olvidaste la contraseña
            </h5>

            <div class="underline2"></div>
        </div>

        <form id="login-form" class="text-between">
            <br />
            <div class="mb-5 position-relative">
                <label class="fs-5">
                    <i class="bi bi-person-fill fs-4 ms-4"></i> | Correo Electrónico
                </label>

                <div id="mensajeVacio" class="mensaje-error2 d-none">
                    <div class="icono">!</div>
                    <span>Por favor ingrese los datos</span>
                </div>

                <div id="mensajeNoExiste" class="mensaje-error2 d-none">
                    <div class="icono">!</div>
                    <span>El correo no está registrado</span>
                </div>

                <input
                    type="email"
                    id="correoInput"
                    class="form-control input-limitado"
                    placeholder="ejemplo@gmail.com"
                    required
                />
            </div>

            <div class="col-md-6 d-flex flex-column align-items-center justify-content-center px-4 py-2">
                <div class="d-flex justify-content-center mb-2">
                    <button type="button" class="btn-custom3" id="BtnAfirmacion" onclick="confirmarEnvioComentario()">
                        Recuperar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/login.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>