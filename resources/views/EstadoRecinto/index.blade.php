@extends('Template-administrador')

@section('title', 'Sistema de Bitácoras')

@section('content')


<div class="wrapper">
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="input-group w-50">
                <span class="input-group-text bg-white border-white">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" class="form-control border-start-0 shadow-sm" style="border-radius: 20px;" placeholder="Buscar estado recinto..." />
            </div>
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center" 
                data-bs-toggle="modal" data-bs-target="#modalAgregarEstadoRecinto" 
                title="Agregar Estado de Recinto" style="background-color: #134496; font-size: 1.2rem;">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
        </div>

        <!-- Modal Crear Estado de Recinto -->
        <div class="modal fade" id="modalAgregarEstadoRecinto" tabindex="-1" aria-labelledby="modalAgregarEstadoRecintoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h5 class="modal-title">Crear Estado de Recinto</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <form action="{{ route('estadoRecinto.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nombreEstadoRecinto" class="form-label fw-bold">Nombre del Estado de Recinto</label>
                                <input type="text" name="nombre" id="nombreEstadoRecinto" class="form-control" placeholder="Ingrese el nombre del Estado de Recinto" required>
                            </div>
                            <div class="mb-3">
                                <label for="colorEstadoRecinto" class="form-label fw-bold">Código para asignar en estado recinto</label>
                                <input type="text" name="color" id="colorEstadoRecinto" class="form-control" placeholder="Ingrese el hexadecimal del Estado de Recinto" required>
                            </div>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-crear">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal Editar Estado de Recinto -->
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 80%;">Nombre del Estado de Recinto</th>
                        <th class="text-center" style="width: 10%;">Color</th>
                        <th class="text-center" style="width: 10%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($estadoRecintos as $estadoRecinto)
                        <tr>
                            @if ($estadoRecinto->condicion == 1)
                                <td class="text-center">{{ $estadoRecinto->nombre }}</td>
                                <td class="text-center">
                                    @if($estadoRecinto->color)
                                        <span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:{{ $estadoRecinto->color }};border:1px solid #ccc;"></span>
                                        <span class="ms-2">{{ $estadoRecinto->color }}</span>
                                    @else
                                        <span class="text-muted">Sin color</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link text-info p-0 me-2 btn-editar"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarEstadoRecinto-{{ $estadoRecinto->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-link text-info p-0" data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $estadoRecinto->id }}" aria-label="Eliminar Estado de Recinto">
                                            <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            @endif
                            
                        </tr>

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
                                        <div class="card text-bg-light">
                                        <form action="{{ route('estadoRecinto.update',['estadoRecinto'=>$estadoRecinto]) }}" method="post">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="id" id="editarIdEstadoRecinto">
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="editarNombreEstadoRecinto" class="form-label fw-bold">Nombre del Estado de Recinto</label>
                                                    <input type="text" name="nombre" id="nombre" class="form-control" value="{{old('nombre',$estadoRecinto->nombre)}}">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="editarColorEstadoRecinto" class="form-label fw-bold">Código de color (hexadecimal)</label>
                                                    <input type="text" name="color" id="editarColorEstadoRecinto" class="form-control" placeholder="#RRGGBB" value="{{ old('color', $estadoRecinto->color) }}">
                                                </div>
                                            </div>
                                            <div class="card-footer text-center">
                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                            </div>
                                        </form>
                                        </div>
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
                                        <p class="modal-text">¿Desea eliminar el estado de recinto?</p>
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
                                    <p class="mb-0">Llave eliminada con éxito</p>
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


@endpush