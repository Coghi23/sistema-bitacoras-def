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
                <input type="text" class="form-control border-start-0 rounded-pill" placeholder="Buscar">
            </div>
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#modalAgregarEspecialidad" title="Agregar Especialidad" style="background-color: #134496; font-size: 1.2rem;">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
        </div>

        {{-- Modal Crear Especialidad --}}
        <div class="modal fade" id="modalAgregarEspecialidad" tabindex="-1" aria-labelledby="modalAgregarEspecialidadLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('especialidades.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalAgregarEspecialidadLabel">Agregar Especialidad</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre de la Especialidad</label>
                                <input type="text" class="form-control" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="institucion_id" class="form-label">Institución</label>
                                <select class="form-select" name="institucion_id" required>
                                    <option value="">Seleccione una institución</option>
                                    @foreach($instituciones as $institucion)
                                        <option value="{{ $institucion->id }}">{{ $institucion->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary rounded-pill">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Editar Especialidad --}}
        <div class="modal fade" id="modalEditarEspecialidad" tabindex="-1" aria-labelledby="modalEditarEspecialidadLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form id="formEditarEspecialidad" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditarEspecialidadLabel">Editar Especialidad</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="editarIdEspecialidad">
                            <div class="mb-3">
                                <label for="editarNombreEspecialidad" class="form-label">Nombre de la Especialidad</label>
                                <input type="text" class="form-control" id="editarNombreEspecialidad" name="nombre" required>
                            </div>
                            <div class="mb-3">
                                <label for="editarInstitucionId" class="form-label">Institución</label>
                                <select class="form-select" id="editarInstitucionId" name="institucion_id" required>
                                    <option value="">Seleccione una institución</option>
                                    @foreach($instituciones as $institucion)
                                        <option value="{{ $institucion->id }}">{{ $institucion->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary rounded-pill">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabla de especialidades --}}
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


@endsection

@push('scripts')
<script src="{{ asset('js/Sidebar.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formEditar = document.getElementById('formEditarEspecialidad');

        document.querySelectorAll('.btn-editar').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;

                fetch(`/especialidades/${id}/edit`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('editarIdEspecialidad').value = data.id;
                        document.getElementById('editarNombreEspecialidad').value = data.nombre;
                        document.getElementById('editarInstitucionId').value = data.institucion_id;
                        formEditar.action = `/especialidades/${id}`;
                        const modal = new bootstrap.Modal(document.getElementById('modalEditarEspecialidad'));
                        modal.show();
                    })
                    .catch(error => {
                        alert('No se pudo cargar la especialidad.');
                        console.error(error);
                    });
            });
        });

        formEditar.addEventListener('submit', function (e) {
            const nombre = document.getElementById('editarNombreEspecialidad').value.trim();
            const institucion = document.getElementById('editarInstitucionId').value;

            if (!nombre || !institucion) {
                e.preventDefault();
                alert('Todos los campos son obligatorios.');
            }
        });
    });
</script>
@endpush
                    .then(data => {
                        document.getElementById('editarIdEspecialidad').value = data.id;
                        document.getElementById('editarNombreEspecialidad').value = data.nombre;
                        document.getElementById('editarInstitucionId').value = data.institucion_id;
                        formEditar.action = `/especialidades/${id}`;
                        const modal = new bootstrap.Modal(document.getElementById('modalEditarEspecialidad'));
                        modal.show();
                    })
                    .catch(error => {
                        alert('No se pudo cargar la especialidad.');
                        console.error(error);
                    });
            });
        });

        formEditar.addEventListener('submit', function (e) {
            const nombre = document.getElementById('editarNombreEspecialidad').value.trim();
            const institucion = document.getElementById('editarInstitucionId').value;

            if (!nombre || !institucion) {
                e.preventDefault();
                alert('Todos los campos son obligatorios.');
            }
        });
    });
</script>
@endpush
