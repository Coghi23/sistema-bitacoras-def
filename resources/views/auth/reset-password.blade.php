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
    <div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center p-3"> 
        <div class="login-card">
            <div class="row g-0 h-100">
                <div class="col-12 d-flex flex-column align-items-center justify-content-center px-4 py-3">
                    <!-- Logos -->
                    <div class="logo-container mb-3 d-flex align-items-center flex-wrap justify-content-center">
                        <img src="{{ asset('Logos_Login/logo1.png') }}" alt="Logo 1" class="logo">
                        <div class="divider d-none d-sm-block"></div>
                        <img src="{{ asset('Logos_Login/logo2.png') }}" alt="Logo 2" class="logo">
                        <div class="divider d-none d-sm-block"></div>
                        <img src="{{ asset('Logos_Login/logo3.png') }}" alt="Logo 3" class="logo">
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
                            <i class="bi bi-arrow-left-circle-fill"></i>
                        </a>
                        <h4 class="inicio text-center mb-0">Restablecer Contraseña</h4>
                    </div>
                    <div class="underline mx-auto"></div>
                    
                    <!-- Mensaje explicativo -->
                    <p class="text-center mt-3 mb-4" style="color: #134496; max-width: 400px;">
                        Ingresa tu nueva contraseña para completar el restablecimiento.
                    </p>

                    <!-- Formulario -->
                    <form id="reset-form" method="POST" action="{{ route('password.store') }}" class="w-100" style="max-width: 400px;">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address (readonly) -->
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-envelope-fill icon"> |</i> Correo Electrónico</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $request->email) }}" required readonly style="background-color: #f8f9fa;">
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-lock-fill icon"> |</i> Nueva Contraseña</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Nueva contraseña" required autocomplete="new-password">
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-3">
                            <label class="form-label"><i class="bi bi-lock-fill icon"> |</i> Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                   placeholder="Confirmar nueva contraseña" required autocomplete="new-password">
                            @error('password_confirmation')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 mb-2">
                            <button type="submit" class="btn-custom">Restablecer Contraseña</button>
                            <a href="{{ route('login') }}" class="olvidé-contraseña text-center">Volver al login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
