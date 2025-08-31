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
            @can('create_tipo_recinto')
                <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
                    data-bs-toggle="modal" data-bs-target="#modalAgregarTipoRecinto"
                    title="Agregar Tipo de Recinto" style="background-color: #134496; font-size: 1.2rem; @if(Auth::user() && Auth::user()->hasRole('director')) display: none; @endif">
                    Agregar <i class="bi bi-plus-circle ms-2"></i>
                </button>
            @endcan
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

        <div class="table-responsive">
            {{-- Botones para mostrar/ocultar tipos de recinto inactivos --}}
            <a href="{{ route('tipoRecinto.index', ['inactivos' => 1]) }}" class="btn btn-warning mb-3">
                Mostrar inactivos
            </a>
            <a href="{{ route('tipoRecinto.index') }}" class="btn btn-primary mb-3">
                Mostrar activos
            </a>
            <table class="table table-striped">
                <thead>
                    <tr class="header-row">
                        <th class="text-center">Nombre del Tipo de Recinto</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $mostrarInactivos = request('inactivos') == 1;
                    @endphp
                    @forelse($tipoRecintos as $tipoRecinto)
                        @if(($mostrarInactivos && $tipoRecinto->condicion == 0) || (!$mostrarInactivos && $tipoRecinto->condicion == 1))
                        <tr class="record-row">
                            <td class="text-center">{{ $tipoRecinto->nombre }}</td>
                            <td class="text-center">
                                <span class="badge {{ isset($tipoRecinto->condicion) && $tipoRecinto->condicion ? 'bg-success' : 'bg-danger' }}">
                                    {{ isset($tipoRecinto->condicion) && $tipoRecinto->condicion ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="text-center">
                                @can('edit_tipo_recinto')
                                    <button type="button" class="btn p-0 me-2" data-bs-toggle="modal" data-bs-target="#modalEditarTipoRecinto-{{ $tipoRecinto->id }}" title="Editar tipo de recinto">
                                        <i class="bi bi-pencil icon-editar"></i>
                                    </button>
                                @endcan
                                @can('delete_tipo_recinto')
                                    @if($tipoRecinto->condicion == 1)
                                        <button class="btn p-0 me-2" data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $tipoRecinto->id }}" title="Desactivar tipo de recinto">
                                            <i class="bi bi-trash icon-eliminar"></i>
                                        </button>
                                    @else
                                        <button class="btn p-0 me-2" data-bs-toggle="modal" data-bs-target="#modalReactivarTipoRecinto-{{ $tipoRecinto->id }}" title="Activar tipo de recinto">
                                            <i class="bi bi-arrow-counterclockwise icon-eliminar"></i>
                                        </button>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                        {{-- Modal Editar --}}
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
                        {{-- Modal Eliminar --}}
                        <div class="modal fade" id="modalConfirmacionEliminar-{{ $tipoRecinto->id }}" tabindex="-1" aria-labelledby="modalTipoRecintoEliminarLabel-{{ $tipoRecinto->id }}" aria-hidden="true">
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
                                                <button type="submit" class="btn btn-custom {{ $tipoRecinto->condicion == 1 }}">Sí</button>
                                                <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Modal Reactivar --}}
                        <div class="modal fade" id="modalReactivarTipoRecinto-{{ $tipoRecinto->id }}" tabindex="-1" aria-labelledby="modalReactivarTipoRecintoLabel-{{ $tipoRecinto->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content custom-modal">
                                    <div class="modal-body text-center">
                                        <div class="icon-container">
                                            <div class="circle-icon" style="background-color: #28a745; color: #fff;">
                                                <i class="bi bi-arrow-counterclockwise" style="color: #fff;"></i>
                                            </div>
                                        </div>
                                        <p class="modal-text">¿Desea reactivar el Tipo de Recinto?</p>
                                        <div class="btn-group-custom">
                                            <form action="{{ route('tipoRecinto.destroy', ['tipoRecinto' => $tipoRecinto->id]) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-custom" style="background-color: #28a745; color: #fff;">Sí</button>
                                                <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                    <tr class="record-row">
                        <td class="text-center" colspan="3">No hay tipos de recinto registrados.</td>
                    </tr>
                    @endforelse
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




