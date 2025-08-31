{{-- filepath: resources/views/Evento/create.blade.php --}}
{{-- Vista de listado y creación de reportes de eventos para bitácoras --}}
@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Título principal de la página --}}
    <h2>Crear Reporte de Evento</h2>

    {{-- Muestra errores de validación si existen --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Formulario para crear un nuevo evento asociado a una bitácora --}}
    <form action="{{ route('evento.store') }}" method="POST">
        @csrf

        {{-- Selector de bitácora (recinto) --}}
        <div class="mb-3">
            <label for="id_bitacora" class="form-label">Bitácora (Recinto)</label>
            <select name="id_bitacora" id="id_bitacora" class="form-control" required>
                <option value="">Seleccione una bitácora</option>
                @foreach($bitacoras as $bitacora)
                    <option value="{{ $bitacora->id }}">
                        {{ $bitacora->recinto->nombre ?? 'Sin recinto' }} - Bitácora #{{ $bitacora->id }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Selector de sección --}}
        <div class="mb-3">
            <label for="id_seccion" class="form-label">Sección</label>
            <select name="id_seccion" id="id_seccion" class="form-control" required>
                <option value="">Seleccione una sección</option>
                @foreach($seccione as $seccion)
                    <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                @endforeach
            </select>
        </div>

        {{-- Selector de subárea --}}
        <div class="mb-3">
            <label for="id_subarea" class="form-label">Subárea</label>
            <select name="id_subarea" id="id_subarea" class="form-control" required>
                <option value="">Seleccione una subárea</option>
                @foreach($subareas as $subarea)
                    <option value="{{ $subarea->id }}">{{ $subarea->nombre }}</option>
                @endforeach
            </select>
        </div>

        {{-- Selector de horario --}}
        <div class="mb-3">
            <label for="id_horario" class="form-label">Horario</label>
            <select name="id_horario" id="id_horario" class="form-control" required>
                <option value="">Seleccione un horario</option>
                @foreach($horarios as $horario)
                    <option value="{{ $horario->id }}">
                        {{ $horario->fecha }} - {{ $horario->recinto->nombre ?? '' }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Campo para la hora de envío del evento --}}
        <div class="mb-3">
            <label for="hora_envio" class="form-label">Hora de Envío</label>
            <input type="time" name="hora_envio" id="hora_envio" class="form-control" required>
        </div>

        {{-- Campo para observaciones adicionales --}}
        <div class="mb-3">
            <label for="observacion" class="form-label">Observación</label>
            <textarea name="observacion" id="observacion" class="form-control" rows="3"></textarea>
        </div>

        {{-- Selector de prioridad del evento --}}
        <div class="mb-3">
            <label for="prioridad" class="form-label">Prioridad</label>
            <select name="prioridad" id="prioridad" class="form-control">
                <option value="">Seleccione prioridad</option>
                <option value="alta">Alta</option>
                <option value="media">Media</option>
                <option value="regular">Regular</option>
                <option value="baja">Baja</option>
            </select>
        </div>

        {{-- Botón para guardar el evento --}}
        <button type="submit" class="btn btn-primary">Guardar Evento</button>
    </form>
</div>
@endsection