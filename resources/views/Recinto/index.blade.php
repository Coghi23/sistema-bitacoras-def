@extends('Template-administrador')

@section('title', 'Gestión de Recintos')

@section('content')
<div class="wrapper">
    <div class="main-content">
        <div class="row mb-5 mt-4">
            <div class="col-8 col-sm-8 col-md-8 text-center mt-3">
                <form method="GET" action="{{ route('recintos.index') }}">
                    <div class="position-relative search-box shadow-sm ">
                        <i class="bi bi-search"></i>
                        <input aria-label="Buscar recintos" type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar" class="form-control search-input"/>
                    </div>
                </form>
            </div>
            <div class="col-4 col-sm-4 col-md-2 text-center mt-3">
                <div class="dropdown">
                    <button aria-label="Filtros" class="btn dropdown-toggle border border-dark rounded-5" type="button" style="width: 100%;" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter"></i>
                        Filtros
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('recintos.index', ['estado' => 'disponible']) }}">Disponible</a></li>
                        <li><a class="dropdown-item" href="{{ route('recintos.index', ['estado' => 'en_uso']) }}">En uso</a></li>
                        <li><a class="dropdown-item" href="{{ route('recintos.index', ['estado' => 'mantenimiento']) }}">En mantenimiento</a></li>
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
                    <a href="{{ route('recintos.index') }}">
                        <button class="btn btn-lightrounded tab-btn {{ request('tipo') ? '' : 'active' }}" type="button" style="width: 100%;">Todos</button>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-md-3 text-center rounded-4 btn-tabs">
                    <a href="{{ route('recintos.index', ['tipo' => 'laboratorio']) }}">
                        <button class="btn tab-btn {{ request('tipo') == 'laboratorio' ? 'active' : '' }}" type="button" style="width: 100%;">
                            <i class="fas fa-desktop"></i>
                            Laboratorios
                        </button>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-md-3 text-center rounded-4 btn-tabs">
                    <a href="{{ route('recintos.index', ['tipo' => 'taller']) }}">
                        <button class="btn tab-btn {{ request('tipo') == 'taller' ? 'active' : '' }}" type="button" style="width: 100%;">
                            <i class="fas fa-wrench"></i>
                            Talleres
                        </button>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-md-3 text-center border rounded-4 btn-tabs ">
                    <a href="{{ route('recintos.index', ['tipo' => 'movil']) }}">
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
            <div class="col d-flex">
                <div class="card flex-fill h-100 border rounded-4 p-2" style="font-size: 0.92em; min-width: 0;">
                    <div class="card-body pb-2 p-2">
                        <div class="d-flex align-items-center mb-2 gap-2 flex-wrap">
                            <span class="badge bg-light text-dark border border-secondary d-flex align-items-center gap-1 px-2 py-1 rounded-pill" style="font-size:0.9em;">
                                {{ ucfirst($recinto->tipo) }}
                            </span>
                            @if($recinto->estado == 'disponible')
                                <span class="badge bg-success text-white px-2 py-1 rounded-pill" style="font-size:0.9em;">Disponible</span>
                            @elseif($recinto->estado == 'en_uso')
                                <span class="badge bg-primary text-white px-2 py-1 rounded-pill" style="font-size:0.9em;">En uso</span>
                            @else
                                <span class="badge bg-danger text-white px-2 py-1 rounded-pill" style="font-size:0.9em;">Mantenimiento</span>
                            @endif
                        </div>
                        <h5 class="card-title fw-bold mb-2" style="font-size:1em;">{{ $recinto->nombre }}</h5>
                        <div class="mb-1 text-secondary" style="font-size:0.93em;">
                            <i class="fas fa-building me-1"></i>Institución: {{ $recinto->institucion }}
                        </div>
                        <div class="mb-3 text-secondary" style="font-size:0.93em;">
                            <i class="fas fa-user me-1"></i>Profesor: {{ $recinto->profesor ?? '' }}
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pt-0 d-flex flex-row justify-content-end align-items-stretch gap-2 p-2">
                        <button class="btn btn-outline-secondary btn-sm rounded-5 px-2 w-100"
                                data-bs-toggle="modal" data-bs-target="#historialModal-{{ $recinto->id }}" style="font-size:0.85em;">
                            Historial de uso
                        </button>
                        <button class="btn btn-outline-secondary btn-sm rounded-5 d-flex align-items-center justify-content-center ms-0 ms-sm-2"
                                data-bs-toggle="modal" data-bs-target="#modalEditarRecinto-{{ $recinto->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('recintos.destroy', $recinto->id) }}" method="POST" onsubmit="return confirm('¿Desea eliminar este recinto?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-5 ms-2">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Historial de Uso -->
            <div class="modal fade" id="historialModal-{{ $recinto->id }}" tabindex="-1" aria-labelledby="historialModalLabel-{{ $recinto->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="min-width: 50px; max-width: 600px;">
                    <div class="modal-content border-0 rounded-0" style="width: 100%; margin: auto;">
                        <div class="modal-header py-3 px-4 rounded-0" style="background: #163374; border-bottom: 4px solid #FFD600;">
                            <button type="button" class="btn p-0 me-3" data-bs-dismiss="modal" aria-label="Volver" style="color: #FFD600; font-size: 1.5rem; background: none; border: none;">
                                <i class="bi bi-arrow-left"></i>
                            </button>
                            <h5 class="modal-title text-white fw-bold flex-grow-1" id="historialModalLabel-{{ $recinto->id }}" style="font-size: 1.35rem;">
                                Historial de uso de {{ $recinto->nombre }}
                            </h5>
                        </div>
                        <div class="modal-body px-4 py-3" style="background: #fff; max-height: 85vh; min-height: 70vh; overflow-y: auto;">
                            <div class="d-flex flex-column gap-3 small">
                                @forelse($recinto->historial as $evento)
                                    <div>
                                        <strong>{{ $evento->fecha }}</strong> - {{ $evento->descripcion }}
                                    </div>
                                @empty
                                    <div>No hay historial disponible.</div>
                                @endforelse
                            </div>
                        </div>
                        <div class="modal-footer rounded-0 justify-content-end py-1"
                             style="background: #ffffff; border-bottom: 8px solid #003366;">
                            <button type="button" class="btn btn-primary rounded-pill px-5 fw-semibold" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

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
                    <form id="formEditarRecinto-{{ $recinto->id }}" action="{{ route('recintos.update', $recinto->id) }}" method="POST">
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
                        <option value="en_uso" {{ $recinto->estado == 'en_uso' ? 'selected' : '' }}>En uso</option>
                        <option value="mantenimiento" {{ $recinto->estado == 'mantenimiento' ? 'selected' : '' }}>En mantenimiento</option>
                        </select>
                    </div>
                    <div class="mb-3" id="profesorField-{{ $recinto->id }}" style="display:{{ $recinto->profesor ? 'block' : 'none' }};">
                        <label for="profesorRecinto-{{ $recinto->id }}" class="form-label mb-1">Profesor</label>
                        <input type="text" class="form-control" id="profesorRecinto-{{ $recinto->id }}" name="profesor" value="{{ $recinto->profesor }}">
                    </div>
                    <div class="mb-3">
                        <label for="institucionRecinto-{{ $recinto->id }}" class="form-label mb-1">Institución</label>
                        <select class="form-select" id="institucionRecinto-{{ $recinto->id }}" name="institucion" required>
                        <option value="">Seleccione una Institución</option>
                        <option value="COVAO Diurno" {{ $recinto->institucion == 'COVAO Diurno' ? 'selected' : '' }}>COVAO Diurno</option>
                        <option value="COVAO Nocturno" {{ $recinto->institucion == 'COVAO Nocturno' ? 'selected' : '' }}>COVAO Nocturno</option>
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
            <script>
            document.getElementById('tipoRecinto-{{ $recinto->id }}').addEventListener('change', function() {
                var profesorField = document.getElementById('profesorField-{{ $recinto->id }}');
                if (this.value === 'laboratorio' || this.value === 'movil' || this.value === 'taller') {
                    profesorField.style.display = 'block';
                } else {
                    profesorField.style.display = 'none';
                }
            });
            </script>
                    </div>
                </div>
            </div>

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
        <form id="formAgregarRecinto" action="{{ route('recintos.store') }}" method="POST">
          @csrf
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
            <label for="nombreRecinto" class="form-label mb-1">Nombre</label>
            <input type="text" class="form-control" id="nombreRecinto" name="nombre" placeholder="Nombre del recinto" required>
          </div>
          <div class="mb-3">
            <label for="estadoRecinto" class="form-label mb-1">Estado</label>
            <select class="form-select" id="estadoRecinto" name="estado" required>
              <option value="">Seleccione un estado</option>
              <option value="disponible">Disponible</option>
              <option value="en_uso">En uso</option>
              <option value="mantenimiento">En mantenimiento</option>
            </select>
          </div>
          <div class="mb-3" id="profesorField" style="display:none;">
            <label for="profesorRecinto" class="form-label mb-1">Profesor</label>
            <input type="text" class="form-control" id="profesorRecinto" name="profesor" placeholder="Nombre del profesor">
          </div>
          <div class="mb-3">
            <label for="institucionRecinto" class="form-label mb-1">Institución</label>
            <select class="form-select" id="institucionRecinto" name="institucion" required>
              <option value="">Seleccione una Institución</option>
              <option value="COVAO Diurno">COVAO Diurno</option>
              <option value="COVAO Nocturno">COVAO Nocturno</option>
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
<script>
document.getElementById('tipoRecinto').addEventListener('change', function() {
    var profesorField = document.getElementById('profesorField');
    if (this.value === 'laboratorio' || this.value === 'movil' || this.value === 'taller') {
        profesorField.style.display = 'block';
    } else {
        profesorField.style.display = 'none';
    }
});
</script>

@endsection