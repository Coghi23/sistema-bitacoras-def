@extends('layouts.app')

@section('content')

    <!-- Modal Agregar Sub Área -->
    <div class="modal fade" id="modalAgregarSubarea" tabindex="-1" aria-labelledby="modalAgregarSubareaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <button class="btn-back" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <h5 class="modal-title">Crear Sub Área</h5>
                </div>

                <div class="modal-body px-4 py-4">
                    <!-- NOMBRE SUB ÁREA -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Sub Área</label>
                        <input type="text" class="form-control" placeholder="Ingrese el nombre de la Sub Área">
                    </div>

                    <!-- ESPECIALIDAD -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Especialidad</label>
                        <div id="especialidades">
                            <div class="input-group dynamic-group">
                                <input type="text" class="form-control especialidad-input" placeholder="Seleccione una especialidad" readonly>
                                <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu custom-dropdown">
                                    <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'especialidad-input')">Mantenimiento Industrial</a></li>
                                    <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'especialidad-input')">Electrónica</a></li>
                                    <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'especialidad-input')">Contabilidad</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- PROFESOR -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Profesor a cargo</label>
                        <div id="profesores">
                            <div class="input-group dynamic-group">
                                <input type="text" class="form-control profesor-input" placeholder="Seleccione un profesor" readonly>
                                <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu custom-dropdown">
                                    <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'profesor-input')">Ana Piedra</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- INSTITUCIÓN -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Institución</label>
                        <div id="instituciones">
                            <div class="input-group dynamic-group">
                                <input type="text" class="form-control institucion-input" placeholder="Seleccione una institución" readonly>
                                <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu custom-dropdown">
                                    <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'institucion-input')">Covao</a></li>
                                    <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'institucion-input')">Covao Nocturno</a></li>
                                    <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'institucion-input')">Academias HHC</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- BOTÓN CREAR -->
                    <div class="text-center mt-4">
                        <button class="btn btn-crear">Crear</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDITAR SUB ÁREA -->
    <div class="modal fade" id="modalEditarSubarea" tabindex="-1" aria-labelledby="modalEditarSubareaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header modal-header-custom">
                    <button class="btn-back" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <h5 class="modal-title">Editar Sub Área</h5>
                </div>
                <div class="modal-body px-4 py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Sub Área</label>
                        <input type="text" class="form-control" id="editarNombreSubarea" placeholder="Ingrese el nombre de la Sub Área">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Especialidad</label>
                        <div class="input-group dynamic-group">
                            <input type="text" class="form-control especialidad-input" id="editarEspecialidad" placeholder="Seleccione una especialidad" readonly>
                            <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                            <ul class="dropdown-menu custom-dropdown">
                                <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'editarEspecialidad')">Mantenimiento Industrial</a></li>
                                <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'editarEspecialidad')">Electrónica</a></li>
                                <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'editarEspecialidad')">Contabilidad</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Profesor a cargo</label>
                        <div class="input-group dynamic-group">
                            <input type="text" class="form-control profesor-input" id="editarProfesor" placeholder="Seleccione un profesor" readonly>
                            <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                            <ul class="dropdown-menu custom-dropdown">
                                <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'editarProfesor')">Ana Piedra</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Institución</label>
                        <div class="input-group dynamic-group">
                            <input type="text" class="form-control institucion-input" id="editarInstitucion" placeholder="Seleccione una institución" readonly>
                            <button class="btn-plus dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                            <ul class="dropdown-menu custom-dropdown">
                                <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'editarInstitucion')">Covao</a></li>
                                <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'editarInstitucion')">Covao Nocturno</a></li>
                                <li><a class="dropdown-item" onclick="seleccionarOpcion(this, 'editarInstitucion')">Academias HHC</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button class="btn btn-crear">Guardar cambios</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                <tr>
                    <td class="text-center">Alexander Monge Vargas</td>
                    <td class="text-center">TIC's WEB</td>
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
                <tr>
                    <td class="text-center">Joshua Quesada Guzman</td>
                    <td class="text-center">Diseño Web</td>
                    <td class="text-center">Desarrollo Web</td>
                    <td class="text-center">Covao Diurno</td>
                    <td class="text-center">
                        <button class="btn btn-link text-info p-0 me-2" data-bs-toggle="modal" data-bs-target="#modalEditarSubarea">
                            <i class="bi bi-pencil" style="font-size: 1.8rem;"></i>
                        </button>
                        <button class="btn btn-link text-info p-0">
                            <i class="bi bi-trash" style="font-size: 1.8rem;"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Scripts inline necesarios para seleccionar opción en inputs --}}
    <script>
        function seleccionarOpcion(elemento, inputId) {
            const input = document.getElementById(inputId) || document.querySelector(`input[readonly].${inputId}`);
            if(input) {
                input.value = elemento.textContent.trim();
            }
        }
    </script>

@endsection
