@extends('Template-administrador')

@section('title', 'Sistema de Bitácoras')

@section('content')
 


<!-- Modal Agregar Sub Área -->
<div class="modal fade" id="modalAgregarSubarea" tabindex="-1" aria-labelledby="modalAgregarSubareaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button class="btn-back" data-bs-dismiss="modal"><i class="bi bi-arrow-left"></i></button>
                <h5 class="modal-title">Crear Sub Área</h5>
            </div>
            <div class="modal-body px-4 py-4">
                <form id="formAgregarSubarea">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Sub Área</label>
                        <input type="text" class="form-control" id="nombreSubarea" placeholder="Ingrese el nombre de la Sub Área" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Especialidad</label>
                        <div class="input-group dynamic-group">
                            <input type="text" class="form-control especialidad-input" id="especialidadSubarea" placeholder="Seleccione una especialidad" readonly required>
                            <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown"></button>
                            <!-- Aqui iban datos quemados pero no se poner la relacion -->
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Profesor a cargo</label>
                        <div class="input-group dynamic-group">
                            <input type="text" class="form-control profesor-input" id="profesorSubarea" placeholder="Seleccione un profesor" readonly required>
                            <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown"></button>
                            <ul class="dropdown-menu custom-dropdown">
                                <!-- Aqui iban datos quemados pero no se poner la relacion -->
                            </ul>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Institución</label>
                        <div class="input-group dynamic-group">
                            <input type="text" class="form-control institucion-input" id="institucionSubarea" placeholder="Seleccione una institución" readonly required>
                            <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown"></button>
                            <ul class="dropdown-menu custom-dropdown">
                                 <!-- Aqui iban datos quemados pero no se poner la relacion -->
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

<!-- Modal Editar Sub Área -->
<div class="modal fade" id="modalEditarSubarea" tabindex="-1" aria-labelledby="modalEditarSubareaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button class="btn-back" data-bs-dismiss="modal"><i class="bi bi-arrow-left"></i></button>
                <h5 class="modal-title">Editar Sub Área</h5>
            </div>
            <div class="modal-body px-4 py-4">
                <form id="formEditarSubarea">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Sub Área</label>
                        <input type="text" class="form-control" id="editarNombreSubarea" placeholder="Ingrese el nombre de la Sub Área" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Especialidad</label>
                        <div class="input-group dynamic-group">
                            <input type="text" class="form-control" id="editarEspecialidad" placeholder="Seleccione una especialidad" readonly required>
                            <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown"></button>
                            <ul class="dropdown-menu custom-dropdown">
                                 <!-- Aqui iban datos quemados pero no se poner la relacion -->
                            </ul>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Profesor a cargo</label>
                        <div class="input-group dynamic-group">
                            <input type="text" class="form-control" id="editarProfesor" placeholder="Seleccione un profesor" readonly required>
                            <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown"></button>
                            <ul class="dropdown-menu custom-dropdown">
                                 <!-- Aqui iban datos quemados pero no se poner la relacion -->
                            </ul>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Institución</label>
                        <div class="input-group dynamic-group">
                            <input type="text" class="form-control" id="editarInstitucion" placeholder="Seleccione una institución" readonly required>
                            <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown"></button>
                            <ul class="dropdown-menu custom-dropdown">
                                 <!-- Aqui iban datos quemados pero no se poner la relacion -->
                            </ul>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-crear">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Sub Áreas -->
<div class="table-responsive">
    <table class="table align-middle table-hover">
        <thead>
            <tr>
                <th class="text-center" style="width: 30%;">Profesor a cargo</th>
                <th class="text-center" style="width: 30%;">Nombre de la Sub-Área</th>
                <th class="text-center" style="width: 30%;">Especialidad</th>
                <th class="text-center" style="width: 30%;">Institución</th>
                <th class="text-center" style="width: 10%;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">Mauricio Vargas Carballo</td>
                <td class="text-center">Programación</td>
                <td class="text-center">Desarrollo Web</td>
                <td class="text-center">COVAO Diurno</td>
                <td class="text-center">
                    <button class="btn btn-link text-info p-0 me-2" data-bs-toggle="modal" data-bs-target="#modalEditarSubarea">
                        <i class="bi bi-pencil" style="font-size: 1.8rem;"></i>
                    </button>
                    <button class="btn btn-link text-info p-0">
                        <i class="bi bi-trash" style="font-size: 1.8rem;"></i>
                    </button>
                </td>
            </tr>
            <!-- ... más filas si es necesario ... -->
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
    // Selecciona opción desde dropdown
    function seleccionarOpcion(elemento, inputId) {
        const input = document.getElementById(inputId) || document.querySelector(`input.${inputId}`);
        if (input) {
            input.value = elemento.textContent.trim();
        }
    }

    // Validaciones simples de campos vacíos para evitar envíos erróneos
    document.getElementById('formAgregarSubarea').addEventListener('submit', function (e) {
        const campos = ['nombreSubarea', 'especialidadSubarea', 'profesorSubarea', 'institucionSubarea'];
        for (const id of campos) {
            if (!document.getElementById(id).value.trim()) {
                e.preventDefault();
                alert('Todos los campos son obligatorios.');
                return;
            }
        }
    });

    document.getElementById('formEditarSubarea').addEventListener('submit', function (e) {
        const campos = ['editarNombreSubarea', 'editarEspecialidad', 'editarProfesor', 'editarInstitucion'];
        for (const id of campos) {
            if (!document.getElementById(id).value.trim()) {
                e.preventDefault();
                alert('Todos los campos son obligatorios.');
                return;
            }
        }
    });
</script>
@endpush
