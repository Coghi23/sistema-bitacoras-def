@extends('Template-administrador')

@section('content')

<style>
/* Responsive adjustments for Permisos */
@media (max-width: 768px) {
    .main-content {
        margin-left: 0 !important;
        padding: 0.5rem !important;
    }
    
    .search-bar-wrapper {
        flex-direction: column !important;
        gap: 1rem;
    }
    
    .search-bar {
        margin-bottom: 0 !important;
    }
    
    .btn-agregar {
        width: 100%;
        justify-content: center !important;
        margin-left: 0 !important;
    }
    
    /* Tabs responsive */
    .nav-tabs {
        flex-wrap: wrap;
        border-bottom: 1px solid #dee2e6;
    }
    
    .nav-tabs .nav-item {
        margin-bottom: 0;
    }
    
    .nav-tabs .nav-link {
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
        white-space: nowrap;
    }
    
    /* Table responsive */
    .table-responsive {
        border: none;
    }
    
    .table {
        font-size: 0.85rem;
    }
    
    .table th,
    .table td {
        padding: 0.5rem 0.25rem;
        vertical-align: middle;
    }
    
    .table th:first-child,
    .table td:first-child {
        padding-left: 0.5rem;
    }
    
    .table th:last-child,
    .table td:last-child {
        padding-right: 0.5rem;
    }
    
    /* Action buttons */
    .btn.p-0 {
        padding: 0.25rem !important;
        margin: 0 0.1rem;
    }
    
    .btn.p-0 i {
        font-size: 1.1rem;
    }
    
    /* Modal responsive */
    .modal-dialog {
        margin: 0.5rem;
        max-width: calc(100% - 1rem);
    }
    
    .modal-body {
        padding: 1rem 0.75rem;
    }
    
    .modal-footer {
        padding: 0.75rem;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .modal-footer .btn {
        width: 100%;
        margin: 0;
    }
    
    /* Search input responsive */
    .search-icon {
        left: 0.75rem;
    }
    
    .form-control {
        font-size: 1rem;
        padding: 0.75rem 0.75rem 0.75rem 2.5rem;
    }
    
    /* Alert responsive */
    .alert {
        margin: 0.5rem 0;
        padding: 0.75rem;
        font-size: 0.9rem;
    }
    
    /* Pagination responsive */
    .pagination {
        font-size: 0.85rem;
    }
    
    .page-link {
        padding: 0.375rem 0.5rem;
    }
}

@media (max-width: 576px) {
    .nav-tabs .nav-link {
        font-size: 0.75rem;
        padding: 0.4rem 0.5rem;
    }
    
    .table {
        font-size: 0.8rem;
    }
    
    .btn-agregar {
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }
    
    .modal-title {
        font-size: 1.1rem;
    }
}
</style>

<div class="wrapper">
    <div class="main-content p-4" style="margin-left: 90px;">
        <div class="row align-items-end mb-4">
            <div class="search-bar-wrapper mb-4 d-flex align-items-center">
                <div class="search-bar flex-grow-1">
                    <form id="busquedaForm" method="GET" action="{{ route('permisos.index') }}" class="w-100 position-relative">
                        <span class="search-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Buscar permiso..." name="busquedaPermiso" value="{{ request('busquedaPermiso') }}" id="inputBusqueda" autocomplete="off">
                        @if(request('busquedaPermiso'))
                        <button type="button" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" id="limpiarBusqueda" title="Limpiar búsqueda" style="background: transparent;">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        @endif
                    </form>
                </div>
                <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
                    data-bs-toggle="modal" data-bs-target="#createPermisoModal"
                    title="Agregar Permiso" style="background-color: #134496; font-size: 1.2rem;">
                    Agregar <i class="bi bi-plus-circle ms-2"></i>
                </button>
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
        <div id="tabla-permisos">
            <ul class="nav nav-tabs mb-3" id="permisoTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ ($tab ?? 'ver') === 'ver' ? 'active' : '' }}" href="?tab=ver{{ request('busquedaPermiso') ? '&busquedaPermiso='.request('busquedaPermiso') : '' }}">Ver</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ ($tab ?? '') === 'crear' ? 'active' : '' }}" href="?tab=crear{{ request('busquedaPermiso') ? '&busquedaPermiso='.request('busquedaPermiso') : '' }}">Crear</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ ($tab ?? '') === 'editar' ? 'active' : '' }}" href="?tab=editar{{ request('busquedaPermiso') ? '&busquedaPermiso='.request('busquedaPermiso') : '' }}">Editar</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ ($tab ?? '') === 'eliminar' ? 'active' : '' }}" href="?tab=eliminar{{ request('busquedaPermiso') ? '&busquedaPermiso='.request('busquedaPermiso') : '' }}">Eliminar</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ ($tab ?? '') === 'sidebar' ? 'active' : '' }}" href="?tab=sidebar{{ request('busquedaPermiso') ? '&busquedaPermiso='.request('busquedaPermiso') : '' }}">
                        <i class="bi bi-sidebar"></i> Sidebar
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ ($tab ?? '') === 'otros' ? 'active' : '' }}" href="?tab=otros{{ request('busquedaPermiso') ? '&busquedaPermiso='.request('busquedaPermiso') : '' }}">Otros</a>
                </li>
            </ul>
            <div class="tab-content" id="permisoTabsContent">
                <div class="tab-pane fade show active" id="{{ $tab ?? 'ver' }}" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr class="header-row">
                                    <th>Nombre del Permiso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permisos as $permiso)
                                <tr class="record-row">
                                    <td>{{ $permiso->name }}</td>
                                    <td>
                                        <button class="btn p-0 me-2" data-bs-toggle="modal" data-bs-target="#editPermisoModal{{ $permiso->id }}" title="Editar permiso">
                                            <i class="bi bi-pencil icon-editar"></i>
                                        </button>
                                        <button class="btn p-0 me-2" data-bs-toggle="modal" data-bs-target="#deletePermisoModal{{ $permiso->id }}" title="Eliminar permiso">
                                            <i class="bi bi-trash icon-eliminar"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr class="record-row">
                                    <td class="text-center" colspan="2">No hay permisos registrados en esta categoría.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $permisos->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Crear Permiso -->
<div class="modal fade" id="createPermisoModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Permiso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('permisos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre del Permiso</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Permiso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales Editar y Eliminar Permiso -->
@foreach($permisos as $permiso)
<!-- Modal Editar Permiso -->
<div class="modal fade" id="editPermisoModal{{ $permiso->id }}" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Editar Permiso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('permisos.update', $permiso->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name_{{ $permiso->id }}" class="form-label">Nombre del Permiso</label>
                        <input type="text" class="form-control" id="edit_name_{{ $permiso->id }}" name="name" value="{{ $permiso->name }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Permiso</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Eliminar Permiso -->
<div class="modal fade" id="deletePermisoModal{{ $permiso->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <form method="POST" action="{{ route('permisos.destroy', $permiso->id) }}">
                @csrf
                @method('DELETE')
                <div class="modal-body d-flex flex-column align-items-center gap-3 p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#efc737" class="bi bi-question-circle-fill" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.496 6.033h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286a.237.237 0 0 0 .241.247m2.325 6.443c.61 0 1.029-.394 1.029-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94 0 .533.425.927 1.01.927z"/>
                    </svg>
                    <p>¿Está seguro de eliminar este permiso?</p>
                    <p class="text-muted small">{{ $permiso->name }}</p>
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
// Búsqueda en tiempo real y limpiar
let timeoutId;
const inputBusqueda = document.getElementById('inputBusqueda');
const formBusqueda = document.getElementById('busquedaForm');
const btnLimpiar = document.getElementById('limpiarBusqueda');

if (inputBusqueda) {
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
        inputBusqueda.value = '';
        window.location.href = '{{ route("permisos.index") }}';
    });
}
</script>
@endsection
