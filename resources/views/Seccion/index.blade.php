@extends('Template-administrador')

@section('title', 'Registro de Sección')

@section('content')
<div class="wrapper">
    <div class="main-content">

        {{-- Búsqueda + botón agregar --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="input-group w-50">
                <form id="busquedaForm" method="GET" action="{{ route('seccion.index') }}" class="d-flex w-100">
                    <span class="input-group-text bg-white border-white">
                        <i class="bi bi-search text-secondary"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 shadow-sm"
                        placeholder="Buscar sección..." name="busquedaSeccion" 
                        value="{{ request('busquedaSeccion') }}" id="inputBusqueda" autocomplete="off" 
                        style="border-radius: 20px;">
                    @if(request('busquedaSeccion'))
                    <button type="button" class="btn btn-outline-secondary border-0" id="limpiarBusqueda" title="Limpiar búsqueda">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    @endif
                </form>
            </div>
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center"
                data-bs-toggle="modal" data-bs-target="#modalAgregarSeccion"
                title="Agregar Sección" style="background-color: #134496; font-size: 1.2rem; @if(Auth::user() && Auth::user()->hasRole('director')) display: none; @endif">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
        </div>

        {{-- Indicador de resultados de búsqueda --}}
        @if(request('busquedaSeccion'))
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <span>
                    Mostrando {{ $secciones->count() }} resultado(s) para "<strong>{{ request('busquedaSeccion') }}</strong>"
                    <a href="{{ route('seccion.index') }}" class="btn btn-sm btn-outline-primary ms-2">Ver todas</a>
                </span>
            </div>
        @endif

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
                                @if(Auth::user() && !Auth::user()->hasRole('director'))
                                <button class="btn btn-link text-info p-0 me-2" data-bs-toggle="modal"
                                    data-bs-target="#modalEditarSeccion-{{ $seccion->id }}">
                                    <i class="bi bi-pencil" style="font-size: 1.5rem;"></i>
                                </button>
                                <button class="btn btn-link text-danger p-0" data-bs-toggle="modal"
                                    data-bs-target="#modalEliminarSeccion-{{ $seccion->id }}">
                                    <i class="bi bi-trash" style="font-size: 1.5rem;"></i>
                                </button>
                                @else
                                <span class="text-muted">Solo vista</span>
                                @endif
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
                                    {{-- Mostrar errores de validación --}}
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            @foreach ($errors->all() as $error)
                                                <div>{{ $error }}</div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    <form action="{{ route('seccion.update', $seccion->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="form_type" value="edit">
                                        <input type="hidden" name="seccion_id" value="{{ $seccion->id }}">
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
                {{-- Mostrar errores de validación --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                
                <form action="{{ route('seccion.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_type" value="create">
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
            }, 500); // Espera 500ms después de que el usuario deje de escribir
        });
        
        // También permitir búsqueda al presionar Enter
        inputBusqueda.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                formBusqueda.submit();
            }
        });
    }
    
    // Funcionalidad del botón limpiar
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function() {
            inputBusqueda.value = '';
            window.location.href = '{{ route("seccion.index") }}';
        });
    }
    
    // Mantener modal abierto si hay errores
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            // Detectar qué tipo de formulario fue enviado para abrir el modal correcto
            const formType = '{{ old("form_type") }}';
            const seccionId = '{{ old("seccion_id") }}';
            
            if (formType === 'create') {
                var modal = new bootstrap.Modal(document.getElementById('modalAgregarSeccion'));
                modal.show();
            } else if (formType === 'edit' && seccionId) {
                var modal = new bootstrap.Modal(document.getElementById('modalEditarSeccion-' + seccionId));
                modal.show();
            }
        });
    @endif
</script>
@endsection


