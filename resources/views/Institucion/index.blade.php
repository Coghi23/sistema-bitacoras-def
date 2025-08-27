@extends('Template-administrador')

@section('title', 'Sistema de Bitácoras')

@section('content')


<div class="wrapper">
    <div class="main-content">
        {{-- Búsqueda + botón agregar --}}
        <div class="search-bar-wrapper mb-4">
            <div class="search-bar">
            <form id="busquedaForm" method="GET" action="{{ route('institucion.index') }}" class="w-100 position-relative">
                    <span class="search-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control"
                        placeholder="Buscar institución..." name="busquedaInstitucion" 
                        value="{{ request('busquedaInstitucioN') }}" id="inputBusqueda" autocomplete="off">
                    @if(request('busquedaInstitucioN'))
                    <button type="button" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" id="limpiarBusqueda" title="Limpiar búsqueda" style="background: transparent;">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    @endif
                </form>
            </div>

            @if(Auth::user() && !Auth::user()->hasRole('director'))
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar" 
                data-bs-toggle="modal" data-bs-target="#modalAgregarInstitucion" 
                title="Agregar Institución" style="background-color: #134496; font-size: 1.2rem;">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
            @endif
        </div>
        {{-- Fin búsqueda + botón agregar --}}

        
        <!-- Modal Crear Institución -->
        <div class="modal fade" id="modalAgregarInstitucion" tabindex="-1" aria-labelledby="modalAgregarInstitucionLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h5 class="modal-title">Crear Nueva Institución</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <form action="{{ route('institucion.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nombreInstitucion" class="form-label fw-bold">Nombre de la Institución</label>
                                <input type="text" name="nombre" id="nombreInstitucion" class="form-control" placeholder="Ingrese el Nombre de la Institución" required>
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
                            @if ($institucion->condicion == 1)
                                <td class="text-center">{{ $institucion->nombre }}</td>
                                <td class="text-center">
                                    @if(Auth::user() && !Auth::user()->hasRole('director'))
                                    <button type="button" class="btn btn-link text-info p-0 me-2 btn-editar"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarInstitucion-{{ $institucion->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-link text-info p-0" data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $institucion->id }}" aria-label="Eliminar Institución">
                                            <i class="bi bi-trash"></i>
                                    </button>
                                    @else
                                    <span class="text-muted">Solo Vista</span>
                                    @endif
                                </td>
                            @endif
                            
                        </tr>

                        <div class="modal fade" id="modalEditarInstitucion-{{ $institucion->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-custom">
                                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                                            <i class="bi bi-arrow-left"></i>
                                        </button>
                                        <h5 class="modal-title">Editar Institución</h5>
                                    </div>
                                    <div class="modal-body px-4 py-4">
                                        {{-- Mostrar errores de validación --}}
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                @foreach ($errors->all() as $error)
                                                    <div>{{ $error }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <form action="{{ route('institucion.update', $institucion->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="form_type" value="edit">
                                            <input type="hidden" name="institucion_id" value="{{ $institucion->id }}">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Nombre de la Institución</label>
                                                <input type="text" name="nombre" class="form-control"
                                                    value="{{ old('nombre', $institucion->nombre) }}" required>
                                            </div>
                                            <div class="text-center mt-4">
                                                <button type="submit" class="btn btn-primary">Modificar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <!-- Modal eliminar -->
                        <div class="modal fade" id="modalConfirmacionEliminar-{{ $institucion->id }}" tabindex="-1" aria-labelledby="modalInstitucionEliminarLabel-{{ $institucion->id }}" 
                        aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content custom-modal">
                                    <div class="modal-body text-center">
                                        <div class="icon-container">
                                            <div class="circle-icon">
                                            <i class="bi bi-exclamation-circle"></i>
                                            </div>
                                        </div>
                                        <p class="modal-text">¿Desea Eliminar la Institución?</p>
                                        <div class="btn-group-custom">
                                            <form action="{{ route('institucion.destroy', ['institucion' => $institucion->id]) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-custom {{ $institucion->condicion == 1 }}">Sí</button>
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
                                    <p class="mb-0">Institución eliminada con éxito</p>
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
<script>



const inputBusqueda = document.getElementById('inputBusqueda');
const institucionesList = document.getElementById('instituciones-list');
const btnLimpiar = document.getElementById('limpiarBusqueda');
 event.preventDefault(); /
if (inputBusqueda && institucionesList) {
    inputBusqueda.addEventListener('input', function () {
        const valor = inputBusqueda.value.trim().toLowerCase();
        const items = institucionesList.querySelectorAll('.institucion-item');
        items.forEach(function (item) {
            const nombre = item.getAttribute('data-nombre').toLowerCase();
            if (!valor || nombre.includes(valor)) {
                item.style.display = ''; // Show item
            } else {
                item.style.display = 'none'; // Hide item
            }
        });
    });
}

if (btnLimpiar && inputBusqueda && institucionesList) {
    btnLimpiar.addEventListener('click', function () {
        inputBusqueda.value = ''; // Clear input field
        const items = institucionesList.querySelectorAll('.institucion-item');
        items.forEach(function (item) {
            item.style.display = ''; // Show all items
        });
    });
}
</script>
<script>

 
    // Mantener modal abierto si hay errores
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            // Detectar qué tipo de formulario fue enviado para abrir el modal correcto
            const formType = '{{ old("form_type") }}';
            const institucionId = '{{ old("institucion_id") }}';
            
            if (formType === 'create') {
                var modal = new bootstrap.Modal(document.getElementById('modalAgregarInstitucion'));
                modal.show();
            } else if (formType === 'edit' && institucionId) {
                var modal = new bootstrap.Modal(document.getElementById('modalEditarInstitucion-' + institucionId));
                modal.show();
            }
        });
    @endif

    // Obtener todos los nombres de instituciones existentes en la tabla
    function obtenerNombresInstituciones() {
        const nombres = [];
        document.querySelectorAll('tbody tr td.text-center:first-child').forEach(function(td) {
            if (td.textContent) {
                nombres.push(td.textContent.trim().toLowerCase());
            }
        });
        return nombres;
    }

    // Validación para el formulario de agregar institución
    document.addEventListener('DOMContentLoaded', function() {
        var formAgregar = document.querySelector('#modalAgregarInstitucion form');
        if (formAgregar) {
            formAgregar.addEventListener('submit', function(e) {
                var nombre = formAgregar.querySelector('[name="nombre"]');
                var nombreValor = nombre.value.trim().toLowerCase();
                var nombresExistentes = obtenerNombresInstituciones();
                if (!nombre.value.trim() || nombre.value.trim().length < 3) {
                    e.preventDefault();
                    alert('El nombre de la institución es obligatorio y debe tener al menos 3 caracteres.');
                    nombre.focus();
                    return;
                }
                if (nombresExistentes.includes(nombreValor)) {
                    e.preventDefault();
                    alert('Ya existe una institución con ese nombre.');
                    nombre.focus();
                }
            });
        }

        // Validación para los formularios de editar institución
        document.querySelectorAll('[id^="modalEditarInstitucion-"] form').forEach(function(formEditar) {
            formEditar.addEventListener('submit', function(e) {
                var nombre = formEditar.querySelector('[name="nombre"]');
                var nombreValor = nombre.value.trim().toLowerCase();
                var nombresExistentes = obtenerNombresInstituciones();

                // Excluir el nombre actual de la institución editada
                var nombreActual = nombre.getAttribute('value') ? nombre.getAttribute('value').trim().toLowerCase() : '';
                var nombresSinActual = nombresExistentes.filter(function(n) { return n !== nombreActual; });

                if (!nombre.value.trim() || nombre.value.trim().length < 3) {
                    e.preventDefault();
                    alert('El nombre de la institución es obligatorio y debe tener al menos 3 caracteres.');
                    nombre.focus();
                    return;
                }
                if (nombresSinActual.includes(nombreValor)) {
                    e.preventDefault();
                    alert('Ya existe una institución con ese nombre.');
                    nombre.focus();
                }
            });
        });
    });
</script>
@endpush