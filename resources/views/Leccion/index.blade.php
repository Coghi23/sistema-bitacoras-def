@extends('Template-administrador')

@section('title', 'Registro de Lección')

@section('content')
<div class="wrapper">
    <div class="main-content">
        {{-- Encabezado de búsqueda y botón Agregar --}}
        <div class="search-bar-wrapper mb-4">
            <div class="search-bar">
                <form id="busquedaForm" method="GET" action="{{ route('leccion.index') }}" class="w-100 position-relative">
                    <span class="search-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Buscar lección..."
                        name="busquedaLeccion"
                        value="{{ request('busquedaLeccion') }}"
                        id="inputBusqueda"
                        autocomplete="off"
                    >
                    @if(request('busquedaLeccion'))
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
            @if(Auth::user() && !Auth::user()->hasRole('director'))
                <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
                    data-bs-toggle="modal" data-bs-target="#modalAgregarLeccion"
                    title="Agregar Lección" style="background-color: #134496; font-size: 1.2rem;">
                    Agregar <i class="bi bi-plus-circle ms-2"></i>
                </button>
            @endif
        </div>
        {{-- Indicador de resultados de búsqueda --}}
        @if(request('busquedaLeccion'))
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <span>
                    Mostrando {{ $lecciones->count() }} resultado(s) para "<strong>{{ request('busquedaLeccion') }}</strong>"
                    <a href="{{ route('leccion.index') }}" class="btn btn-sm btn-outline-primary ms-2">Ver todas</a>
                </span>
            </div>
        @endif
        {{-- Tabla de Lecciones --}}
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th class="text-center">Nombre</th>
                        <th class="text-center">Hora de inicio</th>
                        <th class="text-center">Hora final</th>
                        <th class="text-center">Tipo de Lección</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lecciones as $leccion)
                        @if ($leccion->condicion == 1)
                        <tr>
                            <td class="text-center">{{ $leccion->leccion }}</td>
                            <td class="text-center">{{ $leccion->hora_inicio }}</td>
                            <td class="text-center">{{ $leccion->hora_final }}</td>
                            <td class="text-center">{{ $leccion->tipoLeccion ? $leccion->tipoLeccion->nombre : 'Sin tipo' }}</td>
                            <td class="text-center">
                                @if(Auth::user() && !Auth::user()->hasRole('director'))
                                <button class="btn btn-link text-info p-0 me-2" data-bs-toggle="modal"
                                    data-bs-target="#modalEditarLeccion-{{ $leccion->id }}">
                                    <i class="bi bi-pencil" style="font-size: 1.5rem;"></i>
                                </button>
                                <button class="btn btn-link text-danger p-0" data-bs-toggle="modal"
                                    data-bs-target="#modalEliminarLeccion-{{ $leccion->id }}">
                                    <i class="bi bi-trash" style="font-size: 1.5rem;"></i>
                                </button>
                                @else
                                <span class="text-muted">Solo vista</span>
                                @endif
                            </td>
                        </tr>
                        @endif
                        {{-- Modal Editar Lección --}}
                        @if(Auth::user() && !Auth::user()->hasRole('director'))
                        <div class="modal fade" id="modalEditarLeccion-{{ $leccion->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-custom">
                                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                                            <i class="bi bi-arrow-left"></i>
                                        </button>
                                        <h5 class="modal-title">Editar Lección</h5>
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
                                        <form action="{{ route('leccion.update', $leccion->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="form_type" value="edit">
                                            <input type="hidden" name="leccion_id" value="{{ $leccion->id }}">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nombre de la Lección</label>
                                                <input type="text" name="leccion" class="form-control"
                                                    value="{{ old('leccion', $leccion->leccion) }}" required>
                                                <label class="form-label fw-bold mt-3">Tipo de Lección</label>
                                                <select name="idTipoLeccion" class="form-select" required>
                                                    @foreach ($tipoLecciones as $tipoLeccion)
                                                    <option value="{{ $tipoLeccion->id }}"
                                                        {{ $leccion->idTipoLeccion == $tipoLeccion->id ? 'selected' : '' }}>
                                                        {{ $tipoLeccion->nombre }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <label class="form-label fw-bold mt-3">Hora de inicio</label>
                                                <input type="time" name="hora_inicio" class="form-control" value="{{ old('hora_inicio', $leccion->hora_inicio ?? '') }}" required>
                                                <label class="form-label fw-bold mt-3">Hora final</label>
                                                <input type="time" name="hora_final" class="form-control" value="{{ old('hora_final', $leccion->hora_final ?? '') }}" required>
                                            </div>
                                            <div class="text-center mt-4">
                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Modal Eliminar Lección --}}
                        <div class="modal fade" id="modalEliminarLeccion-{{ $leccion->id }}" tabindex="-1" aria-labelledby="modalLeccionEliminarLabel-{{ $leccion->id }}" 
                        aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content custom-modal">
                                    <div class="modal-body text-center">
                                        <div class="icon-container">
                                            <div class="circle-icon">
                                            <i class="bi bi-exclamation-circle"></i>
                                            </div>
                                        </div>
                                        <p class="modal-text">¿Desea Eliminar la Lección?</p>
                                        <div class="btn-group-custom">
                                            <form action="{{ route('leccion.destroy', ['leccion' => $leccion->id]) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-custom {{ $leccion->condicion == 1 }}">Sí</button>
                                                <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
{{-- Modal Crear Lección --}}
<div class="modal fade" id="modalAgregarLeccion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <h5 class="modal-title">Crear Nueva Lección</h5>
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
                <form action="{{ route('leccion.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_type" value="create">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Lección</label>
                        <input type="text" name="leccion" class="form-control" value="{{ old('leccion') }}" required>
                        <label class="form-label fw-bold mt-3">Tipo de Lección</label>
                        <select name="idTipoLeccion" class="form-select" required>
                            <option value="">Seleccione un Tipo de Lección</option>
                            @foreach ($tipoLecciones as $tipoLeccion)
                            <option value="{{ $tipoLeccion->id }}" {{ old('idTipoLeccion') == $tipoLeccion->id ? 'selected' : '' }}>{{ $tipoLeccion->nombre }}</option>
                            @endforeach
                        </select>
                        <label class="form-label fw-bold mt-3">Hora de inicio</label>
                        <input type="time" name="hora_inicio" class="form-control" value="{{ old('hora_inicio') }}" required>
                        <label class="form-label fw-bold mt-3">Hora final</label>
                        <input type="time" name="hora_final" class="form-control" value="{{ old('hora_final') }}" required>
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
        window.location.href = '{{ route("leccion.index") }}';
    });
}

// Mantener modal abierto si hay errores
@if ($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    const formType = '{{ old("form_type") }}';
    const leccionId = '{{ old("leccion_id") }}';

    if (formType === 'create') {
        var modal = new bootstrap.Modal(document.getElementById('modalAgregarLeccion'));
        modal.show();
    } else if (formType === 'edit' && leccionId) {
        var modal = new bootstrap.Modal(document.getElementById('modalEditarLeccion-' + leccionId));
        modal.show();
    }
});
@endif
</script>
@endsection
