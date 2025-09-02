@extends('Template-administrador')

@section('title', 'Dashboard - Sistema de Bitácoras')

@section('content')
<link rel="stylesheet" href="{{ asset('Css/inicio.css') }}">

<style>
    /* Aseguramos que use el fondo original */
    body {
        background-image: url("/img/template/fondoPrincipal.jpg") !important;
        background-size: cover !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        background-attachment: fixed !important;
    }
    
    .module-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        border: 2px solid rgba(223, 223, 223, 0.8);
    }
    
    .module-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(19, 68, 150, 0.3);
        text-decoration: none;
        color: inherit;
    }
    
    .module-icon {
        font-size: 2.5rem;
        color: #134496;
        margin-bottom: 1rem;
    }
    
    .module-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .module-description {
        color: #666;
        font-size: 0.9rem;
    }
    
    .permissions-badges {
        margin-top: 0.5rem;
    }
    
    .permission-badge {
        display: inline-block;
        background: #e3f2fd;
        color: #1976d2;
        padding: 0.2rem 0.5rem;
        border-radius: 10px;
        font-size: 0.75rem;
        margin-right: 0.3rem;
        margin-bottom: 0.3rem;
    }
    
    .role-badge {
        background: linear-gradient(45deg, #134496, #76C0E6);
        color: white;
        padding: 0.3rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        margin-right: 0.5rem;
        box-shadow: 0 2px 8px rgba(19, 68, 150, 0.3);
    }
    
    .no-modules {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 3rem;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        border: 2px solid rgba(223, 223, 223, 0.8);
        margin: 2rem auto;
        max-width: 600px;
    }
    
    .modules-section {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        padding: 2rem;
        margin-top: 2rem;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .modules-title {
        color: white;
        background: rgba(19, 68, 150, 0.8);
        padding: 1rem 2rem;
        border-radius: 10px;
        margin-bottom: 2rem;
        text-align: center;
        box-shadow: 0 4px 20px rgba(118, 192, 230, 0.3);
    }
    
    /* Ajustes para información del usuario */
    .user-info {
        background: rgba(118, 192, 230, 0.9);
        color: white;
        border: 2px solid #DFDFDF;
        padding: 15px 20px;
        border-radius: 10px;
        margin-top: 1rem;
        box-shadow: 0 4px 20px rgba(19, 68, 150, 0.3);
    }
</style>

<div class="container-fluid">
    <div class="row">
        {{-- Columna del contenido principal --}}
        <div class="col-lg-6 col-md-12">
            <div class="container d-flex flex-column justify-content-start align-items-start">
                {{-- Mensaje de bienvenida - usando el estilo original --}}
                <div class="mb-4" id="mensaje-bienvenida">
                    <h5 class="fw-bold text-white">¡Hola! Gracias por acceder al sistema.</h5>
                    <p class="text-white mb-0">Mantener un registro claro y preciso es fundamental para una gestión eficiente.</p>
                    
                    {{-- Información del usuario y roles integrada --}}
                    <div class="user-info mt-3">
                        <p class="mb-2">
                            <i class="bi bi-person-circle me-2"></i>
                            <strong>Bienvenido, {{ $user->name }}!</strong>
                        </p>
                        <div class="mb-0">
                            <strong>Roles asignados: </strong>
                            @foreach($roles as $rol)
                                <span class="role-badge">{{ ucfirst($rol) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                {{-- Mensaje de seguridad - usando el estilo original --}}
                <div id="mensaje-seguridad">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-lock-fill fs-3 text-white"></i>
                        <p class="mb-0 text-white">
                            La seguridad de los datos es nuestra prioridad. <br>
                            Cierra sesión al finalizar.
                        </p>
                    </div>
                </div>
                
                {{-- Módulos disponibles --}}
                @if(count($modulos) > 0)
                    <div class="modules-section w-100">
                        <div class="modules-title">
                            <h5 class="mb-0">
                                <i class="bi bi-grid-3x3-gap-fill me-2"></i>
                                Módulos Disponibles
                            </h5>
                        </div>
                        <div class="row">
                            @foreach($modulos as $moduloKey => $modulo)
                                <div class="col-lg-6 col-md-6 mb-3">
                                    <a href="{{ route($modulo['ruta']) }}" class="module-card d-block">
                                        <div class="text-center">
                                            <i class="bi {{ $modulo['icono'] }} module-icon"></i>
                                            <div class="module-title">{{ $modulo['titulo'] }}</div>
                                            <div class="module-description">{{ $modulo['descripcion'] }}</div>
                                            
                                            <!-- Mostrar permisos específicos -->
                                            <div class="permissions-badges">
                                                @if(isset($modulo['permisos']['view']))
                                                    <span class="permission-badge">Ver</span>
                                                @endif
                                                @if(isset($modulo['permisos']['create']))
                                                    <span class="permission-badge">Crear</span>
                                                @endif
                                                @if(isset($modulo['permisos']['edit']))
                                                    <span class="permission-badge">Editar</span>
                                                @endif
                                                @if(isset($modulo['permisos']['delete']))
                                                    <span class="permission-badge">Eliminar</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    {{-- Sin módulos disponibles --}}
                    <div class="no-modules">
                        <i class="bi bi-exclamation-triangle" style="font-size: 4rem; color: #ffc107; margin-bottom: 1rem;"></i>
                        <h4>Sin módulos disponibles</h4>
                        <p class="text-muted">
                            No tienes permisos asignados para acceder a ningún módulo del sistema.
                            <br>
                            Contacta al administrador para solicitar los permisos necesarios.
                        </p>
                    </div>
                @endif
            </div>
        </div>
        
        {{-- Columna de la imagen de oficina - usando el estilo original --}}
        <div class="col-lg-6 col-md-12 d-flex align-items-center justify-content-center">
            <div id="Img-Oficina"></div>
        </div>
    </div>
</div>
@endsection
