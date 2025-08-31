@extends('Template-administrador')

@section('title', 'Sistema de Bitácoras')

@section('content')


<div class="wrapper">
    <div class="main-content">
        {{-- Búsqueda + botón agregar --}}
        <div class="search-bar-wrapper mb-4">
            <div class="search-bar">
                <form id="busquedaForm" method="GET" action="{{ route('estadoRecinto.index') }}" class="w-100 position-relative">
                    <span class="search-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control"
                        placeholder="Buscar estado recinto..." name="busquedaEstadoRecinto" 
                        value="{{ request('busquedaEstadoRecinto') }}" id="inputBusqueda" autocomplete="off">
                    @if(request('busquedaEstadoRecinto'))
                    <button type="button" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" id="limpiarBusqueda" title="Limpiar búsqueda" style="background: transparent;">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    @endif
                </form>
            </div>
            @can('create_estado_recinto')
                <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
                    data-bs-toggle="modal" data-bs-target="#modalAgregarEstadoRecinto"
                    title="Agregar Estado de Recinto" style="background-color: #134496; font-size: 1.2rem; @if(Auth::user() && Auth::user()->hasRole('director')) display: none; @endif">
                    Agregar <i class="bi bi-plus-circle ms-2"></i>
                </button>
            @endcan
        </div>

        <div class="mb-3">
            <a href="{{ route('estadoRecinto.index', ['inactivos' => 1]) }}" class="btn btn-warning mb-3">
                Mostrar inactivos
            </a>
            <a href="{{ route('estadoRecinto.index', ['activos' => 1]) }}" class="btn btn-primary mb-3">
                Mostrar activos
            </a>
        </div>


        {{-- Indicador de resultados de búsqueda --}}
        @if(request('busquedaEstadoRecinto'))
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <span>
                    Mostrando {{ $estadoRecintos->count() }} resultado(s) para "<strong>{{ request('busquedaEstadoRecinto') }}</strong>"
                    <a href="{{ route('estadoRecinto.index') }}" class="btn btn-sm btn-outline-primary ms-2">Ver todas</a>
                </span>
            </div>
        @endif

        <!-- Modal Crear estado recinto -->
         {{-- Mostrar errores de validación --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
    <div class="modal fade" id="modalAgregarEstadoRecinto" tabindex="-1" aria-labelledby="modalAgregarEstadoRecintoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h5 class="modal-title">Crear Nuevo Estado de Recinto</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <form action="{{ route('estadoRecinto.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nombreEstadoRecinto" class="form-label fw-bold">Nombre del Estado de Recinto</label>
                                <input type="text" name="nombre" id="nombreEstadoRecinto" class="form-control" placeholder="Ingrese el Nombre del Estado de Recinto" required>
                            </div>
                            <div class="mb-3">
                                <label for="colorEstadoRecinto" class="form-label fw-bold">Color a asignar al estado de recinto</label>
                                <input type="color"value="#ffffff" name="color" id="colorEstadoRecinto" class="form-control"  required>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-crear">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

       
        <!-- Modal Editar Estado de Recinto-->
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 60%;">Nombre del Estado de Recinto</th>
                        <th class="text-center" style="width: 30%;">Color</th>
                        <th class="text-center" style="width: 10%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($estadoRecintos as $estadoRecinto)

                        @php
                            $mostrarActivos = !request('inactivos');
                            $mostrarInactivos = request('inactivos');
                        @endphp
                        @can('view_estado_recinto')
                            <tr>
                                @if (($mostrarActivos && $estadoRecinto->condicion == 1) || ($mostrarInactivos && $estadoRecinto->condicion == 0))
                                    <td class="text-center">{{ $estadoRecinto->nombre }}</td>
                                    <td class="text-center">
                                        <span class="badge" style="background-color: {{ $estadoRecinto->color }};">
                                            {{ $estadoRecinto->color }}
                                        </span>
                                    <td class="text-center">
                                        @if(Auth::user() && !Auth::user()->hasRole('director'))
                                            @if($mostrarActivos && $estadoRecinto->condicion == 1)
                                                @can('edit_estado_recinto')
                                                    <button type="button" class="btn btn-link text-info p-0 me-2 btn-editar"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalEditarEstadoRecinto-{{ $estadoRecinto->id }}">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                @endcan
                                                @can('delete_estado_recinto')
                                                    <button type="button" class="btn btn-link text-info p-0" data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $estadoRecinto->id }}" aria-label="Eliminar Estado de Recinto">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @endcan
                                            @elseif($mostrarInactivos && $estadoRecinto->condicion == 0)
                                                @can('delete_estado_recinto')
                                                    <button type="button" class="btn btn-link text-success p-0" data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $estadoRecinto->id }}" aria-label="Restaurar Estado de Recinto">
                                                        <i class="bi bi-arrow-counterclockwise"></i>
                                                    </button>
                                                @endcan
                                            @endif
                                        @else
                                            <span class="text-muted">Solo Vista</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endcan
                        <!-- Modal editar Estado de Recinto -->
                        <div class="modal fade" id="modalEditarEstadoRecinto-{{ $estadoRecinto->id }}" tabindex="-1" aria-labelledby="modalEditarEstadoRecintoLabel-{{ $estadoRecinto->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-custom">
                                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                                            <i class="bi bi-arrow-left"></i>
                                        </button>
                                        <h5 class="modal-title">Editar Estado de Recinto</h5>
                                    </div>
                                    <div class="modal-body px-4 py-4">
                                        <form action="{{ route('estadoRecinto.update',['estadoRecinto'=>$estadoRecinto]) }}" method="post">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="id" id="editarIdEstadoRecinto">
                                            <div class="mb-3">
                                                <label for="editarNombreEstadoRecinto" class="form-label fw-bold">Nombre del Estado de Recinto</label>
                                                <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre',$estadoRecinto->nombre)}}">
                                            </div>
                                            <div class="mb-3">
                                                <label for="colorEstadoRecinto" class="form-label fw-bold">Color a asignar al estado de recinto</label>
                                                <input type="color" value="{{old('nombre',$estadoRecinto->color)}}" name="color" id="colorEstadoRecinto" class="form-control" required>
                                            </div>
                                            <div class="text-center mt-4">
                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <!-- Modal eliminar -->
                        <div class="modal fade" id="modalConfirmacionEliminar-{{ $estadoRecinto->id }}" tabindex="-1" aria-labelledby="modalEstadoRecintoEliminarLabel-{{ $estadoRecinto->id }}" 
                        aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content custom-modal">
                                    <div class="modal-body text-center">
                                        <div class="icon-container">
                                            <div class="circle-icon">
                                            <i class="bi bi-exclamation-circle"></i>
                                            </div>
                                        </div>
                                        <p class="modal-text">
                                            @if($estadoRecinto->condicion == 1)
                                                ¿Desea Eliminar el Estado de Recinto?
                                            @else
                                                ¿Desea Restaurar el Estado de Recinto?
                                            @endif
                                        </p>
                                        <div class="btn-group-custom">
                                            <form action="{{ route('estadoRecinto.destroy', ['estadoRecinto' => $estadoRecinto->id]) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-custom {{ $estadoRecinto->condicion == 1 }}">Sí</button>
                                                <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                            </form>
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
                                    <p class="mb-0">Estado de recinto eliminado con éxito</p>
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
            window.location.href = '{{ route("estadoRecinto.index") }}';
        });
    }
</script>
<script>
       $(document).ready(function() {
         $('#colorSelector').colorpicker();
       });
       </script>
@endpush
