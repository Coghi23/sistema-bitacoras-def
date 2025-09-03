@extends('Template-administrador')

@section('title', 'Sistema de Bitácoras')

@section('content')

<style>
/* Responsive adjustments for Especialidades */
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
    
    .badge {
        font-size: 0.7rem;
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

<div id="especialidades-container" class="wrapper">
    <div id="main-content" class="main-content">
        {{-- Header con búsqueda y botón agregar --}}
        <div id="header-section" class="row align-items-end mb-4">
            <div id="search-wrapper" class="search-bar-wrapper mb-4 d-flex align-items-center">
                <div id="search-bar-container" class="search-bar flex-grow-1">
                    <form id="busquedaForm" method="GET" action="{{ route('especialidad.index') }}" class="w-100 position-relative">
                        <span class="search-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="inputBusqueda" class="form-control" placeholder="Buscar especialidad o institución..." 
                               name="busquedaEspecialidad" value="{{ request('busquedaEspecialidad') }}" autocomplete="off">
                        @if(request('busquedaEspecialidad'))
                        <button type="button" id="limpiarBusqueda" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" 
                                title="Limpiar búsqueda" style="background: transparent;">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        @endif
                    </form>
                </div>
                @can('create_especialidad')
                    <button id="btn-agregar-especialidad" class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar" 
                        data-bs-toggle="modal" data-bs-target="#modalAgregarespecialidad" 
                        title="Agregar Especialidad" style="background-color: #134496; font-size: 1.2rem;">
                        Agregar <i class="bi bi-plus-circle ms-2"></i>
                    </button>
                @endcan
            </div>
        </div>

        {{-- Botones de filtros --}}
        <div id="filter-buttons" class="filter-buttons mb-3">
            <div class="d-flex flex-column flex-md-row gap-2">
                <a href="{{ route('especialidad.index', ['inactivos' => 1]) }}" class="btn btn-warning">
                    Mostrar inactivos
                </a>
                <a href="{{ route('especialidad.index', ['activos' => 1]) }}" class="btn btn-primary">
                    Mostrar activos
                </a>
            </div>
        </div>

        {{-- Indicador de resultados de búsqueda --}}
        @if(request('busquedaEspecialidad'))
            <div id="search-results" class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <span>
                    Mostrando {{ $especialidades->count() }} resultado(s) para "<strong>{{ request('busquedaEspecialidad') }}</strong>"
                    <a href="{{ route('especialidad.index') }}" class="btn btn-sm btn-outline-primary ms-2">Ver todas</a>
                </span>
            </div>
        @endif

        {{-- Tabla de Especialidades --}}
        <div id="tabla-especialidades" class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr id="especialidades-header-row">
                        <th class="text-center">Nombre de la Especialidad</th>
                        <th class="text-center">Institución</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="especialidades-tbody">
                    @php
                        $mostrarActivos = request('activos') == 1 || !request('inactivos');
                        $mostrarInactivos = request('inactivos') == 1;
                    @endphp
                    @forelse ($especialidades as $especialidad)
                        @if (($mostrarActivos && $especialidad->condicion == 1) || ($mostrarInactivos && $especialidad->condicion == 0))
                            @can('view_especialidad')
                                <tr id="especialidad-row-{{ $especialidad->id }}">
                                    <td class="text-center">{{ $especialidad->nombre }}</td>
                                    <td class="text-center">{{ $especialidad->institucion->nombre ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span id="estado-badge-{{ $especialidad->id }}" class="badge {{ $especialidad->condicion == 1 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $especialidad->condicion == 1 ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div id="actions-{{ $especialidad->id }}" class="d-flex flex-column flex-md-row justify-content-center gap-1">
                                            @if($mostrarActivos && $especialidad->condicion == 1)
                                                @can('edit_especialidad')
                                                    <button id="btn-edit-{{ $especialidad->id }}" class="btn btn-link text-info p-0" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEditarEspecialidad-{{ $especialidad->id }}"
                                                        title="Editar especialidad">
                                                        <i class="bi bi-pencil" style="font-size: 1.5rem;"></i>
                                                    </button>
                                                @endcan
                                                @can('delete_especialidad')
                                                    <button id="btn-delete-{{ $especialidad->id }}" type="button" class="btn btn-link text-danger p-0" 
                                                            data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $especialidad->id }}" 
                                                            title="Eliminar especialidad">
                                                        <i class="bi bi-trash" style="font-size: 1.5rem;"></i>
                                                    </button>
                                                @endcan
                                            @elseif($mostrarInactivos && $especialidad->condicion == 0)
                                                @can('delete_especialidad')
                                                    <button id="btn-restore-{{ $especialidad->id }}" type="button" class="btn btn-link text-success p-0" 
                                                            data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $especialidad->id }}" 
                                                            title="Restaurar especialidad">
                                                        <i class="bi bi-arrow-counterclockwise" style="font-size: 1.5rem;"></i>
                                                    </button>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endcan
                        @endif

                       <!-- Modal Editar Especialidad -->
                        <div class="modal fade" id="modalEditarEspecialidad-{{ $especialidad->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-custom">
                                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                                            <i class="bi bi-arrow-left"></i>
                                        </button>
                                        <h5 class="modal-title">Editar Especialidad</h5>
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
                                        
                                        <form id="formEditarEspecialidad-{{ $especialidad->id }}" action="{{ route('especialidad.update', $especialidad->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="form_type" value="edit">
                                            <input type="hidden" name="especialidad_id" value="{{ $especialidad->id }}">
                                            
                                            <div class="mb-3">
                                                <label for="nombre-edit-{{ $especialidad->id }}" class="form-label fw-bold">Nombre de la Especialidad</label>
                                                <input type="text" id="nombre-edit-{{ $especialidad->id }}" name="nombre" class="form-control"
                                                    value="{{ old('nombre', $especialidad->nombre) }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="institucion-edit-{{ $especialidad->id }}" class="form-label fw-bold">Institución</label>
                                                <select id="institucion-edit-{{ $especialidad->id }}" name="id_institucion" class="form-select" required>
                                                    <option value="">Seleccione una Institución</option>
                                                    @foreach ($instituciones as $institucion)
                                                        <option value="{{ $institucion->id }}" 
                                                            {{ old('id_institucion', $especialidad->id_institucion) == $institucion->id ? 'selected' : '' }}>
                                                            {{ $institucion->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="text-center mt-4">
                                                <button type="submit" class="btn btn-primary">Modificar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal eliminar/restaurar -->
                        <div class="modal fade" id="modalConfirmacionEliminar-{{ $especialidad->id }}" tabindex="-1" 
                             aria-labelledby="modalEspecialidadEliminarLabel-{{ $especialidad->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content custom-modal">
                                    <div class="modal-body text-center">
                                        <div class="icon-container">
                                            <div class="circle-icon {{ $especialidad->condicion == 1 ? '' : 'bg-success text-white' }}">
                                            @if($especialidad->condicion == 1)
                                                <i class="bi bi-exclamation-circle"></i>
                                            @else
                                                <i class="bi bi-arrow-counterclockwise text-white"></i>
                                            @endif
                                            </div>
                                        </div>
                                        <p class="modal-text">
                                            @if($especialidad->condicion == 1)
                                                ¿Desea eliminar la Especialidad?
                                            @else
                                                ¿Desea restaurar la Especialidad?
                                            @endif
                                        </p>
                                        <div class="btn-group-custom d-flex justify-content-center gap-2">
                                            <form action="{{ route('especialidad.destroy', $especialidad->id) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-custom {{ $especialidad->condicion == 1 ? '' : 'bg-success text-white' }}">Sí</button>
                                                <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                    <tr id="no-especialidades-row">
                        <td class="text-center" colspan="4">
                            <div class="text-muted py-4">
                                <i class="bi bi-mortarboard display-4 mb-3"></i>
                                <h5>No hay especialidades registradas</h5>
                                <p>Las especialidades aparecerán aquí cuando se registren.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Crear Especialidad -->
<div class="modal fade" id="modalAgregarespecialidad" tabindex="-1" aria-labelledby="modalAgregarespecialidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <h5 class="modal-title">Crear Nueva Especialidad</h5>
            </div>
            <div class="modal-body px-4 py-4">
                <form id="formCrearEspecialidad" action="{{ route('especialidad.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_type" value="create">
                    <div class="mb-3">
                        <label for="nombreespecialidad" class="form-label fw-bold">Nombre de la Especialidad</label>
                        <input type="text" name="nombre" id="nombreespecialidad" class="form-control" 
                               value="{{old('nombre')}}" placeholder="Ingrese el nombre de la Especialidad" required>
                        
                        <label for="id_institucion" class="form-label fw-bold mt-3">Institución</label>
                        <select name="id_institucion" id="id_institucion" class="form-select" required>
                            <option value="">Seleccione una Institución</option>
                            @foreach ($instituciones as $institucion)
                                <option value="{{$institucion->id}}" {{ old('id_institucion') == $institucion->id ? 'selected' : '' }}>{{$institucion->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-crear">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
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
            window.location.href = '{{ route("especialidad.index") }}';
        });
    }

    // Mantener modal abierto si hay errores
    @if ($errors->any())
        const formType = '{{ old("form_type") }}';
        const especialidadId = '{{ old("especialidad_id") }}';
        
        if (formType === 'create') {
            var modal = new bootstrap.Modal(document.getElementById('modalAgregarespecialidad'));
            modal.show();
        } else if (formType === 'edit' && especialidadId) {
            var modal = new bootstrap.Modal(document.getElementById('modalEditarEspecialidad-' + especialidadId));
            modal.show();
        }
    @endif
});
</script>
@endpush