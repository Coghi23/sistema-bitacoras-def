<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Olvidaste la Contraseña</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('Css/Login.css') }}">
</head>
<body>
<div class="login-wrapper d-flex align-items-center justify-content-center vh-100">
    <div class="login-card row">
            <div class="row g-0 h-100">
                <div class="col-md-6 d-flex flex-column align-items-center justify-content-center px-4 py-2">
                    <!-- Logos -->
                    <div class="logo-container mb-3 d-flex align-items-center flex-wrap justify-content-center">
                    <img src="{{ asset('img/logo1.png') }}" alt="Logo 1" class="logo">
                    <div class="divider d-none d-sm-block"></div>
                    <img src="{{ asset('img/logo2.png') }}" alt="Logo 2" class="logo">
                    <div class="divider d-none d-sm-block"></div>
                    <img src="{{ asset('img/logo3.png') }}" alt="Logo 3" class="logo">
                </div>

                    <!-- Mensaje de estado de sesión -->
                    @if (session('status'))
                        <div class="alert alert-success mb-3 w-100" style="max-width: 400px;">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Título con botón de regreso -->
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <a href="{{ route('login') }}" class="btn btn-link p-0 me-3" style="color: #EFC737; font-size: 1.5rem;">
                        </a>
                        <h5 class="inicio2">
                                <i class="biO bi-arrow-left-circle-fill" style="cursor: pointer;" onclick="location.href='index.html'"></i> | Olvidaste la contraseña
                        </h5>                    
                    </div>

                    <div class="underline2"></div>
                    <!-- Formulario -->
                    <form id="forgot-form" method="POST" action="{{ route('password.email') }}" class="w-100" style="max-width: 400px;">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-envelope-fill icon"> |</i> Correo Electrónico</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="ejemplo@gmail.com" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                            <div class="d-flex justify-content-center mb-2">
                                <button type="submit" class="btn-custom">Recuperar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>