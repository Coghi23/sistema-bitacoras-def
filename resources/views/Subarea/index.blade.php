@extends('Template-administrador')

@section('title', 'Registro de Subárea')

@section('content')
<style>
/* Responsive adjustments for Subareas */
@media (max-width: 768px) {
    .main-content {
        padding: 0.5rem !important;
    }
    
    .search-bar-wrapper {
        flex-direction: column !important;
        gap: 0.75rem;
    }
    
    .search-bar {
        width: 100% !important;
    }
    
    .btn-agregar {
        width: 100% !important;
        justify-content: center !important;
        margin-left: 0 !important;
        font-size: 1rem !important;
    }
    
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .table th, .table td {
        padding: 0.5rem !important;
        vertical-align: middle;
    }
    
    .modal-dialog {
        margin: 0.5rem !important;
        max-width: calc(100% - 1rem) !important;
    }
    
    .filter-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .filter-buttons .btn {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .table {
        font-size: 0.8rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
    }
    
    .alert {
        font-size: 0.85rem;
        padding: 0.5rem;
    }
}
</style>

<div id="subareas-container" class="wrapper">
    <div id="main-content" class="main-content">
        {{-- Header con búsqueda y botón agregar --}}
        <div id="header-section" class="row align-items-end mb-4">
            <div id="search-wrapper" class="search-bar-wrapper mb-4 d-flex align-items-center">
                <div id="search-bar-container" class="search-bar flex-grow-1">
                    <form id="busquedaForm" method="GET" action="{{ route('subarea.index') }}" class="w-100 position-relative">
                        <span class="search-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="inputBusqueda" class="form-control" placeholder="Buscar subárea..." 
                               name="busquedaSubarea" value="{{ request('busquedaSubarea') }}" autocomplete="off">
                        @if(request('busquedaSubarea'))
                        <button type="button" id="limpiarBusqueda" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" 
                                title="Limpiar búsqueda" style="background: transparent;">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        @endif
                    </form>
                </div>
                @can('create_subarea')
                    <button id="btn-agregar-subarea" class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
                        data-bs-toggle="modal" data-bs-target="#modalAgregarSubArea"
                        title="Agregar Subárea" style="background-color: #134496; font-size: 1.2rem;">
                        Agregar <i class="bi bi-plus-circle ms-2"></i>
                    </button>
                @endcan
            </div>
        </div>

        {{-- Indicador de resultados de búsqueda --}}
        @if(request('busquedaSubarea'))
            <div id="search-results" class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <span>
                    Mostrando {{ $subareas->count() }} resultado(s) para "<strong>{{ request('busquedaSubarea') }}</strong>"
                    <a href="{{ route('subarea.index') }}" class="btn btn-sm btn-outline-primary ms-2">Ver todas</a>
                </span>
            </div>
        @endif

        {{-- Botones de filtros --}}
        <div id="filter-buttons" class="filter-buttons mb-3">
            <div class="d-flex flex-column flex-md-row gap-2">
                <a href="{{ route('subarea.index', ['inactivos' => 1]) }}" class="btn btn-warning">
                    Mostrar inactivos
                </a>
                <a href="{{ route('subarea.index', ['activos' => 1]) }}" class="btn btn-primary">
                    Mostrar activos
                </a>
            </div>
        </div>

        {{-- Tabla de Subáreas --}}
        <div id="tabla-subareas" class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr id="header-row">
                        <th class="text-center">Nombre</th>
                        <th class="text-center">Especialidad</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-body">
                    @php
                        $mostrarActivos = request('activos') == 1 || !request('inactivos');
                        $mostrarInactivos = request('inactivos') == 1;
                    @endphp
                    @forelse ($subareas as $subarea)
                        @if (
                            ($mostrarActivos && $subarea->condicion == 1) ||
                            ($mostrarInactivos && $subarea->condicion == 0)
                        )
                        <tr id="subarea-row-{{ $subarea->id }}">
                            @can('view_subarea')
                                <td class="text-center">{{ $subarea->nombre }}</td>
                                <td class="text-center">{{ $subarea->especialidad ? $subarea->especialidad->nombre : 'Sin especialidad' }}</td>
                                <td class="text-center">
                                    <span id="estado-badge-{{ $subarea->id }}" class="badge {{ $subarea->condicion == 1 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $subarea->condicion == 1 ? 'Activa' : 'Inactiva' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div id="actions-{{ $subarea->id }}" class="d-flex flex-column flex-md-row justify-content-center gap-1">
                                        @can('view_subarea')
                                            <button id="btn-edit-{{ $subarea->id }}" class="btn btn-link text-info p-0" 
                                                    data-bs-toggle="modal" data-bs-target="#modalEditarSubArea-{{ $subarea->id }}">
                                                <i class="bi bi-pencil" style="font-size: 1.5rem;"></i>
                                            </button>
                                        @endcan
                                        @can('delete_subarea')
                                            <button id="btn-action-{{ $subarea->id }}" class="btn btn-link {{ $subarea->condicion == 1 ? 'text-danger' : 'text-success' }} p-0"
                                                    data-bs-toggle="modal" data-bs-target="#modalEliminarSubarea-{{ $subarea->id }}">
                                                @if($subarea->condicion == 1)
                                                    <i class="bi bi-trash" style="font-size: 1.5rem;"></i>
                                                @else
                                                    <i class="bi bi-arrow-counterclockwise" style="font-size: 1.5rem;"></i>
                                                @endif
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            @endcan
                        </tr>
                        @endif

                        {{-- Modal Editar Subárea --}}
                        @if(Auth::user() && !Auth::user()->hasRole('director'))
                        <div class="modal fade" id="modalEditarSubArea-{{ $subarea->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-custom">
                                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                                            <i class="bi bi-arrow-left"></i>
                                        </button>
                                        <h5 class="modal-title">Editar Subárea</h5>
                                    </div>
                                    <div class="modal-body px-4 py-4">
                                        {{-- Mostrar errores de validación --}}
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                @foreach ($errors->all() as $error)
                                                    <div>{{ $error }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <form id="formEditarSubarea-{{ $subarea->id }}" action="{{ route('subarea.update', $subarea->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="form_type" value="edit">
                                            <input type="hidden" name="subarea_id" value="{{ $subarea->id }}">
                                            
                                            <div class="mb-3">
                                                <label for="nombre-edit-{{ $subarea->id }}" class="form-label fw-bold">Nombre de la Subárea</label>
                                                <input type="text" id="nombre-edit-{{ $subarea->id }}" name="nombre" 
                                                       class="form-control" value="{{ old('nombre', $subarea->nombre) }}" required>

                                                <label for="especialidad-edit-{{ $subarea->id }}" class="form-label fw-bold mt-3">Especialidad</label>
                                                <select id="especialidad-edit-{{ $subarea->id }}" name="id_especialidad" class="form-select" required>
                                                    @foreach ($especialidades as $especialidad)
                                                    <option value="{{ $especialidad->id }}"
                                                        {{ $subarea->id_especialidad == $especialidad->id ? 'selected' : '' }}>
                                                        {{ $especialidad->nombre }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="text-center mt-4">
                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal Eliminar/Restaurar Subárea --}}
                        <div class="modal fade" id="modalEliminarSubarea-{{ $subarea->id }}" tabindex="-1" 
                             aria-labelledby="modalSubareaEliminarLabel-{{ $subarea->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content custom-modal">
                                    <div class="modal-body text-center">
                                        <div class="icon-container">
                                            <div class="circle-icon {{ $subarea->condicion == 1 ? '' : 'bg-success text-white' }}">
                                            @if($subarea->condicion == 1)
                                                <i class="bi bi-exclamation-circle"></i>
                                            @else
                                                <i class="bi bi-arrow-counterclockwise text-white"></i>
                                            @endif
                                            </div>
                                        </div>
                                        <p class="modal-text">
                                            @if($subarea->condicion == 1)
                                                ¿Desea eliminar la Subárea?
                                            @else
                                                ¿Desea restaurar la Subárea?
                                            @endif
                                        </p>
                                        <div class="btn-group-custom d-flex justify-content-center gap-2">
                                            <form action="{{ route('subarea.destroy', ['subarea' => $subarea->id]) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-custom {{ $subarea->condicion == 1 ? '' : 'bg-success text-white' }}">
                                                    Sí
                                                </button>
                                                <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                    <tr id="no-results-row">
                        <td class="text-center" colspan="4">No hay subáreas registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Crear Subárea --}}
<div class="modal fade" id="modalAgregarSubArea" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <h5 class="modal-title">Crear Nueva Subárea</h5>
            </div>
            <div class="modal-body px-4 py-4">
                {{-- Mostrar errores de validación --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                <form id="formCrearSubarea" action="{{ route('subarea.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_type" value="create">
                    <div class="mb-3">
                        <label for="nombre-crear" class="form-label fw-bold">Nombre de la Subárea</label>
                        <input type="text" id="nombre-crear" name="nombre" class="form-control" 
                               value="{{ old('nombre') }}" required>

                        <label for="especialidad-crear" class="form-label fw-bold mt-3">Especialidad</label>
                        <select id="especialidad-crear" name="id_especialidad" class="form-select" required>
                            <option value="">Seleccione una Especialidad</option>
                            @foreach ($especialidades as $especialidad)
                            <option value="{{ $especialidad->id }}" {{ old('id_especialidad') == $especialidad->id ? 'selected' : '' }}>
                                {{ $especialidad->nombre }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
            window.location.href = '{{ route("subarea.index") }}';
        });
    }

    // Mantener modal abierto si hay errores
    @if ($errors->any())
        const formType = '{{ old("form_type") }}';
        const subareaId = '{{ old("subarea_id") }}';

        if (formType === 'create') {
            var modal = new bootstrap.Modal(document.getElementById('modalAgregarSubArea'));
            modal.show();
        } else if (formType === 'edit' && subareaId) {
            var modal = new bootstrap.Modal(document.getElementById('modalEditarSubArea-' + subareaId));
            modal.show();
        }
    @endif
});
</script>
@endsection


