@extends('Template-administrador')

@section('content')
<style>
/* Responsive adjustments for Usuarios */
@media (max-width: 768px) {
    .main-content {
        margin-left: 0 !important;
        padding: 0.5rem !important;
    }
    
    .search-bar-wrapper {
        flex-direction: column !important;
        gap: 0.75rem;
    }
    
    .search-bar {
        width: 100% !important;
        min-width: auto !important;
    }
    
    .btn-agregar {
        width: 100% !important;
        margin-left: 0 !important;
        margin-top: 0 !important;
        justify-content: center !important;
        font-size: 1rem !important;
    }
    
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .table th, .table td {
        padding: 0.4rem 0.3rem !important;
        font-size: 0.8rem;
        vertical-align: middle;
    }
    
    .modal-dialog {
        margin: 0.5rem !important;
        max-width: calc(100% - 1rem) !important;
    }
    
    .form-responsive {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    
    .form-responsive .form-label,
    .form-responsive label {
        width: 100% !important;
        margin-bottom: 0.5rem !important;
        text-align: left !important;
    }
    
    .form-responsive input,
    .form-responsive select {
        width: 100% !important;
    }
    
    .filter-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .filter-buttons .btn {
        width: 100%;
    }
    
    .modal-footer {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .modal-footer .btn {
        width: 100% !important;
    }
}

@media (max-width: 576px) {
    .table {
        font-size: 0.75rem;
    }
    
    .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.5rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.4rem;
        font-size: 0.7rem;
    }
    
    .alert {
        font-size: 0.85rem;
        padding: 0.5rem;
    }
}

/* Mejorar visibilidad de iconos de acción */
.icon-editar, .icon-eliminar {
    font-size: 1.2rem;
    transition: all 0.2s ease;
}

.icon-editar:hover {
    color: #0d6efd !important;
    transform: scale(1.1);
}

.icon-eliminar:hover {
    color: #dc3545 !important;
    transform: scale(1.1);
}
</style>

<div id="usuarios-container" class="wrapper">
    <div id="main-content" class="main-content p-4 container-fluid" style="margin-left: 90px;">
        {{-- Header con búsqueda y botón agregar --}}
        <div id="header-section" class="row align-items-end mb-4">
            <div id="search-wrapper" class="search-bar-wrapper mb-4 d-flex flex-wrap align-items-center">
                <div id="search-bar-container" class="search-bar flex-grow-1">
                    <form id="busquedaForm" method="GET" action="{{ route('usuario.index') }}" class="w-100 position-relative">
                        <span class="search-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="inputBusqueda" class="form-control" placeholder="Buscar usuario..." 
                               name="busquedaUsuario" value="{{ request('busquedaUsuario') }}" autocomplete="off">
                        @if(request('busquedaUsuario'))
                        <button type="button" id="limpiarBusqueda" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" 
                                title="Limpiar búsqueda" style="background: transparent;">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        @endif
                        {{-- Mantener el parámetro inactivos en la búsqueda --}}
                        @if(request('inactivos'))
                            <input type="hidden" name="inactivos" value="1">
                        @endif
                    </form>
                </div>
                @can('create_usuarios')
                    <button id="btn-agregar-usuario" class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
                        data-bs-toggle="modal" data-bs-target="#modalUsuario"
                        title="Agregar Usuario" style="background-color: #134496; font-size: 1.2rem; @if(Auth::user() && Auth::user()->hasRole('director')) display: none; @endif">
                        Agregar <i class="bi bi-plus-circle ms-2"></i>
                    </button>
                @endcan
            </div>
        </div>

        {{-- Mensajes de éxito/error --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('eliminado'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('eliminado') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Indicador de resultados de búsqueda --}}
        @if(request('busquedaUsuario'))
            <div id="search-results" class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <span>
                    Mostrando {{ $usuarios->count() }} resultado(s) para "<strong>{{ request('busquedaUsuario') }}</strong>"
                    <a href="{{ route('usuario.index', request('inactivos') ? ['inactivos' => 1] : []) }}" class="btn btn-sm btn-outline-primary ms-2">Ver todos</a>
                </span>
            </div>
        @endif

        @can('view_usuarios')
            {{-- Botones para mostrar/ocultar usuarios inactivos --}}
            <div id="filter-buttons" class="filter-buttons d-flex flex-wrap gap-2 mb-3">
                <a href="{{ route('usuario.index', ['inactivos' => 1]) }}" class="btn btn-warning">
                    Mostrar inactivos
                </a>
                <a href="{{ route('usuario.index') }}" class="btn btn-primary">
                    Mostrar activos
                </a>
            </div>

            {{-- Tabla de Usuarios --}}
            <div id="tabla-usuarios" class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr id="usuarios-header-row" class="header-row">
                            <th class="col-nombre">Nombre</th>
                            <th class="col-cedula">Cédula</th>
                            <th class="col-email">Correo Electrónico</th>
                            <th class="col-rol">Rol</th>
                            <th class="col-estado">Estado</th>
                            <th class="col-acciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="usuarios-tbody">
                        @forelse($usuarios as $usuario)
                        <tr id="usuario-row-{{ $usuario->id }}" class="record-row">
                            <td class="col-nombre">{{ $usuario->name }}</td>
                            <td class="col-cedula">{{ $usuario->cedula }}</td>
                            <td class="col-email">{{ $usuario->email }}</td>
                            <td class="col-rol">
                                @if($usuario->getRoleNames()->isNotEmpty())
                                    <span class="badge bg-info">{{ ucfirst($usuario->getRoleNames()->first()) }}</span>
                                @else
                                    <span class="badge bg-secondary">Sin rol</span>
                                @endif
                            </td>
                            <td class="col-estado">
                                <span id="estado-badge-{{ $usuario->id }}" class="badge {{ isset($usuario->condicion) && $usuario->condicion ? 'bg-success' : 'bg-danger' }}">
                                    {{ isset($usuario->condicion) && $usuario->condicion ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="col-acciones">
                                <div id="actions-{{ $usuario->id }}" class="d-flex flex-column flex-md-row justify-content-center gap-1">
                                    @can('edit_usuarios')
                                        <button id="btn-edit-{{ $usuario->id }}" class="btn p-0 me-2" 
                                                data-bs-toggle="modal" data-bs-target="#modalEditarUsuario{{ $usuario->id }}" 
                                                title="Editar usuario">
                                            <i class="bi bi-pencil icon-editar"></i>
                                        </button>
                                    @endcan
                                    @can('delete_usuarios')
                                        @if($usuario->condicion == 1)
                                            <button id="btn-delete-{{ $usuario->id }}" class="btn p-0 me-2" 
                                                    data-bs-toggle="modal" data-bs-target="#modalEliminarUsuario{{ $usuario->id }}" 
                                                    title="Desactivar usuario">
                                                <i class="bi bi-trash icon-eliminar"></i>
                                            </button>
                                        @else
                                            <button id="btn-restore-{{ $usuario->id }}" class="btn p-0 me-2" 
                                                    data-bs-toggle="modal" data-bs-target="#modalReactivarUsuario{{ $usuario->id }}" 
                                                    title="Activar usuario">
                                                <i class="bi bi-arrow-counterclockwise icon-eliminar text-success"></i>
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="no-usuarios-row" class="record-row">
                            <td class="text-center" colspan="6">
                                <div class="text-muted py-4">
                                    <i class="bi bi-people display-4 mb-3"></i>
                                    <h5>No hay usuarios registrados</h5>
                                    <p>Los usuarios aparecerán aquí cuando se registren.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endcan
    </div>

    {{-- Modal Crear Usuario --}}
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content rounded-4 shadow-lg">
                <form id="formCrearUsuario" method="POST" action="{{ route('usuario.store') }}">
                    @csrf
                    <div class="modal-header custom-header text-white px-4 py-3 position-relative justify-content-center">
                        <button type="button" class="btn p-0 d-flex align-items-center position-absolute start-0 ms-3" data-bs-dismiss="modal" aria-label="Cerrar">
                            <div class="circle-yellow d-flex justify-content-center align-items-center">
                                <i class="fas fa-arrow-left text-blue-forced"></i>
                            </div>
                            <div class="linea-vertical-amarilla ms-2"></div>
                        </button>
                        <h5 class="modal-title m-0" id="modalUsuarioLabel">Registro de Usuarios</h5>
                    </div>
                    <div class="linea-divisoria-horizontal"></div>
                    <div class="modal-body px-4 pt-3">
                        {{-- Nombre Completo --}}
                        <div id="nombre-field" class="mb-3 form-responsive d-flex align-items-center justify-content-between">
                            <label for="nombre-crear" class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-user text-primary me-2"></i>Nombre Completo:
                            </label>
                            <input type="text" id="nombre-crear" name="name" class="form-control rounded-4 w-50" placeholder="Nombre..." required>
                        </div>
                        
                        {{-- Cédula --}}
                        <div id="cedula-field" class="mb-3 form-responsive d-flex align-items-center justify-content-between">
                            <label for="cedula-crear" class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-id-card text-info me-2"></i>Cédula:
                            </label>
                            <input type="text" id="cedula-crear" name="cedula" class="form-control rounded-4 w-50" placeholder="Cédula..." required>
                        </div>
                        
                        {{-- Correo Electrónico --}}
                        <div id="email-field" class="mb-3 form-responsive d-flex align-items-center justify-content-between">
                            <label for="email-crear" class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-envelope text-warning me-2"></i>Correo Electrónico:
                            </label>
                            <input type="email" id="email-crear" name="email" class="form-control rounded-4 w-50" placeholder="Correo Electrónico..." required>
                        </div>
                        
                        {{-- Información sobre configuración de contraseña --}}
                        <div id="password-info" class="mb-3 d-flex align-items-center justify-content-between">
                            <div class="w-100 text-center">
                                <div class="alert alert-info d-flex align-items-center" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span>Se enviará un correo automáticamente para que el usuario configure su contraseña.</span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Rol --}}
                        <div id="rol-field" class="mb-3 form-responsive d-flex align-items-center justify-content-between">
                            <label for="rol-crear" class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-user-tag text-success me-2"></i>Rol:
                            </label>
                            <div class="position-relative w-50">
                                <select id="rol-crear" name="role" class="form-control rounded-4" required>
                                    <option value="">Seleccione un rol</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->name }}">{{ ucfirst($rol->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer px-4 pb-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-crear w-100 w-md-auto">Registrar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modales de edición y eliminación --}}
    @foreach($usuarios as $usuario)
    {{-- Modal Editar Usuario --}}
    <div class="modal fade" id="modalEditarUsuario{{ $usuario->id }}" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel{{ $usuario->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content rounded-4 shadow-lg">
                <form id="formEditarUsuario{{ $usuario->id }}" method="POST" action="{{ route('usuario.update', $usuario->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header custom-header text-white px-4 py-3 position-relative justify-content-center">
                        <button type="button" class="btn p-0 d-flex align-items-center position-absolute start-0 ms-3" data-bs-dismiss="modal" aria-label="Cerrar">
                            <div class="circle-yellow d-flex justify-content-center align-items-center">
                                <i class="fas fa-arrow-left text-blue-forced"></i>
                            </div>
                            <div class="linea-vertical-amarilla ms-2"></div>
                        </button>
                        <h5 class="modal-title m-0" id="modalEditarUsuarioLabel{{ $usuario->id }}">Editar información de usuario</h5>
                    </div>
                    <div class="linea-divisoria-horizontal"></div>
                    <div class="modal-body px-4 pt-3">
                        {{-- Nombre Completo --}}
                        <div class="mb-3 form-responsive d-flex align-items-center justify-content-between">
                            <label for="nombre-edit-{{ $usuario->id }}" class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-user text-primary me-2"></i>Nombre Completo:
                            </label>
                            <input type="text" id="nombre-edit-{{ $usuario->id }}" name="name" class="form-control rounded-4 w-50" 
                                value="{{ $usuario->name }}" required>
                        </div>
                        
                        {{-- Cédula --}}
                        <div class="mb-3 form-responsive d-flex align-items-center justify-content-between">
                            <label for="cedula-edit-{{ $usuario->id }}" class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-id-card text-info me-2"></i>Cédula:
                            </label>
                            <input type="text" id="cedula-edit-{{ $usuario->id }}" name="cedula" class="form-control rounded-4 w-50" 
                                value="{{ $usuario->cedula }}" required>
                        </div>
                        
                        {{-- Correo Electrónico --}}
                        <div class="mb-3 form-responsive d-flex align-items-center justify-content-between">
                            <label for="email-edit-{{ $usuario->id }}" class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-envelope text-warning me-2"></i>Correo Electrónico:
                            </label>
                            <input type="email" id="email-edit-{{ $usuario->id }}" name="email" class="form-control rounded-4 w-50" 
                                value="{{ $usuario->email }}" required>
                        </div>
                        
                        {{-- Rol --}}
                        <div class="mb-3 form-responsive d-flex align-items-center justify-content-between">
                            <label for="rol-edit-{{ $usuario->id }}" class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-user-tag text-success me-2"></i>Rol:
                            </label>
                            <div class="position-relative w-50">
                                <select id="rol-edit-{{ $usuario->id }}" name="role" class="form-control rounded-4" required>
                                    <option value="">Seleccione un rol</option>
                                    @if(isset($roles) && $roles)
                                        @foreach ($roles as $rol)
                                            <option value="{{ $rol->name }}" {{ $usuario->getRoleNames()->first() == $rol->name ? 'selected' : '' }}>{{ ucfirst($rol->name) }}</option>
                                        @endforeach
                                    @else
                                        <option value="profesor">Profesor</option>
                                        <option value="administrador">Administrador</option>
                                        <option value="soporte">Soporte</option>
                                        <option value="director">Director</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        {{-- Estado/Condición --}}
                        <div class="mb-3 form-responsive d-flex align-items-center justify-content-between">
                            <label for="condicion-edit-{{ $usuario->id }}" class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-toggle-on text-primary me-2"></i>Estado:
                            </label>
                            <div class="position-relative w-50">
                                <select id="condicion-edit-{{ $usuario->id }}" name="condicion" class="form-control rounded-4" required>
                                    <option value="1" {{ $usuario->condicion == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ $usuario->condicion == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        
                        {{-- Contraseña (opcional) --}}
                        <div class="mb-3 form-responsive d-flex align-items-center justify-content-between">
                            <label for="password-edit-{{ $usuario->id }}" class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-lock text-danger me-2"></i>Nueva Contraseña:
                            </label>
                            <input type="password" id="password-edit-{{ $usuario->id }}" name="password" class="form-control rounded-4 w-50" 
                                placeholder="Dejar vacío para mantener actual">
                        </div>
                    </div>
                    <div class="modal-footer px-4 pb-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary btn-modificar w-100 w-md-auto">Editar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Eliminar Usuario --}}
    <div class="modal fade" id="modalEliminarUsuario{{ $usuario->id }}" tabindex="-1" aria-labelledby="modalEliminarUsuarioLabel{{ $usuario->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal">
                <div class="modal-body text-center">
                    <div class="icon-container">
                        <div class="circle-icon">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>
                    </div>
                    <p class="modal-text">¿Desea desactivar el usuario?</p>
                    <div class="btn-group-custom">
                        <form method="POST" action="{{ route('usuario.destroy', $usuario->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-custom">Sí</button>
                            <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Reactivar Usuario --}}
    <div class="modal fade" id="modalReactivarUsuario{{ $usuario->id }}" tabindex="-1" aria-labelledby="modalReactivarUsuarioLabel{{ $usuario->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal">
                <div class="modal-body text-center">
                    <div class="icon-container">
                        <div class="circle-icon" style="background-color: #28a745; color: #fff;">
                            <i class="bi bi-arrow-counterclockwise" style="color: #fff;"></i>
                        </div>
                    </div>
                    <p class="modal-text">¿Desea reactivar el usuario?</p>
                    <div class="btn-group-custom">
                        <form method="POST" action="{{ route('usuario.destroy', $usuario->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-custom" style="background-color: #28a745; color: #fff;">Sí</button>
                            <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{-- Modal Éxito Eliminar --}}
    @if(session('eliminado'))
    <div class="modal fade show" id="modalExitoEliminar" tabindex="-1" aria-modal="true" style="display:block;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-body d-flex flex-column align-items-center gap-3 p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 256 256">
                        <g fill="#efc737" fill-rule="nonzero">
                            <g transform="scale(5.12,5.12)">
                                <path d="M25,2c-12.683,0 -23,10.317 -23,23c0,12.683 10.317,23 23,23c12.683,0 23,-10.317 23,-23c0,-4.56 -1.33972,-8.81067 -3.63672,-12.38867l-1.36914,1.61719c1.895,3.154 3.00586,6.83148 3.00586,10.77148c0,11.579 -9.421,21 -21,21c-11.579,0 -21,-9.421 -21,-21c0,-11.579 9.421,-21 21,-21c5.443,0 10.39391,2.09977 14.12891,5.50977l1.30859,-1.54492c-4.085,-3.705 -9.5025,-5.96484 -15.4375,-5.96484zM43.23633,7.75391l-19.32227,22.80078l-8.13281,-7.58594l-1.36328,1.46289l9.66602,9.01563l20.67969,-24.40039z"/>
                            </g>
                        </g>
                    </svg>
                    <p class="mb-0">Usuario procesado con éxito</p>
                </div>
                <div class="modal-footer d-flex justify-content-center pb-3">
                    <button type="button" class="btn btn-primary" onclick="cerrarModalExito()">Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Búsqueda en tiempo real
        let timeoutId;
        const inputBusqueda = document.getElementById('inputBusqueda');
        const formBusqueda = document.getElementById('busquedaForm');
        const btnLimpiar = document.getElementById('limpiarBusqueda');
        
        if (inputBusqueda && formBusqueda) {
            inputBusqueda.addEventListener('input', function() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(function() {
                    formBusqueda.submit();
                }, 500);
            });
            
            inputBusqueda.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    formBusqueda.submit();
                }
            });
        }
        
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', function() {
                if (inputBusqueda) {
                    inputBusqueda.value = '';
                }
                window.location.href = '{{ route("usuario.index") }}';
            });
        }

        // Función para cerrar el modal de éxito
        window.cerrarModalExito = function() {
            const modal = document.getElementById('modalExitoEliminar');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }
        };

        // Auto-cerrar el modal después de 3 segundos
        @if(session('eliminado'))
        setTimeout(function() {
            cerrarModalExito();
        }, 3000);
        
        const modal = document.getElementById('modalExitoEliminar');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    cerrarModalExito();
                }
            });
        }
        @endif
    });
    </script>

</div>
@endsection

