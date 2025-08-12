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
                
                <!-- Logos -->
                <div class="logo-container mb-3 d-flex align-items-center flex-wrap justify-content-center">
                    <img src="{{ asset('Logos_Login/logo1.png') }}" alt="Logo 1" class="logo">
                    <div class="divider d-none d-sm-block"></div>
                    <img src="{{ asset('Logos_Login/logo2.png') }}" alt="Logo 2" class="logo">
                    <div class="divider d-none d-sm-block"></div>
                    <img src="{{ asset('Logos_Login/logo3.png') }}" alt="Logo 3" class="logo">
                </div>

                <!-- Mensaje de estado -->
                @if (session('status'))
                    <div class="alert alert-success mb-3 w-100" style="max-width: 400px;">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Título -->
                <h4 class="inicio text-center">Inicio de Sesión</h4>
                <div class="underline mx-auto"></div>
                <br>

                <!-- Formulario -->
                <form id="login-form" method="POST" action="{{ route('login') }}" class="w-100" style="max-width: 400px;">
                    @csrf

                    <!-- Correo -->
                    <div class="mb-3 position-relative">
                        <label class="form-label"><i class="bi bi-person-fill icon"> |</i> Correo Electrónico</label>

                        @error('email')
                            <div class="mensaje-error3">
                                <div class="icono">!</div>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror

                        <input type="email" name="email" id="correoLogin" class="form-control @error('email') is-invalid @enderror" placeholder="ejemplo@gmail.com" value="{{ old('email') }}" required autofocus autocomplete="username">
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-3 position-relative">
                        <label class="form-label"><i class="bi bi-lock"> |</i> Contraseña</label>

                        @error('password')
                            <div class="mensaje-error3">
                                <div class="icono">!</div>
                                <span>{{ $message }}</span>
                            </div>
                        @enderror

                        <input type="password" name="password" id="passwordLogin" class="form-control @error('password') is-invalid @enderror" placeholder="Contraseña" required autocomplete="current-password">
                    </div>

                    <!-- Recuérdame -->
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" class="form-check-input" id="recordar" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="recordar">Recuérdame</label>
                    </div>

                    <!-- Botón + Olvidé contraseña -->
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <button type="submit" class="btn-custom">Inicio de sesión</button>
                        <a href="{{ route('password.request') }}" id="forgot-link" class="olvidé-contraseña ms-auto">¿Olvidaste la contraseña?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
