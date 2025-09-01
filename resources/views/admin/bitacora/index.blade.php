@extends('Template-administrador')

@section('title', 'Bitácoras')

@section('content')
<div class="container mt-4">
    <h1 class="text-center mb-4">Bitácoras</h1>
   {{-- Búsqueda + botón agregar --}}
        <div class="search-bar-wrapper mb-4">
            <div class="search-bar">
                <form id="busquedaForm" method="GET" action="{{ route('recinto.index') }}" class="w-100 position-relative">
                    <span class="search-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control"
                        placeholder="Buscar recinto..." name="busquedaRecinto"
                        value="{{ request('busquedaRecinto') }}" id="inputBusqueda" autocomplete="off">
                    @if(request('busquedaRecinto'))
                  
                    @endif
                </form>
            </div>
        </div>
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
</div>




<script>


const inputBusqueda = document.getElementById('inputBusqueda');
const bitacorasList = document.getElementById('bitacoras-list');
const btnLimpiar = document.getElementById('limpiarBusqueda');


if (inputBusqueda && bitacorasList) {
    inputBusqueda.addEventListener('input', function() {
        const valor = inputBusqueda.value.trim().toLowerCase();
        const items = bitacorasList.querySelectorAll('.bitacora-item');
        items.forEach(function(item) {
            const nombre = item.getAttribute('data-nombre');
            if (!valor || nombre.includes(valor)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
}


if (btnLimpiar && inputBusqueda && bitacorasList) {
    btnLimpiar.addEventListener('click', function() {
        inputBusqueda.value = '';
        const items = bitacorasList.querySelectorAll('.bitacora-item');
        items.forEach(function(item) {
            item.style.display = '';
        });
    });
}
</script>
@endsection
