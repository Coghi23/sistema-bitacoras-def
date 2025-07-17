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
                <input type="text" class="form-control border-start-0" style="border-radius: 20px;" placeholder="Buscar" />
            </div>
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center" 
                data-bs-toggle="modal" data-bs-target="#modalAgregarSeccion" 
                title="Agregar Sección" style="background-color: #134496; font-size: 1.2rem;">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
        </div>

        <!-- Modal Crear Sección -->
        <div class="modal fade" id="modalAgregarSeccion" tabindex="-1" aria-labelledby="modalAgregarSeccionLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <button class="btn-back" data-bs-dismiss="modal">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h5 class="modal-title">Crear Sección</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <form action="{{ route('secciones.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nombreSeccion" class="form-label fw-bold">Nombre de la Sección</label>
                                <input type="text" name="nombre" id="nombreSeccion" class="form-control" placeholder="Ingrese el nombre de la Sección" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Especialidad</label>
                                <select name="especialidad" class="form-select" required>
                                    <option value="" selected disabled>Seleccione una especialidad</option>
                                    @foreach ($especialidades as $especialidad)
                                        <option value="{{ $especialidad->id }}">{{ $especialidad->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold">Institución</label>
                                <select name="institucion" class="form-select" required>
                                    <option value="" selected disabled>Seleccione una institución</option>
                                    @foreach ($instituciones as $institucion)
                                        <option value="{{ $institucion->id }}">{{ $institucion->nombre }}</option>
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

        <!-- Modal Editar Sección -->
        <div class="modal fade" id="modalEditarSeccion" tabindex="-1" aria-labelledby="modalEditarSeccionLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <button class="btn-back" data-bs-dismiss="modal">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h5 class="modal-title">Editar Sección</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <form id="formEditarSeccion" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" id="editarIdSeccion">

                            <div class="mb-3">
                                <label for="editNombreSeccion" class="form-label fw-bold">Nombre de la Sección</label>
                                <input type="text" name="nombre" id="editNombreSeccion" class="form-control" placeholder="Ingrese el nombre de la Sección" required>
                            </div>

                            <div class="mb-3">
                                <label for="editEspecialidad" class="form-label fw-bold">Especialidad</label>
                                <select name="especialidad" id="editEspecialidad" class="form-select" required>
                                    <option value="" selected disabled>Seleccione una especialidad</option>
                                    @foreach ($especialidades as $especialidad)
                                        <option value="{{ $especialidad->id }}">{{ $especialidad->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="editInstitucion" class="form-label fw-bold">Institución</label>
                                <select name="institucion" id="editInstitucion" class="form-select" required>
                                    <option value="" selected disabled>Seleccione una institución</option>
                                    @foreach ($instituciones as $institucion)
                                        <option value="{{ $institucion->id }}">{{ $institucion->nombre }}</option>
                                    @endforeach
                                </select>
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
                        <th class="text-center" style="width: 30%;">Sección</th>
                        <th class="text-center" style="width: 30%;">Especialidad</th>
                        <th class="text-center" style="width: 30%;">Institución</th>
                        <th class="text-center" style="width: 10%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($secciones as $seccion)
                    <tr>
                        <td class="text-center">{{ $seccion->nombre }}</td>
                        <td class="text-center">{{ $seccion->especialidad->nombre ?? '' }}</td>
                        <td class="text-center">{{ $seccion->institucion->nombre ?? '' }}</td>
                        <td class="text-center">
                            <button class="btn btn-link text-info p-0 me-2 btn-editar-seccion" 
                                data-bs-toggle="modal" data-bs-target="#modalEditarSeccion"
                                data-id="{{ $seccion->id }}"
                                data-nombre="{{ $seccion->nombre }}"
                                data-especialidad="{{ $seccion->especialidad_id }}"
                                data-institucion="{{ $seccion->institucion_id }}">
                                <i class="bi bi-pencil" style="font-size: 1.8rem;"></i>
                            </button>
                            <form action="{{ route('secciones.destroy', $seccion->id) }}" method="POST" class="d-inline">
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
<script>
    // Cuando se abre el modal editar sección, cargar los datos
    var modalEditar = document.getElementById('modalEditarSeccion');
    modalEditar.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var nombre = button.getAttribute('data-nombre');
        var especialidad = button.getAttribute('data-especialidad');
        var institucion = button.getAttribute('data-institucion');

        var modal = this;
        modal.querySelector('#editarIdSeccion').value = id;
        modal.querySelector('#editNombreSeccion').value = nombre;
        modal.querySelector('#editEspecialidad').value = especialidad;
        modal.querySelector('#editInstitucion').value = institucion;

        var form = modal.querySelector('form#formEditarSeccion');
        form.action = '/secciones/' + id; // Ajusta según rutas Laravel
    });
</script>

<script src="{{ asset('js/modals-edit-seccion.js') }}"></script>
<script src="{{ asset('js/modals-create-seccion.js') }}"></script>
<script src="{{ asset('js/Sidebar.js') }}"></script>
@endpush
