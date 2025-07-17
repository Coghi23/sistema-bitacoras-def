@extends('layouts.app')

@section('title', 'Sistema de Bitácoras')

@section('content')
<div id="sidebar-navbar">
    @include('partials.topbar')
    @include('partials.sidebar')
</div>

<div class="wrapper">
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="input-group w-50">
                <span class="input-group-text bg-white border-white">
                    <i class="bi bi-search text-secondary"></i>
                </span>
                <input type="text" class="form-control border-start-0 rounded-pill" placeholder="Buscar">
            </div>
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalAgregarEspecialidad" title="Agregar Especialidad" style="background-color: #134496; font-size: 1.2rem;">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
        </div>

        <!-- Modal Crear -->
        <div class="modal fade" id="modalAgregarEspecialidad" tabindex="-1" aria-labelledby="modalAgregarEspecialidadLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <button class="btn-back" data-bs-dismiss="modal">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h5 class="modal-title">Crear Especialidad</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <form action="{{ route('especialidades.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre de la Especialidad</label>
                                <input type="text" name="nombre" class="form-control" placeholder="Ingrese el nombre de la Especialidad" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Institución</label>
                                <div class="input-group dynamic-group">
                                    <input id="inputInstitucion" name="institucion" type="text" class="form-control" placeholder="Seleccione una institución" readonly required>
                                    <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                    <ul class="dropdown-menu custom-dropdown">
                                        <li><a class="dropdown-item" onclick="document.getElementById('inputInstitucion').value='Covao'">Covao</a></li>
                                        <li><a class="dropdown-item" onclick="document.getElementById('inputInstitucion').value='Covao Nocturno'">Covao Nocturno</a></li>
                                        <li><a class="dropdown-item" onclick="document.getElementById('inputInstitucion').value='Academias HHC'">Academias HHC</a></li>
                                    </ul>
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

        <!-- Modal Editar -->
        <div class="modal fade" id="modalEditarEspecialidad" tabindex="-1" aria-labelledby="modalEditarEspecialidadLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <button class="btn-back" data-bs-dismiss="modal">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h5 class="modal-title">Editar Especialidad</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <form id="formEditarEspecialidad" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" id="editarIdEspecialidad">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre de la Especialidad</label>
                                <input type="text" id="editarNombreEspecialidad" name="nombre" class="form-control" placeholder="Ingrese el nombre de la Especialidad" required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-bold">Institución</label>
                                <div class="input-group dynamic-group">
                                    <input id="editarInputInstitucion" name="institucion" type="text" class="form-control" placeholder="Seleccione una institución" readonly required>
                                    <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                    <ul class="dropdown-menu custom-dropdown">
                                        <li><a class="dropdown-item" onclick="document.getElementById('editarInputInstitucion').value='Covao'">Covao</a></li>
                                        <li><a class="dropdown-item" onclick="document.getElementById('editarInputInstitucion').value='Covao Nocturno'">Covao Nocturno</a></li>
                                        <li><a class="dropdown-item" onclick="document.getElementById('editarInputInstitucion').value='Academias HHC'">Academias HHC</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-crear">Guardar Cambios</button>
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
                            <td class="text-center">{{ $especialidad->nombre }}</td>
                            <td class="text-center">{{ $especialidad->institucion->nombre }}</td>
                            <td class="text-center">
                                <button class="btn btn-link text-info p-0 me-2 btn-editar" data-id="{{ $especialidad->id }}">
                                    <i class="bi bi-pencil" style="font-size: 1.8rem;"></i>
                                </button>
                                <form action="{{ route('especialidades.destroy', $especialidad->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-info p-0">
                                        <i class="bi bi-trash" style="font-size: 1.8rem;"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('partials.footer')
@endsection

@push('scripts')
    <script src="{{ asset('js/Sidebar.js') }}"></script>
    <script src="{{ asset('js/modals-create-especialidad.js') }}"></script>
    <script src="{{ asset('js/modals-edit-especialidad.js') }}"></script>
@endpush
