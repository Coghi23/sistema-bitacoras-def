@extends('Template-administrador')

@section('title', 'Registro de Subárea')

@section('content')
<div class="wrapper">
    <div class="main-content">

        {{-- Encabezado de búsqueda y botón Agregar --}}
        <div class="search-bar-wrapper mb-4">
            <div class="search-bar">
                <form id="busquedaForm" method="GET" action="{{ route('subarea.index') }}" class="w-100 position-relative">
                    <span class="search-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Buscar subárea..."
                        name="busquedaSubarea"
                        value="{{ request('busquedaSubarea') }}"
                        id="inputBusqueda"
                        autocomplete="off"
                    >
                    @if(request('busquedaSubarea'))
                    <button
                        type="button"
                        class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2"
                        id="limpiarBusqueda"
                        title="Limpiar búsqueda"
                        style="background: transparent;"
                    >
                        <i class="bi bi-x-circle"></i>
                    </button>
                    @endif
                </form>
            </div>
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
                data-bs-toggle="modal" data-bs-target="#modalAgregarSubArea"
                title="Agregar Subárea" style="background-color: #134496; font-size: 1.2rem;">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
        </div>

        {{-- Tabla de Subáreas --}}
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th class="text-center">Nombre</th>
                        <th class="text-center">Especialidad</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subareas as $subarea)
                    <tr>
                        @if ($subarea->condicion == 1)
                            <td class="text-center">{{ $subarea->nombre }}</td>
                            <td class="text-center">{{ $subarea->especialidad ? $subarea->especialidad->nombre : 'Sin especialidad' }}</td>
                            <td class="text-center">
                                <button class="btn btn-link text-info p-0 me-2" data-bs-toggle="modal"
                                    data-bs-target="#modalEditarSubArea-{{ $subarea->id }}">
                                    <i class="bi bi-pencil" style="font-size: 1.5rem;"></i>
                                </button>
                                <button class="btn btn-link text-danger p-0" data-bs-toggle="modal"
                                    data-bs-target="#modalEliminarSubarea-{{ $subarea->id }}">
                                    <i class="bi bi-trash" style="font-size: 1.5rem;"></i>
                                </button>
                            </td>
                        @endif
                    </tr>

                    {{-- Modal Editar Subárea --}}
                    <div class="modal fade" id="modalEditarSubArea-{{ $subarea->id }}" tabindex="-1"
                        aria-hidden="true">
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
                                    <form action="{{ route('subarea.update', $subarea->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="form_type" value="edit">
                                        <input type="hidden" name="subarea_id" value="{{ $subarea->id }}">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Nombre de la Subárea</label>
                                            <input type="text" name="nombre" class="form-control"
                                                value="{{ old('nombre', $subarea->nombre) }}" required>

                                            <label class="form-label fw-bold mt-3">Especialidad</label>
                                            <select name="id_especialidad" class="form-select" required>
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

                    {{-- Modal Eliminar Subárea --}}
                        <div class="modal fade" id="modalEliminarSubarea-{{ $subarea->id }}" tabindex="-1" aria-labelledby="modalSubareaEliminarLabel-{{ $subarea->id }}" 
                        aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content custom-modal">
                                    <div class="modal-body text-center">
                                        <div class="icon-container">
                                            <div class="circle-icon">
                                            <i class="bi bi-exclamation-circle"></i>
                                            </div>
                                        </div>
                                        <p class="modal-text">¿Desea Eliminar la Subárea?</p>
                                        <div class="btn-group-custom">
                                            <form action="{{ route('subarea.destroy', ['subarea' => $subarea->id]) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-custom {{ $subarea->condicion == 1 }}">Sí</button>
                                                <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
                <form action="{{ route('subarea.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_type" value="create">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Subárea</label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>

                        <label class="form-label fw-bold mt-3">Especialidad</label>
                        <select name="id_especialidad" class="form-select" required>
                            <option value="">Seleccione una Especialidad</option>
                            @foreach ($especialidades as $especialidad)
                            <option value="{{ $especialidad->id }}" {{ old('id_especialidad') == $especialidad->id ? 'selected' : '' }}>{{ $especialidad->nombre }}</option>
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
// Búsqueda en tiempo real + limpiar
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
        window.location.href = '{{ route("subarea.index") }}';
    });
}

// Mantener modal abierto si hay errores
@if ($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    const formType = '{{ old("form_type") }}';
    const subareaId = '{{ old("subarea_id") }}';

    if (formType === 'create') {
        var modal = new bootstrap.Modal(document.getElementById('modalAgregarSubArea'));
        modal.show();
    } else if (formType === 'edit' && subareaId) {
        var modal = new bootstrap.Modal(document.getElementById('modalEditarSubArea-' + subareaId));
        modal.show();
    }
});
@endif
</script>
@endsection