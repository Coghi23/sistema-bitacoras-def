@extends('Template-profesor')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Crear Nuevo Evento</h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('evento.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="id_bitacora" class="form-label">Bitácora</label>
                            <select name="id_bitacora" id="id_bitacora" class="form-select" required>
                                <option value="">Seleccione una bitácora</option>
                                @foreach($bitacoras as $bitacora)
                                    <option value="{{ $bitacora->id }}">
                                        {{ $bitacora->recinto->nombre ?? 'Sin recinto' }} - Bitácora #{{ $bitacora->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_seccion" class="form-label">Sección</label>
                            <select name="id_seccion" id="id_seccion" class="form-select" required>
                                <option value="">Seleccione una sección</option>
                                @foreach($secciones as $seccion)
                                    <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_subarea" class="form-label">Subárea</label>
                            <select name="id_subarea" id="id_subarea" class="form-select" required>
                                <option value="">Seleccione una subárea</option>
                                @foreach($subareas as $subarea)
                                    <option value="{{ $subarea->id }}">{{ $subarea->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="id_horario" class="form-label">Horario</label>
                            <select name="id_horario" id="id_horario" class="form-select" required>
                                <option value="">Seleccione un horario</option>
                                @foreach($horarios as $horario)
                                    <option value="{{ $horario->id }}">
                                        {{ $horario->fecha }} - {{ $horario->recinto->nombre ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="hora_envio" class="form-label">Hora de Envío</label>
                            <input type="time" name="hora_envio" id="hora_envio" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="prioridad" class="form-label">Prioridad</label>
                            <select name="prioridad" id="prioridad" class="form-select" required>
                                <option value="">Seleccione prioridad</option>
                                <option value="alta">Alta</option>
                                <option value="media">Media</option>
                                <option value="regular">Regular</option>
                                <option value="baja">Baja</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="observacion" class="form-label">Observación</label>
                            <textarea name="observacion" id="observacion" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Guardar Evento</button>
                            <a href="{{ route('evento.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection