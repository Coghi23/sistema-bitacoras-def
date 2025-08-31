@extends('Template-profesor')

@section('title', 'Prueba Bitácoras')

@section('content')
<div class="container mt-4">
    <h1>PRUEBA - Vista del Profesor</h1>
    <p>Usuario: {{ Auth::user()->name }}</p>
    <p>Horarios disponibles: {{ count($horarios ?? []) }}</p>
    <p>Recinto: {{ $recinto ?? 'Vacío' }}</p>
    <p>Sección: {{ $seccion ?? 'Vacío' }}</p>
    <p>Subárea: {{ $subarea ?? 'Vacío' }}</p>
    
    @if(isset($horarios) && count($horarios) > 0)
        <h3>Lista de Horarios:</h3>
        <ul>
        @foreach($horarios as $horario)
            <li>
                Horario ID: {{ $horario->id }} - 
                Recinto: {{ optional($horario->recinto)->nombre ?? 'Sin recinto' }} - 
                Sección: {{ optional($horario->seccion)->nombre ?? 'Sin sección' }}
            </li>
        @endforeach
        </ul>
    @else
        <p>No hay horarios disponibles</p>
    @endif
</div>
@endsection
