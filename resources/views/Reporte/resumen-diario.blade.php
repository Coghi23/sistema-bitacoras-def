@extends('Template-administrador')

@section('title', 'Resumen Diario de Eventos')

@section('content')
<div class="container-fluid">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Resumen Diario de Eventos</h2>
        <div class="d-flex gap-2">
            <input type="date" class="form-control" id="fecha" value="{{ date('Y-m-d') }}" onchange="cargarEventosPorFecha(this.value)">
        </div>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check fs-1 text-primary mb-2"></i>
                    <h5 class="card-title">Total de Eventos</h5>
                    <h3 class="mb-0" id="totalEventos">{{ $eventos->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle fs-1 text-warning mb-2"></i>
                    <h5 class="card-title">Alta Prioridad</h5>
                    <h3 class="mb-0" id="eventosAlta">{{ $eventos->where('prioridad', 'Alta')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle fs-1 text-success mb-2"></i>
                    <h5 class="card-title">Confirmados</h5>
                    <h3 class="mb-0" id="eventosConfirmados">{{ $eventos->where('confirmacion', true)->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-clock fs-1 text-info mb-2"></i>
                    <h5 class="card-title">Pendientes</h5>
                    <h3 class="mb-0" id="eventosPendientes">{{ $eventos->where('confirmacion', false)->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de eventos -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Hora</th>
                            <th>Recinto</th>
                            <th>Profesor</th>
                            <th>Observación</th>
                            <th>Prioridad</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($eventos as $evento)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($evento->fecha)->format('H:i') }}</td>
                            <td>{{ $evento->bitacora->recinto->nombre }}</td>
                            <td>{{ $evento->usuario->name }}</td>
                            <td>{{ $evento->observacion }}</td>
                            <td>
                                <span class="badge {{ $evento->prioridad == 'Alta' ? 'bg-danger' : ($evento->prioridad == 'Media' ? 'bg-warning' : 'bg-info') }}">
                                    {{ $evento->prioridad }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $evento->confirmacion ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $evento->confirmacion ? 'Confirmado' : 'Pendiente' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .table th {
        background-color: #134496;
        color: white;
    }
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.8em;
    }
</style>
@endpush

@push('scripts')
<script>
function cargarEventosPorFecha(fecha) {
    window.location.href = `{{ route('reporte.resumen-diario') }}?fecha=${fecha}`;
}
</script>
@endpush

@endsection
