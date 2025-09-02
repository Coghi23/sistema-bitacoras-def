@extends('Template-profesor')

@section('title', 'Bitácoras')

@section('content')
<div class="container mt-4">
    <h1 class="text-center mb-4">Bitácoras</h1>
    
    {{-- Búsqueda + botones de filtro --}}
    <div class="search-bar-wrapper mb-4">
        <div class="search-bar">
            <form id="busquedaForm" method="GET" action="{{ route('bitacora.index') }}" class="w-100 position-relative">
                <span class="search-icon">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control" placeholder="Buscar bitácora..." name="busquedaBitacora" value="{{ request('busquedaBitacora') }}" id="inputBusqueda" autocomplete="off">
                @if(request('busquedaBitacora'))
                <button type="button" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" id="limpiarBusqueda" title="Limpiar búsqueda" style="background: transparent;">
                    <i class="bi bi-x-circle"></i>
                </button>
                @endif
                @if(request('inactivas'))
                    <input type="hidden" name="inactivas" value="1">
                @endif
            </form>
        </div>
        
        {{-- Botones de filtro por estado --}}
        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('bitacora.index', ['inactivas' => '1']) }}" 
               class="btn {{ request('inactivas') ? 'btn-warning' : 'btn-outline-warning' }}">
                Mostrar inactivos
            </a>
            <a href="{{ route('bitacora.index') }}" 
               class="btn {{ !request('inactivas') ? 'btn-primary' : 'btn-outline-primary' }}">
                Mostrar activos
            </a>
        </div>
    </div>
    
    {{-- Estadísticas de bitácoras --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-journal-text text-primary" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ $bitacoras->count() }}</h3>
                    <p class="text-muted">Total Bitácoras {{ request('inactivas') ? 'Inactivas' : 'Activas' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ isset($todasLasBitacoras) ? $todasLasBitacoras->where('condicion', 1)->count() : 0 }}</h3>
                    <p class="text-muted">Activas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 2rem;"></i>
                    <h3 class="mt-2">{{ isset($todasLasBitacoras) ? $todasLasBitacoras->where('condicion', 0)->count() : 0 }}</h3>
                    <p class="text-muted">Inactivas</p>
                </div>
            </div>
        </div>
    </div>
    
    @if(isset($bitacoras) && count($bitacoras) > 0)
        @foreach ($bitacoras as $bitacora)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <h2 class="h5 mb-0">Bitácora - {{ $bitacora->recinto->nombre ?? 'Sin Recinto Asociado' }}</h2>
                        <span class="badge {{ $bitacora->condicion == 1 ? 'bg-success' : 'bg-secondary' }}">
                            {{ $bitacora->condicion == 1 ? 'Activa' : 'Inactiva' }}
                        </span>
                    </div>
                    <div>
                        <a href="{{ route('evento.create', ['id_bitacora' => $bitacora->id]) }}" class="btn btn-success btn-sm me-2">
                            <i class="bi bi-plus-circle"></i> Agregar evento
                        </a>
                        @if ($bitacora->evento && $bitacora->evento->isNotEmpty())
                            <button class="btn btn-outline-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#eventos-{{ $bitacora->id }}" aria-expanded="false" aria-controls="eventos-{{ $bitacora->id }}">
                                <i class="bi bi-eye"></i> Ver eventos ({{ $bitacora->evento->count() }})
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @if ($bitacora->evento && $bitacora->evento->isEmpty())
                        <p class="text-muted">No hay eventos registrados para esta bitácora.</p>
                    @elseif ($bitacora->evento && $bitacora->evento->isNotEmpty())
                        <div class="collapse" id="eventos-{{ $bitacora->id }}">
                            <h6 class="mb-3"><i class="bi bi-calendar-event"></i> Eventos registrados:</h6>
                            <div class="row">
                                @foreach ($bitacora->evento as $evento)
                                    <div class="col-md-6 mb-3">
                                        <div class="border rounded p-3">
                                            <div class="mb-2">
                                                <strong><i class="bi bi-calendar3"></i> Fecha:</strong> 
                                                <span class="text-primary">{{ $evento->fecha }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong><i class="bi bi-chat-text"></i> Observación:</strong> 
                                                <span>{{ $evento->observacion }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <strong><i class="bi bi-exclamation-triangle"></i> Prioridad:</strong> 
                                                <span class="badge bg-{{ $evento->prioridad == 'alta' ? 'danger' : ($evento->prioridad == 'media' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($evento->prioridad) }}
                                                </span>
                                            </div>
                                            <div>
                                                <strong><i class="bi bi-info-circle"></i> Estado:</strong> 
                                                <span class="badge bg-info">{{ $evento->estado }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-muted">No hay eventos registrados para esta bitácora.</p>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info">
            <h4>No hay bitácoras disponibles</h4>
            <p>No se han encontrado bitácoras de los recintos asignados a sus horarios.</p>
            <p><small class="text-muted">Solo puede ver bitácoras de los recintos donde tiene clases programadas.</small></p>
        </div>
    @endif
</div>

<script>
const inputBusqueda = document.getElementById('inputBusqueda');
const btnLimpiar = document.getElementById('limpiarBusqueda');

if (btnLimpiar && inputBusqueda) {
    btnLimpiar.addEventListener('click', function() {
        // Limpiar búsqueda y mantener el filtro de estado actual
        const url = new URL(window.location);
        url.searchParams.delete('busquedaBitacora');
        window.location.href = url.toString();
    });
}
</script>
@endsection
