@extends('Template-profesor')

@section('title', 'Bitácoras - Debug')

@section('content')
<div class="container mt-4">
    <h1>DEBUG - Vista del Profesor</h1>
    <div class="alert alert-info">
        <h4>Información de Debug:</h4>
        <p><strong>Usuario:</strong> {{ Auth::user()->name }}</p>
        <p><strong>Profesor ID:</strong> {{ $profesor->id ?? 'No definido' }}</p>
        <p><strong>Cantidad de Bitácoras:</strong> {{ isset($bitacoras) ? count($bitacoras) : 'Variable bitacoras no definida' }}</p>
        
        @if(isset($bitacoras) && count($bitacoras) > 0)
            <p><strong>Primera Bitácora:</strong></p>
            <ul>
                @foreach($bitacoras->take(3) as $bitacora)
                    <li>
                        ID: {{ $bitacora->id }} - 
                        Recinto: {{ $bitacora->recinto->nombre ?? 'Sin recinto' }} - 
                        Eventos: {{ $bitacora->evento ? count($bitacora->evento) : 0 }}
                    </li>
                @endforeach
            </ul>
        @else
            <p><strong>No hay bitácoras disponibles</strong></p>
        @endif
    </div>
    
    <hr>
    
    <h3>Vista Normal (si hay datos):</h3>
    @if(isset($bitacoras) && count($bitacoras) > 0)
        @foreach ($bitacoras as $bitacora)
            <div class="card mb-4">
                <div class="card-header">
                    <h2 class="h5">Bitácora - {{ $bitacora->recinto->nombre ?? 'Sin Recinto Asociado' }}</h2>
                </div>
                <div class="card-body">
                    @if ($bitacora->evento && $bitacora->evento->isEmpty())
                        <p>No hay eventos registrados para esta bitácora.</p>
                    @elseif ($bitacora->evento)
                        <ul class="list-group">
                            @foreach ($bitacora->evento as $evento)
                                <li class="list-group-item">
                                    <strong>Fecha:</strong> {{ $evento->fecha }}<br>
                                    <strong>Observación:</strong> {{ $evento->observacion }}<br>
                                    <strong>Prioridad:</strong> {{ $evento->prioridad }}<br>
                                     <strong>Estado:</strong> {{ $evento->estado }}<br>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No hay eventos registrados para esta bitácora.</p>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-warning">
            <p>No se encontraron bitácoras para mostrar.</p>
        </div>
    @endif
</div>
@endsection
