@extends('Template-administrador')

@section('content')
<div class="wrapper">
    <div class="main-content p-4" style="margin-left: 90px;">
        <div class="row align-items-end mb-4">
            <div class="search-bar-wrapper mb-4 d-flex align-items-center">
                <div class="search-bar flex-grow-1">
                    <form id="busquedaForm" method="GET" action="{{ route('role.index') }}" class="w-100 position-relative">
                        <span class="search-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Buscar rol..." name="busquedaRol" value="{{ request('busquedaRol') }}" id="inputBusqueda" autocomplete="off">
                        @if(request('busquedaRol'))
                        <button type="button" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" id="limpiarBusqueda" title="Limpiar búsqueda" style="background: transparent;">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        @endif
                        @if(request('inactivos'))
                            <input type="hidden" name="inactivos" value="1">
                        @endif
                    </form>
                </div>
                @can('create_roles')
                    <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
                        data-bs-toggle="modal" data-bs-target="#createRoleModal"
                        title="Agregar Rol" style="background-color: #134496; font-size: 1.2rem; @if(Auth::user() && Auth::user()->hasRole('director')) display: none; @endif">
                        Agregar <i class="bi bi-plus-circle ms-2"></i>
                    </button>
                @endcan
            </div>
        </div>
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
        @if(request('busquedaRol'))
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <span>
                    Mostrando {{ $roles->count() }} resultado(s) para "<strong>{{ request('busquedaRol') }}</strong>"
                    <a href="{{ route('role.index', request('inactivos') ? ['inactivos' => 1] : []) }}" class="btn btn-sm btn-outline-primary ms-2">Ver todos</a>
                </span>
            </div>
        @endif 
            <button id="btnMostrarInactivos" class="btn btn-warning mb-3 me-2" type="button">
                Mostrar inactivos
            </button>
            <button id="btnMostrarActivos" class="btn btn-primary mb-3 me-2" type="button">
                Mostrar activos
            </button>

        <div id="tabla-roles">
            <table class="table table-striped">
                <thead>
                    <tr class="header-row">
                        <th>Nombre del Rol</th>
                        <th>Permisos</th>
                        <th>Fecha de Creación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr class="record-row {{ $role->condicion == 0 ? 'fila-inactiva' : 'fila-activa' }}">
                        <td>{{ $role->name }}</td>
                        <td>
                            @if($role->permissions->count() > 0)
                                <span class="badge bg-info">{{ $role->permissions->count() }} permisos</span>
                            @else
                                <span class="badge bg-secondary">Sin Permisos</span>
                            @endif
                        </td>
                        <td>{{ $role->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge {{ $role->condicion == 1 ? 'bg-success' : 'bg-danger' }}">
                                {{ $role->condicion == 1 ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            @can('edit_roles')
                            <button 
                                class="btn p-0 me-2" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editRoleModal"
                                title="Editar rol"
                                onclick="loadEditModal({{ $role->id }}, '{{ addslashes($role->name) }}', @json($role->permissions->pluck('id')))">
                                <i class="bi bi-pencil icon-editar"></i>
                            </button>
                            @endcan
                            @if($role->condicion == 1)
                                @can('delete_roles')
                                <button class="btn p-0 me-2" data-bs-toggle="modal" data-bs-target="#eliminarRoleModal{{ $role->id }}" title="Desactivar rol">
                                    <i class="bi bi-trash icon-eliminar"></i>
                                </button>
                                @endcan
                            @else
                                @can('delete_roles')
                                <button class="btn p-0 me-2" data-bs-toggle="modal" data-bs-target="#reactivarRoleModal{{ $role->id }}" title="Activar rol">
                                    <i class="bi bi-recycle icon-eliminar"></i>
                                </button>
                                @endcan
                            @endif

                        </td>
                    </tr>
                    @empty
                    <tr class="record-row">
                        <td class="text-center" colspan="5">No hay roles registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
    </div>
</div>

@if(Auth::user() && !Auth::user()->hasRole('director'))
<!-- Modal Crear Rol -->
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('role.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre del Rol</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Permisos</label>
                        <div class="row">
                            @foreach($permisos as $permiso)
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="permissions[]" value="{{ $permiso->id }}" 
                                               id="permission_{{ $permiso->id }}">
                                        <label class="form-check-label" for="permission_{{ $permiso->id }}">
                                            {{ $permiso->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('permissions')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Rol</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Rol -->
<div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editRoleModalLabel">Editar Rol</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editRoleForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nombre del Rol</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Permisos</label>
                        <div class="row" id="edit_permissions">
                            @foreach($permisos as $permiso)
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="permissions[]" value="{{ $permiso->id }}" 
                                               id="edit_permission_{{ $permiso->id }}">
                                        <label class="form-check-label" for="edit_permission_{{ $permiso->id }}">
                                            {{ $permiso->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Actualizar Rol</button>
                </div>
            </form>
        </div>
    </div>
</div>


@foreach($roles as $role)
<!-- Modal Desactivar Rol -->
<div class="modal fade" id="eliminarRoleModal{{ $role->id }}" tabindex="-1" aria-labelledby="modalEliminarRoleLabel{{ $role->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <form method="POST" action="{{ route('role.destroy', $role->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-body d-flex flex-column align-items-center gap-3 p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#efc737" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
                    </svg>
                    <p>¿Está seguro de desactivar este rol?</p>
                    <p class="text-muted small">{{ $role->name }}</p>
                </div>
                <div class="modal-footer d-flex justify-content-center gap-2 pb-3">
                    <button type="submit" class="btn btn-primary">Sí</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Reactivar Rol -->
<div class="modal fade" id="reactivarRoleModal{{ $role->id }}" tabindex="-1" aria-labelledby="modalReactivarRoleLabel{{ $role->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <form method="POST" action="{{ route('role.destroy', $role->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-body d-flex flex-column align-items-center gap-3 p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#efc737" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
                    </svg>
                    <p>¿Está seguro de reactivar este rol?</p>
                    <p class="text-muted small">{{ $role->name }}</p>
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

<script>
function loadEditModal(roleId, roleName, permissions) {
    document.getElementById('edit_name').value = roleName;
    document.getElementById('editRoleForm').action = `/role/${roleId}`;
    // Limpiar todos los checkboxes
    const checkboxes = document.querySelectorAll('#edit_permissions input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    // Marcar los permisos del rol
    permissions.forEach(permissionId => {
        const checkbox = document.getElementById(`edit_permission_${permissionId}`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });
}

// Mostrar solo activos o solo inactivos
document.addEventListener('DOMContentLoaded', function() {
    const btnMostrarActivos = document.getElementById('btnMostrarActivos');
    const btnMostrarInactivos = document.getElementById('btnMostrarInactivos');
    function mostrarSoloActivos() {
        document.querySelectorAll('.fila-activa').forEach(row => row.classList.remove('d-none'));
        document.querySelectorAll('.fila-inactiva').forEach(row => row.classList.add('d-none'));
    }
    function mostrarSoloInactivos() {
        document.querySelectorAll('.fila-activa').forEach(row => row.classList.add('d-none'));
        document.querySelectorAll('.fila-inactiva').forEach(row => row.classList.remove('d-none'));
    }
    btnMostrarActivos.addEventListener('click', mostrarSoloActivos);
    btnMostrarInactivos.addEventListener('click', mostrarSoloInactivos);
    // Mostrar solo activos por defecto
    mostrarSoloActivos();
});
</script>
@endif
@endsection