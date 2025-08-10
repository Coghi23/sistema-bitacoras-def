<?php
@extends('Template-administrador')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Gestión de Roles</h3>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createRoleModal">
                        <i class="bi bi-plus-circle"></i> Crear Rol
                    </button>
                </div>
                <div class="card-body">
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

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre del Rol</th>
                                    <th>Permisos</th>
                                    <th>Fecha de Creación</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $role)
                                    <tr>
                                        <td>{{ $role->id }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td>
                                            @if($role->permissions->count() > 0)
                                                <span class="badge bg-info">{{ $role->permissions->count() }} permisos</span>
                                            @else
                                                <span class="badge bg-secondary">Sin permisos</span>
                                            @endif
                                        </td>
                                        <td>{{ $role->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editRoleModal"
                                                        onclick="loadEditModal({{ $role->id }}, '{{ $role->name }}', [{{ $role->permissions->pluck('id')->implode(',') }}])">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteRoleModal"
                                                        onclick="setDeleteRole({{ $role->id }}, '{{ $role->name }}')">
                                                    <i class="bi bi-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No hay roles registrados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Rol -->
<div class="modal fade" id="createRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST">
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
<div class="modal fade" id="editRoleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Rol</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Eliminar Rol -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Eliminar Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar el rol <strong id="deleteRoleName"></strong>?</p>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteRoleForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function loadEditModal(roleId, roleName, permissions) {
    document.getElementById('edit_name').value = roleName;
    document.getElementById('editRoleForm').action = `/roles/${roleId}`;
    
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

function setDeleteRole(roleId, roleName) {
    document.getElementById('deleteRoleName').textContent = roleName;
    document.getElementById('deleteRoleForm').action = `/roles/${roleId}`;
}
</script>
@endsection