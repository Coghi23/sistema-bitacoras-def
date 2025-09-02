@extends('Template-administrador')

@section('content')
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
                <a href="{{ route('usuario.index', ['inactivos' => 1]) }}" class="btn btn-warning">
                    Mostrar inactivos
                </a>
                <a href="{{ route('usuario.index') }}" class="btn btn-primary">
                    Mostrar activos
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
        <div class="modal-dialog modal-dialog-centered modal-md">
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
                                <select name="role" class="form-control rounded-4" required>
                                    <option value="">Seleccione un rol</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->name }}">{{ ucfirst($rol->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        {{-- Institución (temporalmente desactivada)
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-building text-primary me-2"></i>Institución:
                            </label>
                            <div class="position-relative w-50">
                                <select data-size="4" title="Seleccione una institución" data-live-search="true" name="id_institucion" id="id_institucion" class="form-control selectpicker show-tick" required>
                                    <option value="">Seleccione una institución</option>
                                    @foreach ($instituciones as $institucion)
                                        <option value="{{$institucion->id}}" {{ old('id_institucion') == $institucion->id ? 'selected' : '' }}>{{$institucion->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        --}}
                        
                        {{-- Especialidad (temporalmente desactivada)
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                <i class="fas fa-graduation-cap text-success me-2"></i>Especialidad:
                            </label>
                            <div class="position-relative w-50">
                                <select data-size="4" title="Seleccione una especialidad" data-live-search="true" name="id_especialidad" id="id_especialidad" class="form-control selectpicker show-tick" required>
                                        <option value="">Seleccione una institución</option>
                                        @foreach ($especialidades as $especialidad)
                                            <option value="{{$especialidad->id}}" {{ old('id_especialidad') == $especialidad->id ? 'selected' : '' }}>{{$especialidad->nombre}}</option>
                                        @endforeach
                                </select>
                            </div>
                        </div>
                        --}}
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
        <div class="modal-dialog modal-dialog-centered modal-md">
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
                                <select name="role" class="form-control rounded-4" required>
                                    <option value="">Seleccione un rol</option>
                                    @if(isset($roles) && $roles)
                                        @foreach ($roles as $rol)
                                            <option value="{{ $rol->name }}">{{ ucfirst($rol->name) }}</option>
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
    </script>

</div>
@endsection

