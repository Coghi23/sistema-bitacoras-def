@extends('Template-administrador')

@section('title', 'Gestión de Recintos')

@section('content')
<style>
/* Responsive adjustments for Recintos */
@media (max-width: 768px) {
    .main-content {
        padding: 0.5rem !important;
    }
    
    .search-bar-wrapper {
        flex-direction: column !important;
        gap: 0.75rem;
    }
    
    .search-bar {
        width: 100% !important;
    }
    
    .btn-agregar {
        width: 100% !important;
        justify-content: center !important;
        margin-left: 0 !important;
        font-size: 1rem !important;
    }
    
    .card {
        margin-bottom: 1rem;
    }
    
    .card-footer {
        justify-content: center !important;
        flex-wrap: wrap;
    }
    
    .modal-dialog {
        margin: 0.5rem !important;
        max-width: calc(100% - 1rem) !important;
    }
    
    .btn-group-custom {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .btn-group-custom .btn {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .card-title {
        font-size: 0.9rem !important;
    }
    
    .badge {
        font-size: 0.75rem !important;
    }
    
    .text-secondary {
        font-size: 0.8rem !important;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>

<div id="recintos-container" class="wrapper">
    <div id="main-content" class="main-content">
        {{-- Header con búsqueda y botón agregar --}}
        <div id="header-section" class="row align-items-end mb-4">
            <div id="search-wrapper" class="search-bar-wrapper mb-4 d-flex align-items-center">
                <div id="search-bar-container" class="search-bar flex-grow-1">
                    <form id="busquedaForm" method="GET" action="{{ route('recinto.index') }}" class="w-100 position-relative">
                        <span class="search-icon">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="inputBusqueda" class="form-control" placeholder="Buscar recinto..." 
                               name="busquedaRecinto" value="{{ request('busquedaRecinto') }}" autocomplete="off">
                        @if(request('busquedaRecinto'))
                        <button type="button" id="limpiarBusqueda" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" 
                                title="Limpiar búsqueda" style="background: transparent;">
                            <i class="bi bi-x-circle"></i>
                        </button>
                        @endif
                        @if(request('inactivos'))
                            <input type="hidden" name="inactivos" value="1">
                        @endif
                    </form>
                </div>
                @can('create_recintos')
                    <button id="btn-agregar-recinto" class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
                        data-bs-toggle="modal" data-bs-target="#modalAgregarRecinto"
                        title="Agregar Recinto" style="background-color: #134496; font-size: 1.2rem; @if(Auth::user() && Auth::user()->hasRole('director')) display: none; @endif">
                        Agregar <i class="bi bi-plus-circle ms-2"></i>
                    </button>
                @endcan
            </div>
        </div>

        {{-- Alertas de estado --}}
        <div id="alerts-section">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        {{-- Botones de filtros --}}
        <div id="filter-buttons" class="mb-3">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('recinto.index', ['inactivos' => 1]) }}" class="btn btn-warning">
                    Mostrar inactivos
                </a>
                <a href="{{ route('recinto.index') }}" class="btn btn-primary">
                    Mostrar activos
                </a>
            </div>
        </div>
     
        {{-- Grid de recintos --}}
        <div id="recintos-grid" class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        @foreach($recintos as $recinto)
            @if ($recinto->condicion == 1)
                @if(!request('tipo') || $recinto->tipo == request('tipo'))
                    <div class="col d-flex recinto-item" data-nombre="{{ strtolower($recinto->nombre) }}">
                        <div id="recinto-card-{{ $recinto->id }}" class="card flex-fill h-100 border rounded-4 p-2" style="font-size: 0.92em; min-width: 0;">
                            <div class="card-body pb-2 p-2">
                                <div class="d-flex align-items-center mb-2 gap-2 flex-wrap">
                                    <span class="badge bg-light text-dark border border-secondary d-flex align-items-center gap-1 px-2 py-1 rounded-pill" style="font-size:0.9em;">
                                        {{ ucfirst($recinto->tipo) }}
                                    </span>
                                    <span class="badge px-2 py-1 rounded-pill text-dark"
                                            style="font-size:0.9em; background-color: {{ $recinto->estadoRecinto ? $recinto->estadoRecinto->color : '#ccc' }};">
                                            {{ $recinto->estadoRecinto ? $recinto->estadoRecinto->nombre : 'Sin estado' }}
                                    </span>
                                </div>
                                <h5 class="card-title fw-bold mb-2" style="font-size:1em;">{{ $recinto->nombre }}</h5>
                                <div class="mb-1 text-secondary" style="font-size:0.93em;">
                                    <i class="fas fa-key me-1"></i>Número de llave: {{ $recinto->llave->nombre}}
                                </div>
                                <div class="mb-1 text-secondary" style="font-size:0.93em;">
                                    <i class="fas fa-building me-1"></i>Institución: {{ $recinto->institucion->nombre }}
                                </div>
                                <div class="mb-1 text-secondary" style="font-size:0.93em;">
                                    <i class="fas fa-building me-1"></i>Tipo: {{ $recinto->tipoRecinto ? $recinto->tipoRecinto->nombre : 'Sin tipo' }}                                
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 d-flex flex-row justify-content-end align-items-stretch gap-2 p-2">
                                <button id="btn-edit-{{ $recinto->id }}" class="btn btn-outline-secondary btn-sm rounded-5 d-flex align-items-center justify-content-center ms-0 ms-sm-2"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarRecinto-{{ $recinto->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('recinto.destroy', $recinto->id) }}" method="POST" >
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" id="btn-delete-{{ $recinto->id }}" class="btn btn-outline-danger btn-sm rounded-5 ms-2" 
                                            data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $recinto->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="col d-flex recinto-item" data-nombre="{{ strtolower($recinto->nombre) }}">
                    <div id="recinto-card-inactive-{{ $recinto->id }}" class="card flex-fill h-100 border rounded-4 p-2" style="font-size: 0.92em; min-width: 0;">
                        <div class="card-body pb-2 p-2">
                            <div class="d-flex align-items-center mb-2 gap-2 flex-wrap">
                                <span class="badge bg-light text-dark border border-secondary d-flex align-items-center gap-1 px-2 py-1 rounded-pill" style="font-size:0.9em;">
                                    {{ ucfirst($recinto->tipo) }}
                                </span>
                                <span class="badge px-2 py-1 rounded-pill text-dark"
                                        style="font-size:0.9em; background-color: {{ $recinto->estadoRecinto ? $recinto->estadoRecinto->color : '#ccc' }};">
                                        {{ $recinto->estadoRecinto ? $recinto->estadoRecinto->nombre : 'Sin estado' }}
                                </span>
                            </div>
                            <h5 class="card-title fw-bold mb-2" style="font-size:1em;">{{ $recinto->nombre }}</h5>
                            <div class="mb-1 text-secondary" style="font-size:0.93em;">
                                <i class="fas fa-key me-1"></i>Número de llave: {{ $recinto->llave->nombre}}
                            </div>
                            <div class="mb-1 text-secondary" style="font-size:0.93em;">
                                <i class="fas fa-building me-1"></i>Institución: {{ $recinto->institucion->nombre }}
                            </div>
                            <div class="mb-1 text-secondary" style="font-size:0.93em;">
                                <i class="fas fa-building me-1"></i>Tipo: {{ $recinto->tipoRecinto ? $recinto->tipoRecinto->nombre : 'Sin tipo' }}                                
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 pt-0 d-flex flex-row justify-content-end align-items-stretch gap-2 p-2">
                            <form action="{{ route('recinto.destroy', $recinto->id) }}" method="POST" >
                                @csrf
                                @method('DELETE')
                                <button type="button" id="btn-reactivate-{{ $recinto->id }}" class="btn btn-outline-danger btn-sm rounded-5 ms-2" 
                                        data-bs-toggle="modal" data-bs-target="#modalConfirmacionReactivar-{{ $recinto->id }}">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Modal de eliminacion de recinto --}}
            <div class="modal fade" id="modalConfirmacionEliminar-{{ $recinto->id }}" tabindex="-1" 
                 aria-labelledby="modalRecintoEliminarLabel-{{ $recinto->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content custom-modal">
                        <div class="modal-body text-center">
                            <div class="icon-container">
                                <div class="circle-icon">
                                    <i class="bi bi-exclamation-circle"></i>
                                </div>
                            </div>
                            <p class="modal-text">¿Desea Eliminar el Recinto?</p>
                            <div class="btn-group-custom d-flex justify-content-center gap-2">
                                <form action="{{ route('recinto.destroy', ['recinto' => $recinto->id]) }}" method="post">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-custom">Sí</button>
                                    <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal de reactivacion de recinto --}}
            <div class="modal fade" id="modalConfirmacionReactivar-{{ $recinto->id }}" tabindex="-1" 
                 aria-labelledby="modalRecintoReactivarLabel-{{ $recinto->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content custom-modal">
                        <div class="modal-body text-center">
                            <div class="icon-container">
                                <div class="circle-icon">
                                    <i class="bi bi-exclamation-circle"></i>
                                </div>
                            </div>
                            <p class="modal-text">¿Desea reactivar el Recinto?</p>
                            <div class="btn-group-custom d-flex justify-content-center gap-2">
                                <form action="{{ route('recinto.destroy', ['recinto' => $recinto->id]) }}" method="post">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-custom">Sí</button>
                                    <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Editar Recinto -->
            <div class="modal fade" id="modalEditarRecinto-{{ $recinto->id }}" tabindex="-1" 
                 aria-labelledby="modalEditarRecintoLabel-{{ $recinto->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-0" id="modalEditarRecintoContent-{{ $recinto->id }}">
                        <div class="modal-header rounded-0 custom-header">
                            <button type="button" class="btn p-0 me-3" data-bs-dismiss="modal" aria-label="Volver" 
                                    style="color: #FFD600; font-size: 1.5rem; background: none; border: none;">
                                <span class="icono-atras">
                                    <i><img width="40" height="40" src="https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png" alt="external-ui-basic-ui-solid-adri-ansyah-26"/></i>
                                </span>
                            </button>
                            <h3 class="flex-grow-1">Editar Recinto</h3>
                        </div>
                        <div class="modal-body pb-0" style="border-bottom: 8px solid #003366;">
                            <form id="formEditarRecinto-{{ $recinto->id }}" action="{{ route('recinto.update', $recinto->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                            
                                <div class="mb-3">
                                    <label for="nombreRecinto-{{ $recinto->id }}" class="form-label mb-1">Nombre del Recinto</label>
                                    <input type="text" class="form-control" id="nombreRecinto-{{ $recinto->id }}" 
                                           name="nombre" value="{{ $recinto->nombre }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="tipoRecinto-{{ $recinto->id }}" class="form-label mb-1">Tipo de Recinto</label>
                                    <select data-size="4" title="Seleccione un Tipo de Recinto" data-live-search="true" 
                                            name="tipoRecinto_id" id="tipoRecinto-{{ $recinto->id }}" class="form-control selectpicker show-tick">
                                        @if(isset($tiposRecinto))
                                            @foreach ($tiposRecinto as $tipoRecinto)
                                                <option value="{{$tipoRecinto->id}}"
                                                    {{ (isset($recinto) && $recinto->tipoRecinto_id == $tipoRecinto->id) || old('tipoRecinto_id') == $tipoRecinto->id ? 'selected' : '' }}>
                                                    {{$tipoRecinto->nombre}}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="estadoRecinto-{{ $recinto->id }}" class="form-label mb-1">Estado del Recinto</label>
                                    <select data-size="4" title="Seleccione un Estado de Recinto" data-live-search="true" 
                                            name="estadoRecinto_id" id="editarEstadoRecinto-{{ $recinto->id }}" class="form-control selectpicker show-tick">
                                        @if(isset($estadosRecinto))
                                            @foreach ($estadosRecinto as $estadoRecinto)
                                                <option value="{{$estadoRecinto->id}}"
                                                    {{ (isset($recinto) && $recinto->estadoRecinto_id == $estadoRecinto->id) || old('estadoRecinto_id') == $estadoRecinto->id ? 'selected' : '' }}>
                                                    {{$estadoRecinto->nombre}}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="llaveRecinto-{{ $recinto->id }}" class="form-label mb-1">Número de Llave</label>
                                    <select data-size="4" title="Seleccione una Llave" data-live-search="true" 
                                            name="llave_id" id="llave-{{ $recinto->id }}" class="form-control selectpicker show-tick">
                                        @if(isset($llaves))
                                            @foreach ($llaves as $llave)
                                                <option value="{{$llave->id}}"
                                                    {{ (isset($recinto) && $recinto->llave_id == $llave->id) || old('llave_id') == $llave->id ? 'selected' : '' }}>
                                                    {{$llave->nombre}}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="institucionRecinto-{{ $recinto->id }}" class="form-label mb-1">Institución</label>
                                    <select data-size="4" title="Seleccione una Institución" data-live-search="true" 
                                            name="institucion_id" id="editarInstitucion-{{ $recinto->id }}" class="form-control selectpicker show-tick">
                                        @if(isset($instituciones))
                                            @foreach ($instituciones as $institucion)
                                                <option value="{{$institucion->id}}"
                                                    {{ (isset($recinto) && $recinto->institucion_id == $institucion->id) || old('institucion_id') == $institucion->id ? 'selected' : '' }}>
                                                    {{$institucion->nombre}}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="d-flex flex-column flex-md-row justify-content-end gap-2 mt-4 mb-2">
                                    <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-guardar rounded-pill px-4">Guardar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Modal Agregar Recinto -->
        <div class="modal fade" id="modalAgregarRecinto" tabindex="-1" aria-labelledby="modalAgregarRecintoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-0" id="modalAgregarRecintoContent">
                    <div class="modal-header rounded-0 custom-header">
                        <button type="button" class="btn p-0 me-3" data-bs-dismiss="modal" aria-label="Volver" 
                                style="color: #FFD600; font-size: 1.5rem; background: none; border: none;">
                            <span class="icono-atras">
                                <i><img width="40" height="40" src="https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png" alt="external-ui-basic-ui-solid-adri-ansyah-26"/></i>
                            </span>
                        </button>
                        <h3 class="flex-grow-1">Crear Nuevo Recinto</h3>
                    </div>
                    <div class="modal-body pb-0" style="border-bottom: 8px solid #003366;">
                        <form id="formAgregarRecinto" action="{{ route('recinto.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nombreRecinto" class="form-label mb-1">Nombre del Recinto</label>
                                <input type="text" class="form-control" id="nombreRecinto" name="nombre" placeholder="Nombre del Recinto" required>
                            </div>
                            <div class="mb-3">
                                <label for="tipoRecinto" class="form-label mb-1">Tipo de Recinto</label>
                                <select data-size="4" title="Seleccione un Tipo de Recinto" data-live-search="true" 
                                        name="tipoRecinto_id" id="tipoRecinto_id" class="form-control selectpicker show-tick" required>
                                    <option value="">Seleccione un Tipo de Recinto</option>
                                    @foreach ($tiposRecinto as $tipoRecinto)
                                        <option value="{{$tipoRecinto->id}}" {{ old('tipoRecinto_id') == $tipoRecinto->id ? 'selected' : '' }}>{{$tipoRecinto->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        
                            <div class="mb-3">
                                <label for="estadoRecinto" class="form-label mb-1">Estado del Recinto</label>
                                <select data-size="4" title="Seleccione un Estado de Recinto" data-live-search="true" 
                                        name="estadoRecinto_id" id="estadoRecinto_id" class="form-control selectpicker show-tick" required>
                                    <option value="">Seleccione un Estado de Recinto</option>
                                    @foreach ($estadosRecinto as $estadoRecinto)
                                        <option value="{{$estadoRecinto->id}}" {{ old('estadoRecinto_id') == $estadoRecinto->id ? 'selected' : '' }}>{{$estadoRecinto->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="llaveRecinto" class="form-label mb-1">Número de Llave</label>
                                <select data-size="4" title="Seleccione una Llave" data-live-search="true" 
                                        name="llave_id" id="llave_id" class="form-control selectpicker show-tick" required>
                                    <option value="">Seleccione una Llave</option>
                                    @foreach ($llaves as $llave)
                                        <option value="{{$llave->id}}" {{ old('llave_id') == $llave->id ? 'selected' : '' }}>{{$llave->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="institucionRecinto" class="form-label mb-1">Institución</label>
                                <select data-size="4" title="Seleccione una Institución" data-live-search="true" 
                                        name="institucion_id" id="institucion_id" class="form-control selectpicker show-tick" required>
                                    <option value="">Seleccione una Institución</option>
                                    @foreach ($instituciones as $institucion)
                                        <option value="{{$institucion->id}}" {{ old('institucion_id') == $institucion->id ? 'selected' : '' }}>{{$institucion->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex flex-column flex-md-row justify-content-end gap-2 mt-4 mb-2">
                                <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-guardar rounded-pill px-4">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Devolución de Llave -->
@foreach($recintos as $recinto)
<div class="modal fade" id="modalDevolucionLlave-{{ $recinto->id }}" tabindex="-1" 
     aria-labelledby="modalDevolucionLlaveLabel-{{ $recinto->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-0">
            <div class="modal-header rounded-0 custom-header">
                <button type="button" class="btn p-0 me-3" data-bs-dismiss="modal" aria-label="Volver" 
                        style="color: #FFD600; font-size: 1.5rem; background: none; border: none;">
                    <span class="icono-atras">
                        <i><img width="40" height="40" src="https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png" alt="external-ui-basic-ui-solid-adri-ansyah-26"/></i>
                    </span>
                </button>
                <h3 class="flex-grow-1">Devolución de Llave</h3>
            </div>
            <div class="modal-body pb-0 text-center" style="border-bottom: 8px solid #003366;">
                <div class="mb-4">
                    <h5>{{ $recinto->nombre }}</h5>
                    <p class="text-secondary">Número de Llave: <strong>{{ $recinto->llave->nombre }}</strong></p>
                </div>
            
                <div id="qrCode-{{ $recinto->id }}" class="mb-4" style="display: none;">
                    <div class="d-flex justify-content-center">
                        <div id="qrCodeContainer-{{ $recinto->id }}"></div>
                    </div>
                    <p class="mt-2 text-success">¡Código QR generado exitosamente!</p>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-center gap-2 mt-4 mb-2">
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success rounded-pill px-4" 
                            onclick="generarQRDevolucion({{ $recinto->id }}, '{{ $recinto->llave->nombre }}', '{{ $recinto->nombre }}')">
                        Realizar Devolución
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Búsqueda en tiempo real
    const inputBusqueda = document.getElementById('inputBusqueda');
    const recintosList = document.getElementById('recintos-grid');
    const btnLimpiar = document.getElementById('limpiarBusqueda');

    if (inputBusqueda && recintosList) {
        inputBusqueda.addEventListener('input', function() {
            const valor = inputBusqueda.value.trim().toLowerCase();
            const items = recintosList.querySelectorAll('.recinto-item');
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

    if (btnLimpiar && inputBusqueda && recintosList) {
        btnLimpiar.addEventListener('click', function() {
            inputBusqueda.value = '';
            const items = recintosList.querySelectorAll('.recinto-item');
            items.forEach(function(item) {
                item.style.display = '';
            });
            // Remover el parámetro de la URL
            window.location.href = "{{ route('recinto.index') }}";
        });
    }
});

function generarQRDevolucion(recintoId, numeroLlave, nombreRecinto) {
    const qrContainer = document.getElementById(`qrCodeContainer-${recintoId}`);
    const qrDiv = document.getElementById(`qrCode-${recintoId}`);
    
    // Limpiar contenedor previo
    qrContainer.innerHTML = '';
    
    // Datos para el QR
    const datosDevolucion = {
        tipo: 'devolucion_llave',
        recinto: nombreRecinto,
        llave: numeroLlave,
        fecha: new Date().toISOString(),
        id: recintoId
    };
    
    // Generar QR
    QRCode.toCanvas(JSON.stringify(datosDevolucion), {
        width: 200,
        height: 200,
        margin: 2,
    }, function (error, canvas) {
        if (error) {
            console.error(error);
            alert('Error al generar el código QR');
            return;
        }
        
        qrContainer.appendChild(canvas);
        qrDiv.style.display = 'block';
    });
}
</script>
@endsection


