@extends('Template-profesor')

@section('title', 'Bitácoras')

@section('content')
<div class="container mt-4">
    <h1 class="text-center mb-4">Bitácoras</h1>
    
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
        <div class="alert alert-info">
            <h4>No hay bitácoras disponibles</h4>
            <p>No se han encontrado bitácoras de los recintos asignados a sus horarios.</p>
            <p><small class="text-muted">Solo puede ver bitácoras de los recintos donde tiene clases programadas.</small></p>
        </div>
    @endif
</div>
@endsection
