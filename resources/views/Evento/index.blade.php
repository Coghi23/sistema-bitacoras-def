@extends('Template-profesor')

@section('title', 'Registrar Evento')

@section('content')
<div class="container my-5">
    <h2 class="mb-4 text-primary">Registrar Evento</h2>

    {{-- Mostrar errores de validación --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('evento.store') }}" id="eventoForm">
        @csrf

        {{-- Campos ocultos --}}
        <input type="hidden" name="id_bitacora" id="id_bitacora">
        <input type="hidden" name="id_seccion" id="id_seccion">
        <input type="hidden" name="id_subarea" id="id_subarea">
        <input type="hidden" name="id_horario" id="id_horario">

        {{-- Docente --}}
        <div class="mb-3">
            <label class="form-label">Docente:</label>
            <input class="form-control" disabled value="{{ Auth::user()->name }}">
        </div>

        {{-- Selección de Horario / Lección --}}
        <div class="mb-3">
            <label class="form-label">Lección / Horario:</label>
            <select name="leccion" id="leccionSelect" class="form-select" required>
                <option value="">Seleccione un horario</option>
                @foreach($horarios as $horario)
                    <option value="{{ $horario->id }}"
                        data-recinto="{{ optional($horario->recinto)->nombre ?? '' }}"
                        data-recinto-id="{{ optional($horario->recinto)->id ?? '' }}"
                        data-seccion="{{ optional($horario->seccion)->nombre ?? '' }}"
                        data-seccion-id="{{ optional($horario->seccion)->id ?? '' }}"
                        data-subarea="{{ optional($horario->subarea)->nombre ?? '' }}"
                        data-subarea-id="{{ optional($horario->subarea)->id ?? '' }}">
                        {{ $horario->fecha }} - {{ optional($horario->recinto)->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Campos dinámicos --}}
        <div class="row mb-3 d-none" id="camposDinamicos">
            <div class="col-md-4">
                <label class="form-label">Recinto:</label>
                <input type="text" class="form-control" id="recintoInput" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Sección:</label>
                <input type="text" class="form-control" id="seccionInput" readonly>
            </div>
            <div class="col-md-4">
                <label class="form-label">Subárea:</label>
                <input type="text" class="form-control" id="subareaInput" readonly>
            </div>
        </div>

        {{-- Observaciones --}}
        <div class="mb-3">
            <label class="form-label">Observaciones:</label>
            <textarea class="form-control" name="observacion" rows="4" required></textarea>
        </div>

        {{-- Prioridad --}}
        <div class="mb-3">
            <label class="form-label">Prioridad:</label>
            <select name="prioridad" class="form-select" required>
                <option value="">Seleccione una prioridad</option>
                <option value="alta">Alta</option>
                <option value="media">Media</option>
                <option value="regular">Regular</option>
                <option value="baja">Baja</option>
            </select>
        </div>

        {{-- Botones --}}
        <div class="d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-secondary" onclick="limpiarFormulario()">Cancelar</button>
            <button type="submit" class="btn btn-primary">Enviar Evento</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
const bitacoras = @json($bitacoras);

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('leccionSelect').addEventListener('change', mostrarCampos);
});

function mostrarCampos() {
    const select = document.getElementById('leccionSelect').selectedOptions[0];
    if (!select || !select.value) return limpiarCampos();

    document.getElementById('camposDinamicos').classList.remove('d-none');
    document.getElementById('recintoInput').value = select.getAttribute('data-recinto');
    document.getElementById('seccionInput').value = select.getAttribute('data-seccion');
    document.getElementById('subareaInput').value = select.getAttribute('data-subarea');

    // Actualizar campos ocultos
    document.getElementById('id_horario').value = select.value;
    document.getElementById('id_bitacora').value = bitacoras.find(b => b.recinto_id == select.getAttribute('data-recinto-id'))?.id || '';
    document.getElementById('id_seccion').value = select.getAttribute('data-seccion-id') || '';
    document.getElementById('id_subarea').value = select.getAttribute('data-subarea-id') || '';
}

function limpiarCampos() {
    document.getElementById('camposDinamicos').classList.add('d-none');
    document.getElementById('id_bitacora').value = '';
    document.getElementById('id_seccion').value = '';
    document.getElementById('id_subarea').value = '';
    document.getElementById('id_horario').value = '';
}

function limpiarFormulario() {
    document.getElementById('eventoForm').reset();
    limpiarCampos();
}
</script>
@endpush
