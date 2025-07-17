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
                <input type="text" class="form-control border-start-0" style="border-radius: 20px;" placeholder="Buscar" />
            </div>
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center" 
                data-bs-toggle="modal" data-bs-target="#modalAgregarInstitucion" 
                title="Agregar Institución" style="background-color: #134496; font-size: 1.2rem;">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
        </div>

        <!-- Modal Crear Institución -->
        <div class="modal fade" id="modalAgregarInstitucion" tabindex="-1" aria-labelledby="modalAgregarInstitucionLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <button class="btn-back" data-bs-dismiss="modal">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h5 class="modal-title">Crear Institución</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <form action="{{ route('institucion.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nombreInstitucion" class="form-label fw-bold">Nombre de la Institución</label>
                                <input type="text" name="nombre" id="nombreInstitucion" class="form-control" placeholder="Ingrese el nombre de la Institución" required>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-crear">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Editar Institución -->
        <div class="modal fade" id="modalEditarInstitucion" tabindex="-1" aria-labelledby="modalEditarInstitucionLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <button class="btn-back" data-bs-dismiss="modal">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h5 class="modal-title">Editar Institución</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <form id="formEditarInstitucion" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="id" id="editarIdInstitucion">
                            <div class="mb-3">
                                <label for="editarNombreInstitucion" class="form-label fw-bold">Nombre de la Institución</label>
                                <input type="text" name="nombre" id="editarNombreInstitucion" class="form-control" placeholder="Ingrese el nuevo nombre de la Institución" required>
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
                        <th class="text-center" style="width: 90%;">Nombre de la Institución</th>
                        <th class="text-center" style="width: 10%;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($instituciones as $institucion)
                        <tr>
                            <td class="text-center">{{ $institucion->nombre }}</td>
                            <td class="text-center">
                                <button class="btn btn-link text-info p-0 me-2 btn-editar" 
                                    data-bs-toggle="modal" data-bs-target="#modalEditarInstitucion"
                                    data-id="{{ $institucion->id }}" data-nombre="{{ $institucion->nombre }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Pasar datos al modal Editar Institución al abrirlo
    var editarModal = document.getElementById('modalEditarInstitucion');
    editarModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var nombre = button.getAttribute('data-nombre');

        var modal = this;
        modal.querySelector('#editarIdInstitucion').value = id;
        modal.querySelector('#editarNombreInstitucion').value = nombre;

        // Actualizar acción del formulario según id
        var form = modal.querySelector('#formEditarInstitucion');
        form.action = '/instituciones/' + id; // Ajusta la URL según rutas Laravel
    });
</script>

<script src="{{ asset('js/Sidebar.js') }}"></script>
<script src="{{ asset('js/modals-create-institucion.js') }}"></script>
<script src="{{ asset('js/modals-edit-institucion.js') }}"></script>
@endpush
