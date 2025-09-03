@extends('Template-administrador')

@section('title', 'Sistema de Bitácoras')

@section('content')

<style>
/* Responsive adjustments for Instituciones */
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

<div id="instituciones-container" class="wrapper">
    <div id="main-content" class="main-content">
        {{-- Header con búsqueda y botón agregar --}}
        <div id="header-section" class="row align-items-end mb-4">
            <div id="search-wrapper" class="search-bar-wrapper mb-4 d-flex align-items-center">
                <div id="search-bar-container" class="search-bar flex-grow-1">
                    <form id="busquedaForm" method="GET" action="{{ route('institucion.index') }}" class="w-100 position-relative">
                        <span class="search-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="inputBusqueda" class="form-control" placeholder="Buscar institución..." 
                               name="busquedaInstitucion" value="{{ request('busquedaInstitucion') }}" autocomplete="off">
                        @if(request('busquedaInstitucion'))
                        <button type="button" id="limpiarBusqueda" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" 
                                title="Limpiar búsqueda" style="background: transparent;">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        @endif
                    </form>
                </div>
                @can('create_institucion')
                    <button id="btn-agregar-institucion" class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar" 
                        data-bs-toggle="modal" data-bs-target="#modalAgregarInstitucion" 
                        title="Agregar Institución" style="background-color: #134496; font-size: 1.2rem;">
                        Agregar <i class="bi bi-plus-circle ms-2"></i>
                    </button>
                @endcan
            </div>
        </div>

        {{-- Botones de filtros --}}
        <div id="filter-buttons" class="filter-buttons mb-3">
            <div class="d-flex flex-column flex-md-row gap-2">
                <a href="{{ route('institucion.index', ['inactivos' => 1]) }}" class="btn btn-warning">
                    Mostrar inactivos
                </a>
                <a href="{{ route('institucion.index', ['activos' => 1]) }}" class="btn btn-primary">
                    Mostrar activos
                </a>
            </div>
        </div>

        {{-- Indicador de resultados de búsqueda --}}
        @if(request('busquedaInstitucion'))
            <div id="search-results" class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <span>
                    Mostrando {{ $instituciones->count() }} resultado(s) para "<strong>{{ request('busquedaInstitucion') }}</strong>"
                    <a href="{{ route('institucion.index') }}" class="btn btn-sm btn-outline-primary ms-2">Ver todas</a>
                </span>
            </div>
        @endif

        {{-- Tabla de Instituciones --}}
        <div id="tabla-instituciones" class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr id="instituciones-header-row">
                        <th class="text-center">Nombre de la Institución</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="instituciones-tbody">
                    @php
                        $mostrarActivos = request('activos') == 1 || !request('inactivos');
                        $mostrarInactivos = request('inactivos') == 1;
                    @endphp
                    @forelse ($instituciones as $institucion)
                        @if (($mostrarActivos && $institucion->condicion == 1) || ($mostrarInactivos && $institucion->condicion == 0))
                            <tr id="institucion-row-{{ $institucion->id }}">
                                @can('view_institucion')
                                    <td class="text-center">{{ $institucion->nombre }}</td>
                                    <td class="text-center">
                                        <span id="estado-badge-{{ $institucion->id }}" class="badge {{ $institucion->condicion == 1 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $institucion->condicion == 1 ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div id="actions-{{ $institucion->id }}" class="d-flex flex-column flex-md-row justify-content-center gap-1">
                                            @if($mostrarActivos && $institucion->condicion == 1)
                                                @can('edit_institucion')
                                                    <button id="btn-edit-{{ $institucion->id }}" type="button" class="btn btn-link text-info p-0"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalEditarInstitucion-{{ $institucion->id }}"
                                                        title="Editar institución">
                                                        <i class="bi bi-pencil" style="font-size: 1.5rem;"></i>
                                                    </button>
                                                @endcan
                                                @can('delete_institucion')
                                                    <button id="btn-delete-{{ $institucion->id }}" type="button" class="btn btn-link text-danger p-0" 
                                                            data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $institucion->id }}" 
                                                            title="Eliminar institución">
                                                        <i class="bi bi-trash" style="font-size: 1.5rem;"></i>
                                                    </button>
                                                @endcan
                                            @elseif($mostrarInactivos && $institucion->condicion == 0)
                                                @can('restore_institucion')
                                                    <button id="btn-restore-{{ $institucion->id }}" type="button" class="btn btn-link text-success p-0" 
                                                            data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $institucion->id }}" 
                                                            title="Restaurar institución">
                                                        <i class="bi bi-arrow-counterclockwise" style="font-size: 1.5rem;"></i>
                                                    </button>
                                                @endcan        
                                            @endif
                                        </div>
                                    </td>
                                @endcan
                            </tr>
                        @endif

                        {{-- Modal Editar Institución --}}
                        <div class="modal fade" id="modalEditarInstitucion-{{ $institucion->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-custom">
                                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                                            <i class="bi bi-arrow-left"></i>
                                        </button>
                                        <h5 class="modal-title">Editar Institución</h5>
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
                                        
                                        <form id="formEditarInstitucion-{{ $institucion->id }}" action="{{ route('institucion.update', $institucion->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="form_type" value="edit">
                                            <input type="hidden" name="institucion_id" value="{{ $institucion->id }}">
                                            
                                            <div class="mb-3">
                                                <label for="nombre-edit-{{ $institucion->id }}" class="form-label fw-bold">Nombre de la Institución</label>
                                                <input type="text" id="nombre-edit-{{ $institucion->id }}" name="nombre" class="form-control"
                                                    value="{{ old('nombre', $institucion->nombre) }}" required>
                                            </div>
                                            <div class="text-center mt-4">
                                                <button type="submit" class="btn btn-primary">Modificar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Modal eliminar/restaurar --}}
                        <div class="modal fade" id="modalConfirmacionEliminar-{{ $institucion->id }}" tabindex="-1" 
                             aria-labelledby="modalInstitucionEliminarLabel-{{ $institucion->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content custom-modal">
                                    <div class="modal-body text-center">
                                        <div class="icon-container">
                                            <div class="circle-icon {{ $institucion->condicion == 1 ? '' : 'bg-success text-white' }}">
                                                @if($institucion->condicion == 1)
                                                    <i class="bi bi-exclamation-circle"></i>
                                                @else
                                                    <i class="bi bi-arrow-counterclockwise text-white"></i>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="modal-text">
                                            @if($institucion->condicion == 1)
                                                ¿Desea eliminar la Institución?
                                            @else
                                                ¿Desea restaurar la Institución?
                                            @endif
                                        </p>
                                        <div class="btn-group-custom d-flex justify-content-center gap-2">
                                            <form action="{{ route('institucion.destroy', ['institucion' => $institucion->id]) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-custom {{ $institucion->condicion == 1 ? '' : 'bg-success text-white' }}">Sí</button>
                                                <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                    <tr id="no-instituciones-row">
                        <td class="text-center" colspan="3">
                            <div class="text-muted py-4">
                                <i class="bi bi-building display-4 mb-3"></i>
                                <h5>No hay instituciones registradas</h5>
                                <p>Las instituciones aparecerán aquí cuando se registren.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Crear Institución --}}
<div class="modal fade" id="modalAgregarInstitucion" tabindex="-1" aria-labelledby="modalAgregarInstitucionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <h5 class="modal-title">Crear Nueva Institución</h5>
            </div>
            <div class="modal-body px-4 py-4">
                <form id="formCrearInstitucion" action="{{ route('institucion.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_type" value="create">
                    <div class="mb-3">
                        <label for="nombreInstitucion" class="form-label fw-bold">Nombre de la Institución</label>
                        <input type="text" name="nombre" id="nombreInstitucion" class="form-control" 
                               placeholder="Ingrese el Nombre de la Institución" value="{{ old('nombre') }}" required>
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
            window.location.href = '{{ route("institucion.index") }}';
        });
    }

    // Mantener modal abierto si hay errores
    @if ($errors->any())
        const formType = '{{ old("form_type") }}';
        const institucionId = '{{ old("institucion_id") }}';
        
        if (formType === 'create') {
            var modal = new bootstrap.Modal(document.getElementById('modalAgregarInstitucion'));
            modal.show();
        } else if (formType === 'edit' && institucionId) {
            var modal = new bootstrap.Modal(document.getElementById('modalEditarInstitucion-' + institucionId));
            modal.show();
        }
    @endif

    // Obtener nombres de instituciones existentes
    function obtenerNombresInstituciones() {
        const nombres = [];
        document.querySelectorAll('tbody tr td.text-center:first-child').forEach(function(td) {
            if (td.textContent && td.textContent.trim() !== 'No hay instituciones registradas') {
                nombres.push(td.textContent.trim().toLowerCase());
            }
        });
        return nombres;
    }

    // Validación para el formulario de agregar institución
    var formAgregar = document.querySelector('#modalAgregarInstitucion form');
    if (formAgregar) {
        formAgregar.addEventListener('submit', function(e) {
            var nombre = formAgregar.querySelector('[name="nombre"]');
            var nombreValor = nombre.value.trim().toLowerCase();
            var nombresExistentes = obtenerNombresInstituciones();
            
            if (!nombre.value.trim() || nombre.value.trim().length < 3) {
                e.preventDefault();
                alert('El nombre de la institución es obligatorio y debe tener al menos 3 caracteres.');
                nombre.focus();
                return;
            }
            
            if (nombresExistentes.includes(nombreValor)) {
                e.preventDefault();
                alert('Ya existe una institución con ese nombre.');
                nombre.focus();
            }
        });
    }

    // Validación para los formularios de editar institución
    document.querySelectorAll('[id^="modalEditarInstitucion-"] form').forEach(function(formEditar) {
        formEditar.addEventListener('submit', function(e) {
            var nombre = formEditar.querySelector('[name="nombre"]');
            var nombreValor = nombre.value.trim().toLowerCase();
            var nombresExistentes = obtenerNombresInstituciones();

            // Excluir el nombre actual de la institución editada
            var nombreActual = nombre.getAttribute('value') ? nombre.getAttribute('value').trim().toLowerCase() : 
                               nombre.defaultValue ? nombre.defaultValue.trim().toLowerCase() : '';
            var nombresSinActual = nombresExistentes.filter(function(n) { return n !== nombreActual; });

            if (!nombre.value.trim() || nombre.value.trim().length < 3) {
                e.preventDefault();
                alert('El nombre de la institución es obligatorio y debe tener al menos 3 caracteres.');
                nombre.focus();
                return;
            }
            
            if (nombresSinActual.includes(nombreValor)) {
                e.preventDefault();
                alert('Ya existe una institución con ese nombre.');
                nombre.focus();
            }
        });
    });
});
</script>
@endpush