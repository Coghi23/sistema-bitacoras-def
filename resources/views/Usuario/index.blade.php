@extends('Template-administrador')

@section('content')
<div class="wrapper">
    <div class="main-content p-4">
        <div class="row align-items-end mb-4">
            {{-- Barra de búsqueda --}}
            <div class="col-auto flex-grow-1" style="min-width: 0;">
                <form id="busquedaForm" method="GET" action="{{ route('usuario.index') }}">
                    <div class="position-relative" style="max-width: 700px; width: 100%;">
                        <div class="input-group search-box">
                            <input type="text" class="form-control border-0" placeholder="Buscar usuario" name="busquedaUsuario" value="{{ request('busquedaUsuario') }}" autocomplete="off" id="inputBusqueda" class="bi bi-search" />

                            @if(request('busquedaUsuario'))
                            <button type="button" class="btn btn-outline-secondary border-0" id="limpiarBusqueda" title="Limpiar búsqueda">
                                <i class="bi bi-x-circle"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            {{-- Botones --}}
            <div class="col-auto d-flex gap-2 align-items-end">
                @if(Auth::user() && !Auth::user()->hasRole('director'))
                <button class="btn btn-agregar d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                    Agregar
                    <span class="icon-circle">
                        <i class="fas fa-plus fa-sm text-primary"></i>
                    </span>
                </button>
                @endif
                {{-- Filtros temporalmente desactivados
                <div class="filtros-wrapper position-relative">
                    <button class="btn btn-filtros d-flex align-items-center gap-2" type="submit" form="filtrosForm">
                        <i class="fas fa-filter"></i>
                        Filtros
                    </button>
                    <form id="filtrosForm" method="GET" action="{{ route('usuario.index') }}">
                        <div class="dropdown-panel shadow-sm">
                        </div>
                    </form>
                </div>
                --}}
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
                    <a href="{{ route('usuario.index') }}" class="btn btn-sm btn-outline-primary ms-2">Ver todos</a>
                </span>
            </div>
        @endif

        {{-- Botones para mostrar/ocultar usuarios inactivos --}}
        <div class="button-group mb-3">
            <a href="{{ route('usuario.index', ['inactivos' => 1]) }}" class="btn btn-agregar btn-inactivos btn-agregar-sm">
                Mostrar inactivos
            </a>
            <a href="{{ route('usuario.index') }}" class="btn btn-agregar btn-activos btn-agregar-sm">
                Mostrar activos
            </a>
        </div>

        <div id="tabla-usuarios">
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Cédula</th>
                            <th class="text-center">Correo Electrónico</th>
                            <th class="text-center">Rol</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                        <tr>
                            <td class="text-center">{{ $usuario->name }}</td>
                            <td class="text-center">{{ $usuario->cedula }}</td>
                            <td class="text-center">{{ $usuario->email }}</td>
                            <td class="text-center">
                                @if($usuario->getRoleNames()->isNotEmpty())
                                    {{ ucfirst($usuario->getRoleNames()->first()) }}
                                @else
                                    Sin rol
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ isset($usuario->condicion) && $usuario->condicion ? 'bg-success' : 'bg-danger' }}">
                                    {{ isset($usuario->condicion) && $usuario->condicion ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if(Auth::user() && !Auth::user()->hasRole('director'))
                                <button class="btn btn-link text-info p-0 me-2" data-bs-toggle="modal" data-bs-target="#modalEditarUsuario{{ $usuario->id }}">
                                    <i class="bi bi-pencil" style="font-size: 1.8rem;"></i>
                                </button>
                                <button class="btn btn-link text-info p-0" data-bs-toggle="modal" data-bs-target="#modalEliminarUsuario{{ $usuario->id }}">
                                    <i class="bi bi-trash" style="font-size: 1.8rem;"></i>
                                </button>
                                @else
                                <span class="text-muted">Solo vista</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center" colspan="6">No hay usuarios registrados.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Crear Usuario --}}
    <div class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content rounded-4 shadow-lg">
                <form method="POST" action="{{ route('usuario.store') }}">
                    @csrf
                    <div class="modal-header modal-header-custom text-white px-4 py-3 position-relative justify-content-center">
                        <button type="button" class="btn p-0 d-flex align-items-center position-absolute start-0 ms-3" data-bs-dismiss="modal" aria-label="Cerrar">
                            <div class="circle-yellow d-flex justify-content-center align-items-center">
                                <i class="fas fa-arrow-left text-blue-forced"></i>
                            </div>
                            <div class="linea-vertical-amarilla ms-2"></div>
                        </button>
                        <h5 class="modal-title m-0" id="modalUsuarioLabel">Registro de usuarios</h5>
                    </div>
                    <div class="linea-divisoria-horizontal"></div>
                    <div class="modal-body px-4 pt-3">
                        {{-- Nombre Completo --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                Nombre Completo:
                            </label>
                            <input type="text" name="name" class="form-control rounded-4 w-50" placeholder="Mauricio Vargas" required>
                        </div>
                        
                        {{-- Cédula --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                Cédula:
                            </label>
                            <input type="text" name="cedula" class="form-control rounded-4 w-50" placeholder="30458065" required>
                        </div>
                        
                        {{-- Correo Electrónico --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                Correo Electrónico:
                            </label>
                            <input type="email" name="email" class="form-control rounded-4 w-50" placeholder="MauriVargas17@gmail.com" required>
                        </div>
                        
                        {{-- Contraseña --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                Contraseña:
                            </label>
                            <input type="password" name="password" class="form-control rounded-4 w-50" placeholder="••••••••" required>
                        </div>
                        
                        {{-- Confirmar Contraseña --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                Confirmar:
                            </label>
                            <input type="password" name="password_confirmation" class="form-control rounded-4 w-50" placeholder="••••••••" required>
                        </div>
                        
                        {{-- Rol --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                Rol:
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
                    <div class="modal-footer px-4 pb-3 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary btn-crear">Registrar</button>
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
                    <div class="modal-header modal-header-custom text-white px-4 py-3 position-relative justify-content-center">
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
                                Nombre Completo:
                            </label>
                            <input type="text" name="name" class="form-control rounded-4 w-50" 
                                value="{{ $usuario->name }}" required>
                        </div>
                        
                        {{-- Cédula --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                Cédula:
                            </label>
                            <input type="text" name="cedula" class="form-control rounded-4 w-50" 
                                value="{{ $usuario->cedula }}" required>
                        </div>
                        
                        {{-- Correo Electrónico --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                Correo Electrónico:
                            </label>
                            <input type="email" name="email" class="form-control rounded-4 w-50" 
                                value="{{ $usuario->email }}" required>
                        </div>
                        
                        {{-- Rol --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">
                                Rol:
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
                                Estado:
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
                                Nueva Contraseña:
                            </label>
                            <input type="password" name="password" class="form-control rounded-4 w-50" 
                                placeholder="Dejar vacío para mantener actual">
                        </div>
                    </div>
                    <div class="modal-footer px-4 pb-3 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary btn-modificar">Editar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Eliminar Usuario --}}
    <div class="modal fade" id="modalEliminarUsuario{{ $usuario->id }}" tabindex="-1" aria-labelledby="modalEliminarUsuarioLabel{{ $usuario->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <form method="POST" action="{{ route('usuario.destroy', $usuario->id) }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body d-flex flex-column align-items-center gap-3 p-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#efc737" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
                        </svg>
                        <p>¿Está usted seguro de eliminar este usuario?</p>
                        <p class="text-muted small">{{ $usuario->name }} - {{ $usuario->email }}</p>
                    </div>
                    <div class="modal-footer d-flex justify-content-center gap-2 pb-3">
                        <button type="submit" class="btn btn-primary">Sí</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    </div>
                </form>
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
                window.location.href = '{{ route("usuario.index") }}';
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

