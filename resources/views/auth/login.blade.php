<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('Css/Login.css') }}">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center h-100"> 
        <div class="login-card row">
            <div class="col-md-6 d-flex flex-column align-items-center justify-content-center px-4 py-2">
                <!-- Logos -->
                <div class="logo-container mb-4">
                    <img src="{{ asset('Logos_Login/logo1.png') }}" alt="Logo 1" class="logo">
                    <div class="divider"></div>
                    <img src="{{ asset('Logos_Login/logo2.png') }}" alt="Logo 2" class="logo">
                    <div class="divider"></div>
                    <img src="{{ asset('Logos_Login/logo3.png') }}" alt="Logo 3" class="logo">
                </div>

                <!-- Mensaje de estado de sesión -->
                @if (session('status'))
                    <div class="alert alert-success mb-3">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Título -->
                <h4 class="inicio">Inicio de Sesión</h4>
                <div class="underline"></div>
                <br>

                <!-- Formulario -->
                <form id="login-form" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label><i class="bi bi-person-fill icon"> |</i> Correo Electrónico</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="ejemplo@gmail.com" value="{{ old('email') }}" required autofocus autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label><i class="bi bi-lock"> |</i> Contraseña</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Contraseña" required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" class="form-check-input" id="recordar" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="recordar">Recuérdame</label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <button type="submit" class="btn-custom">Inicio de sesión</button>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" id="forgot-link" class="olvidé-contraseña">¿Olvidaste la contraseña?</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>