@extends('Template-administrador')

@section('title', 'Gestión de Recintos')

@section('content')
<div class="wrapper">
    <div class="main-content">
        <div class="row mb-5 mt-4">
            <div class="col-8 col-sm-8 col-md-8 text-center mt-3">
                <div class="position-relative search-box shadow-sm ">
                    <i class="bi bi-search"></i>
                    <input aria-label="Buscar recintos" type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar" class="form-control search-input"/>
                </div>
            </div>
            <div class="col-4 col-sm-4 col-md-2 text-center mt-3">
                <div class="dropdown">
                    <button aria-label="Filtros" class="btn dropdown-toggle border border-dark rounded-5" type="button" style="width: 100%;" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter"></i>
                        Filtros
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="">Disponible</a></li>
                        <li><a class="dropdown-item" href="">En mantenimiento</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-2 text-center mt-3">
                <button aria-label="Agregar recinto" class="btn rounded-pill text-white"
                  data-bs-toggle="modal" 
                  data-bs-target="#modalAgregarRecinto" 
                  type="button" 
                  style="width: 100%; background-color: #134496">
                  Agregar
                  <i class="fas fa-plus-circle"></i>
                </button>
            </div>
            
        </div>
        <div class="container">
            <div class="row align-items-center filter-tabs rounded-3 mb-4 altura-lg altura-md altura-sm" id="filterTabs">
                <div class="tab-indicator"></div>
                <div class="col-12 col-sm-6 col-md-3 text-center rounded-4 btn-tabs">
                    <a href="{{ route('recinto.index') }}"> <!-- Todos -->
                         <button class="btn btn-lightrounded tab-btn {{ request('tipo') ? '' : 'active' }}" type="button" style="width: 100%;">Todos</button>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-md-3 text-center rounded-4 btn-tabs">
                    <a href="{{ route('recinto.index', ['tipo' => 'laboratorio']) }}">
                        <button class="btn tab-btn {{ request('tipo') == 'laboratorio' ? 'active' : '' }}" type="button" style="width: 100%;">
                            <i class="fas fa-desktop"></i>
                            Laboratorios
                        </button>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-md-3 text-center rounded-4 btn-tabs">
                    <a href="{{ route('recinto.index', ['tipo' => 'taller']) }}">
                        <button class="btn tab-btn {{ request('tipo') == 'taller' ? 'active' : '' }}" type="button" style="width: 100%;">
                            <i class="fas fa-wrench"></i>
                            Talleres
                        </button>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-md-3 text-center rounded-4 btn-tabs">
                    <a href="{{ route('recinto.index', ['tipo' => 'movil']) }}">
                        <button class="btn tab-btn {{ request('tipo') == 'movil' ? 'active' : '' }}" type="button" style="width: 100%;">
                            <i class="fas fa-laptop"></i>
                            Laboratorios móviles
                        </button>
                    </a>
                </div>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
            @foreach($recintos as $recinto)
                @if ($recinto->condicion == 1)
                    <div class="col d-flex">
                        <div class="card flex-fill h-100 border rounded-4 p-2" style="font-size: 0.92em; min-width: 0;">
                            <div class="card-body pb-2 p-2">
                                <div class="d-flex align-items-center mb-2 gap-2 flex-wrap">
                                    <span class="badge bg-light text-dark border border-secondary d-flex align-items-center gap-1 px-2 py-1 rounded-pill" style="font-size:0.9em;">
                                        {{ ucfirst($recinto->tipo) }}
                                    </span>
                                    @if($recinto->estado == 'disponible')
                                        <span class="badge bg-success text-white px-2 py-1 rounded-pill" style="font-size:0.9em;">Disponible</span>
                                    @else
                                        <span class="badge bg-danger text-white px-2 py-1 rounded-pill" style="font-size:0.9em;">Mantenimiento</span>
                                    @endif
                                </div>
                                <h5 class="card-title fw-bold mb-2" style="font-size:1em;">{{ $recinto->nombre }}</h5>
                                <div class="mb-1 text-secondary" style="font-size:0.93em;">
                                    <i class="fas fa-building me-1"></i>Institución: {{ $recinto->institucion->nombre }}
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 pt-0 d-flex flex-row justify-content-end align-items-stretch gap-2 p-2">
                                <button class="btn btn-outline-secondary btn-sm rounded-5 d-flex align-items-center justify-content-center ms-0 ms-sm-2"
                                        data-bs-toggle="modal" data-bs-target="#modalEditarRecinto-{{ $recinto->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('recinto.destroy', $recinto->id) }}" method="POST" onsubmit="return confirm('¿Desea eliminar este recinto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-5 ms-2">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                @endif
          

            <!-- Modal Editar Recinto -->
            
            <div class="modal fade" id="modalEditarRecinto-{{ $recinto->id }}" tabindex="-1" aria-labelledby="modalEditarRecintoLabel-{{ $recinto->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-0" id="modalEditarRecintoContent">
                <div class="modal-header rounded-0 custom-header">
                    <button type="button" class="btn p-0 me-3" data-bs-dismiss="modal" aria-label="Volver" style="color: #FFD600; font-size: 1.5rem; background: none; border: none;">
                    <span class="icono-atras">
                        <i><img width="40" height="40" src="https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png" alt="external-ui-basic-ui-solid-adri-ansyah-26"/></i>
                    </span>
                    </button>
                    <h3 class="flex-grow-1">Editar recinto</h3>
                </div>
                <div class="modal-body pb-0" style="border-bottom: 8px solid #003366;">
                    <form id="formEditarRecinto-{{ $recinto->id }}" action="{{ route('recinto.update', $recinto->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="tipoRecinto-{{ $recinto->id }}" class="form-label mb-1">Tipo</label>
                        <select class="form-select" id="tipoRecinto-{{ $recinto->id }}" name="tipo" required>
                        <option value="">Seleccione un tipo</option>
                        <option value="laboratorio" {{ $recinto->tipo == 'laboratorio' ? 'selected' : '' }}>Laboratorio</option>
                        <option value="taller" {{ $recinto->tipo == 'taller' ? 'selected' : '' }}>Taller</option>
                        <option value="movil" {{ $recinto->tipo == 'movil' ? 'selected' : '' }}>Laboratorio móvil</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nombreRecinto-{{ $recinto->id }}" class="form-label mb-1">Nombre</label>
                        <input type="text" class="form-control" id="nombreRecinto-{{ $recinto->id }}" name="nombre" value="{{ $recinto->nombre }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="estadoRecinto-{{ $recinto->id }}" class="form-label mb-1">Estado</label>
                        <select class="form-select" id="estadoRecinto-{{ $recinto->id }}" name="estado" required>
                        <option value="">Seleccione un estado</option>
                        <option value="disponible" {{ $recinto->estado == 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="mantenimiento" {{ $recinto->estado == 'mantenimiento' ? 'selected' : '' }}>En mantenimiento</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="institucionRecinto-{{ $recinto->id }}" class="form-label mb-1">Institución</label>
                        <select data-size="4" title="Seleccione una institución" data-live-search="true" name="institucion_id" id="editarInstitucion" class="form-control selectpicker show-tick">
                            @if(isset($instituciones))
                                @foreach ($instituciones as $institucion)
                                    <option value="{{$institucion->id}}" 
                                        {{ (isset($especialidad) && $especialidad->id_institucion == $institucion->id) || old('id_institucion') == $institucion->id ? 'selected' : '' }}>
                                        {{$institucion->nombre}}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4 mb-2">
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
                    <button type="button" class="btn p-0 me-3" data-bs-dismiss="modal" aria-label="Volver" style="color: #FFD600; font-size: 1.5rem; background: none; border: none;">
                    <span class="icono-atras">
                        <i><img width="40" height="40" src="https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png" alt="external-ui-basic-ui-solid-adri-ansyah-26"/></i>
                    </span>
                    </button>
                    <h3 class="flex-grow-1">Crear nuevo recinto</h3>
                </div>
                <div class="modal-body pb-0" style="border-bottom: 8px solid #003366;">
                    <form action="{{ route('recinto.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nombreRecinto" class="form-label mb-1">Nombre</label>
                            <input type="text" class="form-control" id="nombreRecinto" name="nombre" placeholder="Nombre del recinto" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipoRecinto" class="form-label mb-1">Tipo</label>
                            <select class="form-select" id="tipoRecinto" name="tipo" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="laboratorio">Laboratorio</option>
                            <option value="taller">Taller</option>
                            <option value="movil">Laboratorio móvil</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="estadoRecinto" class="form-label mb-1">Estado</label>
                            <select class="form-select" id="estadoRecinto" name="estado" required>
                            <option value="">Seleccione un estado</option>
                            <option value="disponible">Activo</option>
                            <option value="mantenimiento">En mantenimiento</option>
                            </select>
                        </div>
                        <div class="mb-3" id="profesorField" style="display:none;">
                            <label for="profesorRecinto" class="form-label mb-1">Profesor</label>
                            <input type="text" class="form-control" id="profesorRecinto" name="profesor" placeholder="Nombre del profesor">
                        </div>
                        <div class="mb-3">
                            <label for="institucionRecinto" class="form-label mb-1">Institución</label>
                            
                            <select data-size="4" title="Seleccione una institución" data-live-search="true" name="institucion_id" id="institucion_id" class="form-control selectpicker show-tick" required>
                                <option value="">Seleccione una institución</option>
                                @foreach ($instituciones as $institucion)
                                    <option value="{{$institucion->id}}" {{ old('institucion_id') == $institucion->id ? 'selected' : '' }}>{{$institucion->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4 mb-2">
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
</div>
@endsection

