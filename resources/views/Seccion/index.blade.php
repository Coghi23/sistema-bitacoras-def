@extends('Template-administrador')

@section('title', 'Registro de Sección')

@section('content')
<div class="wrapper">
    <div class="main-content">

        {{-- Búsqueda + botón agregar --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="input-group w-50">
                <span class="input-group-text bg-white border-white">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" class="form-control border-start-0 shadow-sm"
                    placeholder="Buscar..." style="border-radius: 20px;">
            </div>
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center"
                data-bs-toggle="modal" data-bs-target="#modalAgregarSeccion"
                title="Agregar Sección" style="background-color: #134496; font-size: 1.2rem;">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
        </div>

        {{-- Tabla --}}
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th class="text-center">Sección</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($secciones as $seccion)
                    <tr>
                        @if ($seccion->condicion == 1)
                            <td class="text-center">{{ $seccion->nombre }}</td>
                            <td class="text-center">
                                <button class="btn btn-link text-info p-0 me-2" data-bs-toggle="modal"
                                    data-bs-target="#modalEditarSeccion-{{ $seccion->id }}">
                                    <i class="bi bi-pencil" style="font-size: 1.5rem;"></i>
                                </button>
                                <button class="btn btn-link text-danger p-0" data-bs-toggle="modal"
                                    data-bs-target="#modalEliminarSeccion-{{ $seccion->id }}">
                                    <i class="bi bi-trash" style="font-size: 1.5rem;"></i>
                                </button>
                            </td>
                        @endif
                    </tr>

                    {{-- Modal Editar --}}
                    <div class="modal fade" id="modalEditarSeccion-{{ $seccion->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header modal-header-custom">
                                    <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                                        <i class="bi bi-arrow-left"></i>
                                    </button>
                                    <h5 class="modal-title">Registro de sección</h5>
                                </div>
                                <div class="modal-body px-4 py-4">
                                    <form action="{{ route('seccion.update', $seccion->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Sección</label>
                                            <input type="text" name="nombre" class="form-control"
                                                value="{{ old('nombre', $seccion->nombre) }}" required>

                                        </div>
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary">Modificar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Eliminar --}}
                        <div class="modal fade" id="modalEliminarSeccion-{{ $seccion->id }}" tabindex="-1" aria-labelledby="modalSeccionEliminarLabel-{{ $seccion->id }}" 
                        aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content custom-modal">
                                    <div class="modal-body text-center">
                                        <div class="icon-container">
                                            <div class="circle-icon">
                                            <i class="bi bi-exclamation-circle"></i>
                                            </div>
                                        </div>
                                        <p class="modal-text">¿Desea eliminar la sección?</p>
                                        <div class="btn-group-custom">
                                            <form action="{{ route('seccion.destroy', ['seccion' => $seccion->id]) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-custom {{ $seccion->condicion == 1 }}">Sí</button>
                                                <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                            </form>
                                        </div>
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

{{-- Modal Crear Sección --}}
<div class="modal fade" id="modalAgregarSeccion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <h5 class="modal-title">Registro de sección</h5>
            </div>
            <div class="modal-body px-4 py-4">
                <form action="{{ route('seccion.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sección</label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


