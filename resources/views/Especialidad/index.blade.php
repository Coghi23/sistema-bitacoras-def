@extends('Template-administrador')

@section('title', 'Sistema de Bitácoras')

@section('content')


<div class="wrapper">
    <div class="main-content">
        <div class="d-flex justify-content-between align-ins$institucions-center mb-3">
            <div class="input-group w-50">
                <form id="busquedaForm" method="GET" action="{{ route('especialidad.index') }}" class="d-flex w-100">
                    <span class="input-group-text bg-white border-white">
                        <i class="bi bi-search text-secondary"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 shadow-sm" style="border-radius: 20px;" 
                           placeholder="Buscar especialidad o institución..." name="busquedaEspecialidad" 
                           value="{{ request('busquedaEspecialidad') }}" id="inputBusqueda" autocomplete="off" />
                    @if(request('busquedaEspecialidad'))
                    <button type="button" class="btn btn-outline-secondary border-0" id="limpiarBusqueda" title="Limpiar búsqueda">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    @endif
                </form>
            </div>
            
            @if(Auth::user() && !Auth::user()->hasRole('director'))
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center" 
                data-bs-toggle="modal" data-bs-target="#modalAgregarespecialidad" 
                title="Agregar Especialidad" style="background-color: #134496; font-size: 1.2rem;">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
            @endif
        </div>

        {{-- Indicador de resultados de búsqueda --}}
        @if(request('busquedaEspecialidad'))
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <span>
                    Mostrando {{ $especialidades->count() }} resultado(s) para "<strong>{{ request('busquedaEspecialidad') }}</strong>"
                    <a href="{{ route('especialidad.index') }}" class="btn btn-sm btn-outline-primary ms-2">Ver todas</a>
                </span>
            </div>
        @endif

        <!-- Modal Crear Especialidad -->
        <div class="modal fade" id="modalAgregarespecialidad" tabindex="-1" aria-labelledby="modalAgregarespecialidadLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h5 class="modal-title">Crear Especialidad</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <form action="{{ route('especialidad.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nombreespecialidad" class="form-label fw-bold">Nombre de la Especialidad</label>
                                <input type="text" name="nombre" id="nombreespecialidad" class="form-control" value="{{old('nombre')}}" placeholder="Ingrese el nombre de la Especialidad" required>
                                <label for="id_institucion" class="form-label fw-bold mt-3">Institución</label>
                                <select data-size="4" title="Seleccione una institución" data-live-search="true" name="id_institucion" id="id_institucion" class="form-control selectpicker show-tick" required>
                                    <option value="">Seleccione una institución</option>
                                    @foreach ($instituciones as $institucion)
                                        <option value="{{$institucion->id}}" {{ old('id_institucion') == $institucion->id ? 'selected' : '' }}>{{$institucion->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-crear">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

       <div class="table-responsive">
    <table class="table align-middle table-hover">
        <thead>
            <tr>
                <th class="text-center" style="width: 45%;">Nombre de la Especialidad</th>
                <th class="text-center" style="width: 45%;">Institución</th>
                <th class="text-center" style="width: 10%;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($especialidades as $especialidad)
                <tr>
                    @if ($especialidad->condicion == 1)
                        <td class="text-center">{{ $especialidad->nombre }}</td>
                        <td class="text-center">{{ $especialidad->institucion->nombre }}</td>
                        <td class="text-center">
                            @if(Auth::user() && !Auth::user()->hasRole('director'))
                            <button class="btn btn-link text-info p-0 me-2 btn-editar" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalEditarEspecialidad-{{ $especialidad->id }}"
                                data-id="{{ $especialidad->id }}" 
                                data-nombre="{{ $especialidad->nombre }}"
                                data-institucion="{{ $especialidad->institucion->id }}"
                                aria-label="Editar Especialidad">
                                <i class="bi bi-pencil" style="font-size: 1.8rem;"></i>
                            </button>
                            <button type="button" class="btn btn-link text-info p-0" data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $especialidad->id }}" aria-label="Eliminar Especialidad">
                                <i class="bi bi-trash" style="font-size: 1.8rem;"></i>
                            </button>
                            @else
                            <span class="text-muted">Solo vista</span>
                            @endif
                        </td>
                    @endif
                    
                </tr>


               <!-- Modal Editar Especialidad -->
<div class="modal fade" id="modalEditarEspecialidad-{{ $especialidad->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <h5 class="modal-title">Registro de especialidad</h5>
            </div>
            <div class="modal-body px-4 py-4">
                <form action="{{ route('especialidad.update', $especialidad->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Especialidad</label>
                        <input type="text" name="nombre" class="form-control"
                            value="{{ old('nombre', $especialidad->nombre) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Institución</label>
                        <select name="id_institucion" class="form-control" required>
                            @foreach ($instituciones as $institucion)
                                <option value="{{ $institucion->id }}" 
                                    {{ old('id_institucion', $especialidad->id_institucion) == $institucion->id ? 'selected' : '' }}>
                                    {{ $institucion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Modificar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


    <!-- Modal eliminar -->
    <div class="modal fade" id="modalConfirmacionEliminar-{{ $especialidad->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <h5 class="modal-title">Confirmar eliminación</h5>
                </div>
                <div class="modal-body px-4 py-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="mb-3">¿Está seguro que desea eliminar esta especialidad?</h6>
                    <p class="text-muted mb-4">Esta acción no se puede deshacer.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <form action="{{ route('especialidad.destroy', $especialidad->id) }}" method="post" class="d-inline">
                            @method('DELETE')
                            @csrf
                            <button type="submit" class="btn btn-danger">Sí</button>
                        </form>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
                        
                        
                        <!-- Modal Éxito Eliminar -->
                        <div class="modal fade" id="modalExitoEliminar" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content text-center">
                                <div class="modal-body d-flex flex-column align-items-center gap-3 p-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 256 256">
                                    <g fill="#efc737" fill-rule="nonzero">
                                        <g transform="scale(5.12,5.12)">
                                        <path d="M25,2c-12.683,0 -23,10.317 -23,23c0,12.683 10.317,23 23,23c12.683,0 23,-10.317 23,-23c0,-4.56 -1.33972,-8.81067 -3.63672,-12.38867l-1.36914,1.61719c1.895,3.154 3.00586,6.83148 3.00586,10.77148c0,11.579 -9.421,21 -21,21c-11.579,0 -21,-9.421 -21,-21c0,-11.579 9.421,-21 21,-21c5.443,0 10.39391,2.09977 14.12891,5.50977l1.30859,-1.54492c-4.085,-3.705 -9.5025,-5.96484 -15.4375,-5.96484zM43.23633,7.75391l-19.32227,22.80078l-8.13281,-7.58594l-1.36328,1.46289l9.66602,9.01563l20.67969,-24.40039z"/>
                                        </g>
                                    </g>
                                    </svg>
                                    <p class="mb-0">Especialidad eliminada con éxito</p>
                                </div>
                                </div>
                            </div>
                        </div>

            @endforeach
            
        </tbody>
    </table>
</div> 
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Funcionalidad de búsqueda en tiempo real
    let timeoutId;
    const inputBusqueda = document.getElementById('inputBusqueda');
    const formBusqueda = document.getElementById('busquedaForm');
    const btnLimpiar = document.getElementById('limpiarBusqueda');
    
    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(function() {
                formBusqueda.submit();
            }, 500); // Espera 500ms después de que el usuario deje de escribir
        });
        
        // También permitir búsqueda al presionar Enter
        inputBusqueda.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                formBusqueda.submit();
            }
        });
    }
    
    // Funcionalidad del botón limpiar
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function() {
            inputBusqueda.value = '';
            window.location.href = '{{ route("especialidad.index") }}';
        });
    }
</script>
@endpush