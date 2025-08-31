@extends('Template-administrador')

@section('title', 'Sistema de Bitácoras')

@section('content')


<div class="wrapper">
    <div class="main-content">
        {{-- Búsqueda + botón agregar --}}
        <div class="search-bar-wrapper mb-4">
            <div class="search-bar">
                <form id="busquedaForm" method="GET" action="{{ route('tipoRecinto.index') }}" class="w-100 position-relative">
                    <span class="search-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control"
                        placeholder="Buscar tipo de recinto..." name="busquedaTipoRecinto" 
                        value="{{ request('busquedaTipoRecinto') }}" id="inputBusqueda" autocomplete="off">
                    @if(request('busquedaTipoRecinto'))
                    <button type="button" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" id="limpiarBusqueda" title="Limpiar búsqueda" style="background: transparent;">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    @endif
                </form>
            </div>
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
                data-bs-toggle="modal" data-bs-target="#modalAgregarTipoRecinto"
                title="Agregar Tipo de Recinto" style="background-color: #134496; font-size: 1.2rem; @if(Auth::user() && Auth::user()->hasRole('director')) display: none; @endif">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
        </div>

        {{-- Indicador de resultados de búsqueda --}}
        @if(request('busquedaTipoRecinto'))
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <span>
                    Mostrando {{ $tipoRecintos->count() }} resultado(s) para "<strong>{{ request('busquedaTipoRecinto') }}</strong>"
                    <a href="{{ route('tipoRecinto.index') }}" class="btn btn-sm btn-outline-primary ms-2">Ver todas</a>
                </span>
            </div>
        @endif

        <!-- Modal Crear Tipo de Recinto -->
        <div class="modal fade" id="modalAgregarTipoRecinto" tabindex="-1" aria-labelledby="modalAgregarTipoRecintoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h5 class="modal-title">Crear Nuevo Tipo de Recinto</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <form action="{{ route('tipoRecinto.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nombreTipoRecinto" class="form-label fw-bold">Nombre del Tipo de Recinto</label>
                                <input type="text" name="nombre" id="nombreTipoRecinto" class="form-control" placeholder="Ingrese el Nombre del Tipo de Recinto" required>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-crear">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

       
        <!-- Modal Editar Tipo de Recinto -->
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 90%;">Nombre del Tipo de Recinto</th>
                        <th class="text-center" style="width: 10%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tipoRecintos as $tipoRecinto)
                        @if ($tipoRecinto->condicion == 1)
                            <tr>
                                <td class="text-center">{{ $tipoRecinto->nombre }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link text-info p-0 me-2 btn-editar"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarTipoRecinto-{{ $tipoRecinto->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-link text-info p-0" data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $tipoRecinto->id }}" aria-label="Eliminar Tipo de Recinto">
                                            <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Editar -->
                            <div class="modal fade" id="modalEditarTipoRecinto-{{ $tipoRecinto->id }}" tabindex="-1" aria-labelledby="modalEditarTipoRecintoLabel-{{ $tipoRecinto->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <h5 class="modal-title">Editar Tipo de Recinto</h5>
            </div>
            <div class="modal-body px-4 py-4">
                <form action="{{ route('tipoRecinto.update',['tipoRecinto'=>$tipoRecinto]) }}" method="post">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="id" id="editarIdTipoRecinto">
                    <div class="mb-3">
                        <label for="editarNombreTipoRecinto" class="form-label fw-bold">Nombre del Tipo de Recinto</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre',$tipoRecinto->nombre)}}">
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
                            <div class="modal fade" id="modalConfirmacionEliminar-{{ $tipoRecinto->id }}" tabindex="-1" aria-labelledby="modalTipoRecintoEliminarLabel-{{ $tipoRecinto->id }}" 
                            aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content custom-modal">
                                        <div class="modal-body text-center">
                                            <div class="icon-container">
                                                <div class="circle-icon">
                                                <i class="bi bi-exclamation-circle"></i>
                                                </div>
                                            </div>
                                            <p class="modal-text">¿Desea Eliminar el Tipo de Recinto?</p>
                                            <div class="btn-group-custom">
                                                <form action="{{ route('tipoRecinto.destroy', ['tipoRecinto' => $tipoRecinto->id]) }}" method="post">
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
                                        <p class="mb-0">Tipo de recinto eliminado con éxito</p>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div> 
    </div>
</div>

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
            }, 500);
        });
        
        inputBusqueda.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                formBusqueda.submit();
            }
        });
    }
    
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function() {
            inputBusqueda.value = '';
            window.location.href = '{{ route("tipoRecinto.index") }}';
        });
    }
</script>

@endsection

@push('scripts')


@endpush
