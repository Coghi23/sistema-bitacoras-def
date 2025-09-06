@extends('Template-administrador')

@section('content')
<style>
    /* Estilos adicionales para los checkboxes */
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .form-check-input:focus {
        border-color: #86b7fe;
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .form-check-label {
        cursor: pointer;
    }
</style>
<style>
/* Responsive adjustments */
@media (max-width: 768px) {
    .main-content {
        margin-left: 0 !important;
        padding: 1rem !important;
    }
    .search-bar-wrapper, .search-bar, .btn-agregar {
        flex-direction: column !important;
        width: 100% !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    .btn-agregar {
        margin-top: 1rem !important;
        width: 100%;
        justify-content: center;
    }
    .table-responsive {
        overflow-x: auto;
    }
    .table th, .table td {
        white-space: nowrap;
    }
    .modal-dialog {
        margin: 1rem auto;
        max-width: 95vw;
    }
    .mb-3.d-flex.align-items-center.justify-content-between {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .mb-3.d-flex.align-items-center.justify-content-between label,
    .mb-3.d-flex.align-items-center.justify-content-between .w-50,
    .mb-3.d-flex.align-items-center.justify-content-between input,
    .mb-3.d-flex.align-items-center.justify-content-between select {
        width: 100% !important;
        margin-bottom: 0.5rem !important;
    }
    .modal-footer {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
<div class="wrapper">
    <div class="main-content p-4 container-fluid" style="margin-left: 90px;">
        <div class="row align-items-end mb-4">
            {{-- Búsqueda + botón agregar --}}
        <div class="search-bar-wrapper mb-4 d-flex flex-wrap align-items-center">
            <div class="search-bar flex-grow-1" style="min-width: 220px;">
                <form id="busquedaForm" method="GET" action="{{ route('usuario.index') }}" class="w-100 position-relative">
                    <span class="search-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control"
                        placeholder="Buscar usuario..." name="busquedaUsuario" 
                        value="{{ request('busquedaUsuario') }}" id="inputBusqueda" autocomplete="off">
                    @if(request('busquedaUsuario'))
                    <button type="button" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" id="limpiarBusqueda" title="Limpiar búsqueda" style="background: transparent;">
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
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
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
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <span>
                    Mostrando {{ $usuarios->count() }} resultado(s) para "<strong>{{ request('busquedaUsuario') }}</strong>"
                    <a href="{{ route('usuario.index', request('inactivos') ? ['inactivos' => 1] : []) }}" class="btn btn-sm btn-outline-primary ms-2">Ver todos</a>
                </span>
            </div>
        @endif
        @can('view_usuarios')
            {{-- Botones para mostrar/ocultar usuarios inactivos --}}
            <div class="d-flex flex-wrap gap-2 mb-3">
                <a href="{{ route('usuario.index') }}" class="btn {{ !request('inactivos') ? 'btn-primary' : 'btn-outline-primary' }}">
                    Mostrar activos
                </a>
                <a href="{{ route('usuario.index', ['inactivos' => 1]) }}" class="btn {{ request('inactivos') ? 'btn-warning' : 'btn-outline-warning' }}">
                    Mostrar inactivos
                </a>
            </div>

            <div id="tabla-usuarios" class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr class="header-row">
                            <th class="col-dia">Nombre</th>
                            <th class="col-docente">Cédula</th>
                            <th class="col-recinto">Correo Electrónico</th>
                            <th class="col-subarea-seccion">Rol</th>
                            <th class="col-entrada">Estado</th>
                            <th class="col-acciones">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr class="record-row">
                            <td class="col-dia">{{ $usuario->name }}</td>
                            <td class="col-docente">{{ $usuario->cedula }}</td>
                            <td class="col-recinto">{{ $usuario->email }}</td>
                            <td class="col-subarea-seccion">
                                @if($usuario->getRoleNames()->isNotEmpty())
                                    {{ ucfirst($usuario->getRoleNames()->first()) }}
                                @else
                                    Sin rol
                                @endif
                            </td>
                            <td class="col-entrada">
                                <span class="badge {{ isset($usuario->condicion) && $usuario->condicion ? 'bg-success' : 'bg-danger' }}">
                                    {{ isset($usuario->condicion) && $usuario->condicion ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="col-acciones">
                            @can('edit_usuarios')
                                <button class="btn p-0 me-2" data-bs-toggle="modal" data-bs-target="#modalEditarUsuario{{ $usuario->id }}" title="Editar usuario">
                                    <i class="bi bi-pencil icon-editar"></i>
                                </button>
                            @endcan
                            @can('delete_usuarios')
                                {{-- Mostrar botón de eliminar o reactivar según el estado del usuario --}}
                                @if($usuario->condicion == 1)
                                    <button class="btn p-0 me-2" data-bs-toggle="modal" data-bs-target="#modalEliminarUsuario{{ $usuario->id }}" title="Desactivar usuario">
                                        <i class="bi bi-trash icon-eliminar"></i>
                                    </button>
                                @else
                                    <button class="btn p-0 me-2" data-bs-toggle="modal" data-bs-target="#modalReactivarUsuario{{ $usuario->id }}" title="Activar usuario">
                                        <i class="bi bi-arrow-counterclockwise icon-eliminar"></i>
                                    </button>
                                @endif
                            @endcan
                            </td>
                        </tr>
                        @empty
                        <tr class="record-row">
                            <td class="text-center" colspan="6">No hay usuarios registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endcan
    </div>

    {{-- Modal Crear Usuario --}}
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 shadow-lg">
                <form method="POST" action="{{ route('usuario.store') }}">
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
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-user text-primary me-2"></i>Nombre Completo:
                            </label>
                            <input type="text" name="name" class="form-control rounded-4 w-50" placeholder="Nombre..." required>
                        </div>
                        
                        {{-- Cédula --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-id-card text-info me-2"></i>Cédula:
                            </label>
                            <input type="text" name="cedula" class="form-control rounded-4 w-50" placeholder="Cédula..." required>
                        </div>
                        
                        {{-- Correo Electrónico --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-envelope text-warning me-2"></i>Correo Electrónico:
                            </label>
                            <input type="email" name="email" class="form-control rounded-4 w-50" placeholder="Correo Electrónico..." required>
                        </div>
                        
                        {{-- Información sobre configuración de contraseña --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <div class="w-100 text-center">
                                <div class="alert alert-info d-flex align-items-center" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span>Se enviará un correo automáticamente para que el usuario configure su contraseña.</span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Rol --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-user-tag text-success me-2"></i>Rol:
                            </label>
                            <div class="position-relative w-50">
                                <select name="role" id="selectRolCrear" class="form-control rounded-4" required>
                                    <option value="">Seleccione un rol</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->name }}">{{ ucfirst($rol->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Instituciones (para todos los roles excepto admin) --}}
                        <div class="mb-3" id="institucionesSectionCrear" style="display: none;">
                            <div class="d-flex align-items-start justify-content-between">
                                <label class="fw-bold me-3 w-50 text-start">
                                    <i class="fas fa-building text-primary me-2"></i>Instituciones:
                                </label>
                                <div class="position-relative w-50">
                                    <div class="border rounded-4 p-3" style="background-color: #f8f9fa; min-height: 100px; max-height: 150px; overflow-y: auto;">
                                        @foreach($instituciones as $institucion)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input instituciones-checkbox" type="checkbox" 
                                                       name="instituciones[]" value="{{ $institucion->id }}" 
                                                       id="institucion{{ $institucion->id }}">
                                                <label class="form-check-label small" for="institucion{{ $institucion->id }}">
                                                    {{ $institucion->nombre }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="form-text text-muted">Seleccione las instituciones correspondientes</small>
                                </div>
                            </div>
                        </div>

                        {{-- Especialidades (SOLO para profesor) --}}
                        <div class="mb-3" id="especialidadesSectionCrear" style="display: none;">
                            <div class="d-flex align-items-start justify-content-between">
                                <label class="fw-bold me-3 w-50 text-start">
                                    <i class="fas fa-graduation-cap text-success me-2"></i>Especialidades:
                                </label>
                                <div class="position-relative w-50">
                                    <div class="border rounded-4 p-3" style="background-color: #f8f9fa; min-height: 100px; max-height: 150px; overflow-y: auto;">
                                        <div id="especialidadesContainer">
                                            <div class="text-muted text-center py-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Primero seleccione instituciones
                                            </div>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Se mostrarán las especialidades de las instituciones seleccionadas</small>
                                </div>
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

    {{-- Modales funcionales de editar y eliminar usuarios --}}
    @foreach($usuarios as $usuario)
    {{-- Modal Editar Usuario --}}
    <div class="modal fade" id="modalEditarUsuario{{ $usuario->id }}" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel{{ $usuario->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 shadow-lg">
                <form method="POST" action="{{ route('usuario.update', $usuario->id) }}">
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
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-user text-primary me-2"></i>Nombre Completo:
                            </label>
                            <input type="text" name="name" class="form-control rounded-4 w-50" 
                                value="{{ $usuario->name }}" required>
                        </div>
                        
                        {{-- Cédula --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-id-card text-info me-2"></i>Cédula:
                            </label>
                            <input type="text" name="cedula" class="form-control rounded-4 w-50" 
                                value="{{ $usuario->cedula }}" required>
                        </div>
                        
                        {{-- Correo Electrónico --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-envelope text-warning me-2"></i>Correo Electrónico:
                            </label>
                            <input type="email" name="email" class="form-control rounded-4 w-50" 
                                value="{{ $usuario->email }}" required>
                        </div>
                        
                        {{-- Rol --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-user-tag text-success me-2"></i>Rol:
                            </label>
                            <div class="position-relative w-50">
                                <select name="role" id="selectRolEditar{{ $usuario->id }}" class="form-control rounded-4" required>
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

                        {{-- Instituciones (para todos los roles excepto admin) --}}
                        @php
                            $currentRole = $usuario->getRoleNames()->first();
                            $showInstituciones = $currentRole && $currentRole !== 'admin';
                            $showEspecialidades = $currentRole === 'profesor';
                        @endphp
                        <div class="mb-3" id="institucionesSectionEditar{{ $usuario->id }}" style="display: {{ $showInstituciones ? 'block' : 'none' }};">
                            <div class="d-flex align-items-start justify-content-between">
                                <label class="fw-bold me-3 w-50 text-start">
                                    <i class="fas fa-building text-primary me-2"></i>Instituciones:
                                </label>
                                <div class="position-relative w-50">
                                    <div class="border rounded-4 p-3" style="background-color: #f8f9fa; min-height: 100px; max-height: 150px; overflow-y: auto;">
                                        @foreach($instituciones as $institucion)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input instituciones-checkbox-{{ $usuario->id }}" type="checkbox" 
                                                       name="instituciones[]" value="{{ $institucion->id }}" 
                                                       id="institucionEditar{{ $usuario->id }}_{{ $institucion->id }}"
                                                       {{ $usuario->instituciones->contains($institucion->id) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="institucionEditar{{ $usuario->id }}_{{ $institucion->id }}">
                                                    {{ $institucion->nombre }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="form-text text-muted">Seleccione las instituciones correspondientes</small>
                                </div>
                            </div>
                        </div>

                        {{-- Especialidades (SOLO para profesor) --}}
                        <div class="mb-3" id="especialidadesSectionEditar{{ $usuario->id }}" style="display: {{ $showEspecialidades ? 'block' : 'none' }};">
                            <div class="d-flex align-items-start justify-content-between">
                                <label class="fw-bold me-3 w-50 text-start">
                                    <i class="fas fa-graduation-cap text-success me-2"></i>Especialidades:
                                </label>
                                <div class="position-relative w-50">
                                    <div class="border rounded-4 p-3" style="background-color: #f8f9fa; min-height: 100px; max-height: 150px; overflow-y: auto;">
                                        <div id="especialidadesContainerEditar{{ $usuario->id }}">
                                            @if($showEspecialidades && $usuario->instituciones->count() > 0)
                                                @foreach($especialidades as $especialidad)
                                                    @if($usuario->instituciones->contains($especialidad->id_institucion))
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="especialidades[]" value="{{ $especialidad->id }}" 
                                                                   id="especialidadEditar{{ $usuario->id }}_{{ $especialidad->id }}"
                                                                   {{ $usuario->especialidades->contains($especialidad->id) ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="especialidadEditar{{ $usuario->id }}_{{ $especialidad->id }}">
                                                                {{ $especialidad->nombre }}
                                                            </label>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <div class="text-muted text-center py-3">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Primero seleccione instituciones
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Se muestran especialidades de las instituciones seleccionadas</small>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Estado/Condición --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-toggle-on text-primary me-2"></i>Estado:
                            </label>
                            <div class="position-relative w-50">
                                <select name="condicion" class="form-control rounded-4" required>
                                    <option value="1" {{ $usuario->condicion == 1 ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ $usuario->condicion == 0 ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>
                        </div>
                        
                        {{-- Contraseña (opcional) --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-lock text-danger me-2"></i>Nueva Contraseña:
                            </label>
                            <input type="password" name="password" class="form-control rounded-4 w-50" 
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

    {{-- Modal Reactivar Usuario --}}
    <!-- Modal Eliminar Usuario -->
    <div class="modal fade" id="modalEliminarUsuario{{ $usuario->id }}" tabindex="-1" aria-labelledby="modalEliminarUsuarioLabel{{ $usuario->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content custom-modal">
                <div class="modal-body text-center">
                    <div class="icon-container">
                        <div class="circle-icon">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>
                    </div>
                    <p class="modal-text">¿Desea eliminar el usuario?</p>
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

    <!-- Modal Reactivar Usuario -->
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
                    <p class="mb-0">Usuario eliminado con éxito</p>
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
        // Funcionalidad de búsqueda en tiempo real
        let timeoutId;
        const inputBusqueda = document.getElementById('inputBusqueda');
        const formBusqueda = document.getElementById('busquedaForm');
        const btnLimpiar = document.getElementById('limpiarBusqueda');
        
        if (inputBusqueda) {
            inputBusqueda.addEventListener('input', function() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(function() {
                    formBusqueda.submit();
                }, 500); // Espera 500ms después de que el usuario deje de escribir
            });
            
            // También permitir búsqueda al presionar Enter
            inputBusqueda.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    formBusqueda.submit();
                }
            });
        }
    
        // Funcionalidad del botón limpiar
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', function() {
                inputBusqueda.value = '';
                window.location.href = '{{ asset("usuario.index") }}';
            });
        }

        // Función para cerrar el modal de éxito
        function cerrarModalExito() {
            const modal = document.getElementById('modalExitoEliminar');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                // Remover el backdrop si existe
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                // Restaurar el scroll del body
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }
        }

        // Auto-cerrar el modal después de 3 segundos
        @if(session('eliminado'))
        setTimeout(function() {
            cerrarModalExito();
        }, 3000);
        @endif

        // Cerrar modal al hacer clic fuera de él
        @if(session('eliminado'))
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalExitoEliminar');
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        cerrarModalExito();
                    }
                });
            }
        });
        @endif

        /////////////////// JavaScript para mostrar/ocultar campos de profesor /////////////////

        document.addEventListener('DOMContentLoaded', function() {
            // Para el formulario de creación
            const selectRolCrear = document.getElementById('selectRolCrear');
            const institucionesSectionCrear = document.getElementById('institucionesSectionCrear');
            const especialidadesSectionCrear = document.getElementById('especialidadesSectionCrear');
            const institucionesCheckboxes = document.querySelectorAll('.instituciones-checkbox');
            const especialidadesContainer = document.getElementById('especialidadesContainer');

            function mostrarOcultarCamposCrear() {
                if (selectRolCrear && selectRolCrear.value) {
                    if (selectRolCrear.value === 'admin') {
                        // Admin no necesita instituciones ni especialidades
                        institucionesSectionCrear.style.display = 'none';
                        especialidadesSectionCrear.style.display = 'none';
                    } else if (selectRolCrear.value === 'profesor') {
                        // SOLO profesor necesita instituciones y especialidades
                        institucionesSectionCrear.style.display = 'block';
                        especialidadesSectionCrear.style.display = 'block';
                    } else {
                        // Otros roles (director, soporte) SOLO necesitan instituciones
                        institucionesSectionCrear.style.display = 'block';
                        especialidadesSectionCrear.style.display = 'none';
                    }
                } else {
                    // No hay rol seleccionado, ocultar todo
                    institucionesSectionCrear.style.display = 'none';
                    especialidadesSectionCrear.style.display = 'none';
                }
                
                // Limpiar selecciones cuando se ocultan los campos
                if (institucionesSectionCrear.style.display === 'none') {
                    institucionesCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }
                if (especialidadesSectionCrear.style.display === 'none') {
                    // Limpiar especialidades cuando se oculta la sección
                    especialidadesContainer.innerHTML = `
                        <div class="text-muted text-center py-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Primero seleccione instituciones
                        </div>
                    `;
                }
            }

            // Función para cargar especialidades basadas en instituciones seleccionadas
            function cargarEspecialidades() {
                const institucionesSeleccionadas = Array.from(institucionesCheckboxes)
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);
                
                if (institucionesSeleccionadas.length === 0) {
                    especialidadesContainer.innerHTML = `
                        <div class="text-muted text-center py-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Primero seleccione instituciones
                        </div>
                    `;
                    return;
                }

                // Mostrar loading
                especialidadesContainer.innerHTML = `
                    <div class="text-center py-3">
                        <i class="fas fa-spinner fa-spin me-2"></i>
                        Cargando especialidades...
                    </div>
                `;

                // Hacer petición AJAX
                fetch('{{ route("usuario.especialidades-por-instituciones") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        instituciones: institucionesSeleccionadas
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        especialidadesContainer.innerHTML = `
                            <div class="text-muted text-center py-3">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                No hay especialidades disponibles
                            </div>
                        `;
                    } else {
                        let html = '';
                        data.forEach(especialidad => {
                            html += `
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" 
                                           name="especialidades[]" value="${especialidad.id}" 
                                           id="especialidad${especialidad.id}">
                                    <label class="form-check-label small" for="especialidad${especialidad.id}">
                                        ${especialidad.nombre}
                                    </label>
                                </div>
                            `;
                        });
                        especialidadesContainer.innerHTML = html;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    especialidadesContainer.innerHTML = `
                        <div class="text-danger text-center py-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error al cargar especialidades
                        </div>
                    `;
                });
            }

            if (selectRolCrear) {
                selectRolCrear.addEventListener('change', mostrarOcultarCamposCrear);
                mostrarOcultarCamposCrear();
            }

            // Agregar event listeners a los checkboxes de instituciones
            institucionesCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', cargarEspecialidades);
            });

            // Para los formularios de edición (dinámicos)
            @foreach($usuarios as $usuario)
            const selectRolEditar{{ $usuario->id }} = document.getElementById('selectRolEditar{{ $usuario->id }}');
            const institucionesSectionEditar{{ $usuario->id }} = document.getElementById('institucionesSectionEditar{{ $usuario->id }}');
            const especialidadesSectionEditar{{ $usuario->id }} = document.getElementById('especialidadesSectionEditar{{ $usuario->id }}');
            const institucionesCheckboxesEditar{{ $usuario->id }} = document.querySelectorAll('.instituciones-checkbox-{{ $usuario->id }}');
            const especialidadesContainerEditar{{ $usuario->id }} = document.getElementById('especialidadesContainerEditar{{ $usuario->id }}');

            function mostrarOcultarCamposEditar{{ $usuario->id }}() {
                if (selectRolEditar{{ $usuario->id }} && selectRolEditar{{ $usuario->id }}.value) {
                    if (selectRolEditar{{ $usuario->id }}.value === 'admin') {
                        // Admin no necesita instituciones ni especialidades
                        institucionesSectionEditar{{ $usuario->id }}.style.display = 'none';
                        especialidadesSectionEditar{{ $usuario->id }}.style.display = 'none';
                    } else if (selectRolEditar{{ $usuario->id }}.value === 'profesor') {
                        // SOLO profesor necesita instituciones y especialidades
                        institucionesSectionEditar{{ $usuario->id }}.style.display = 'block';
                        especialidadesSectionEditar{{ $usuario->id }}.style.display = 'block';
                    } else {
                        // Otros roles (director, soporte) SOLO necesitan instituciones
                        institucionesSectionEditar{{ $usuario->id }}.style.display = 'block';
                        especialidadesSectionEditar{{ $usuario->id }}.style.display = 'none';
                    }
                } else {
                    // No hay rol seleccionado, ocultar todo
                    institucionesSectionEditar{{ $usuario->id }}.style.display = 'none';
                    especialidadesSectionEditar{{ $usuario->id }}.style.display = 'none';
                }
            }

            function cargarEspecialidadesEditar{{ $usuario->id }}() {
                if (!especialidadesContainerEditar{{ $usuario->id }}) return;
                
                const institucionesSeleccionadas = Array.from(institucionesCheckboxesEditar{{ $usuario->id }})
                    .filter(checkbox => checkbox.checked)
                    .map(checkbox => checkbox.value);
                
                const especialidadesActuales = Array.from(especialidadesContainerEditar{{ $usuario->id }}.querySelectorAll('input[type="checkbox"]:checked'))
                    .map(checkbox => checkbox.value);
                
                if (institucionesSeleccionadas.length === 0) {
                    especialidadesContainerEditar{{ $usuario->id }}.innerHTML = `
                        <div class="text-muted text-center py-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Primero seleccione instituciones
                        </div>
                    `;
                    return;
                }

                // Mostrar loading
                especialidadesContainerEditar{{ $usuario->id }}.innerHTML = `
                    <div class="text-center py-3">
                        <i class="fas fa-spinner fa-spin me-2"></i>
                        Cargando especialidades...
                    </div>
                `;

                // Hacer petición AJAX
                fetch('{{ route("usuario.especialidades-por-instituciones") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        instituciones: institucionesSeleccionadas
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        especialidadesContainerEditar{{ $usuario->id }}.innerHTML = `
                            <div class="text-muted text-center py-3">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                No hay especialidades disponibles
                            </div>
                        `;
                    } else {
                        let html = '';
                        data.forEach(especialidad => {
                            const isChecked = especialidadesActuales.includes(especialidad.id.toString()) ? 'checked' : '';
                            html += `
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" 
                                           name="especialidades[]" value="${especialidad.id}" 
                                           id="especialidadEditar{{ $usuario->id }}_${especialidad.id}" ${isChecked}>
                                    <label class="form-check-label small" for="especialidadEditar{{ $usuario->id }}_${especialidad.id}">
                                        ${especialidad.nombre}
                                    </label>
                                </div>
                            `;
                        });
                        especialidadesContainerEditar{{ $usuario->id }}.innerHTML = html;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    especialidadesContainerEditar{{ $usuario->id }}.innerHTML = `
                        <div class="text-danger text-center py-3">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error al cargar especialidades
                        </div>
                    `;
                });
            }

            if (selectRolEditar{{ $usuario->id }}) {
                selectRolEditar{{ $usuario->id }}.addEventListener('change', mostrarOcultarCamposEditar{{ $usuario->id }});
                mostrarOcultarCamposEditar{{ $usuario->id }}();
            }

            // Agregar event listeners a los checkboxes de instituciones
            if (institucionesCheckboxesEditar{{ $usuario->id }}) {
                institucionesCheckboxesEditar{{ $usuario->id }}.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        // Solo cargar especialidades si es profesor
                        if (selectRolEditar{{ $usuario->id }} && selectRolEditar{{ $usuario->id }}.value === 'profesor') {
                            cargarEspecialidadesEditar{{ $usuario->id }}();
                        }
                    });
                });
            }
            @endforeach
        });

        // Función para reenviar configuración de contraseña
        function reenviarConfiguracionPassword(usuarioId) {
            if (confirm('¿Está seguro de que desea reenviar el correo de configuración de contraseña?')) {
                const btn = event.target.closest('button');
                const originalContent = btn.innerHTML;
                
                // Mostrar loading
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;
                
                fetch(`/usuario/${usuarioId}/resend-password-setup`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar mensaje de éxito
                        const alert = document.createElement('div');
                        alert.className = 'alert alert-success alert-dismissible fade show';
                        alert.innerHTML = `
                            <i class="fas fa-check-circle me-2"></i>
                            ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        document.querySelector('.container-fluid').insertBefore(alert, document.querySelector('#tabla-usuarios'));
                        
                        // Auto-hide alert after 5 seconds
                        setTimeout(() => {
                            alert.remove();
                        }, 5000);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al reenviar el correo. Por favor, intente nuevamente.');
                })
                .finally(() => {
                    // Restaurar botón
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                });
            }
        }

    </script>

</div>
@endsection

