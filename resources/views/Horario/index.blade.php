{{-- filepath: resources/views/horario.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistema de Bitácoras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('Css/Horario.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/Modal.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/Sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/exito.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/Mensajeconf.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/mensajeeliminar.css') }}">
    <link rel="stylesheet" href="{{ asset('Css/error.css') }}">
</head>
<body class="ajuste-labels">

{{-- Sidebar --}}
@include('sidebar')

<div class="wrapper">
    <div class="main-content p-4" style="margin-left: 90px;">
        <div class="row align-items-end mb-4">
            {{-- Barra de búsqueda --}}
            <div class="col-auto flex-grow-1" style="min-width: 0;">
                <form method="GET" action="{{ route('horario.index') }}">
                    <div class="position-relative" style="max-width: 700px; width: 100%;">
                        <div class="input-group search-box shadow-sm">
                            <span class="input-group-text bg-white border-0">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control border-0" placeholder="Buscar docente" name="busquedaDocente" value="{{ request('busquedaDocente') }}" autocomplete="off" />
                        </div>
                    </div>
                </form>
            </div>
            {{-- Botones --}}
            <div class="col-auto d-flex gap-2 align-items-end">
                <button class="btn btn-agregar d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalHorario">
                    Agregar
                    <span class="icon-circle">
                        <i class="fas fa-plus fa-sm text-primary"></i>
                    </span>
                </button>
                <div class="filtros-wrapper position-relative">
                    <button class="btn btn-filtros d-flex align-items-center gap-2" type="submit" form="filtrosForm">
                        <i class="fas fa-filter"></i>
                        Filtros
                    </button>
                    <form id="filtrosForm" method="GET" action="{{ route('horario.index') }}">
                        <div class="dropdown-panel shadow-sm">
                            <button class="dropdown-item" name="tipo" value="fijo">Horarios fijos</button>
                            <button class="dropdown-item" name="tipo" value="temporal">Horarios temp.</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Tabla Horarios Fijos --}}
        <div id="tabla-horarios-fijos" @if(request('tipo')=='temporal') style="display:none;" @endif>
            <div class="d-flex header-row">
                <div class="col-dia">Día</div>
                <div class="col-docente">Docente</div>
                <div class="col-recinto">Recinto</div>
                <div class="col-subarea-seccion">Subárea - Sección</div>
                <div class="col-entrada">Entrada</div>
                <div class="col-salida">Salida</div>
                <div class="col-acciones">Acciones</div>
            </div>
            @forelse($horariosFijos as $horario)
            <div class="d-flex record-row">
                <div class="col-dia">{{ $horario->dia }}</div>
                <div class="col-docente">{{ $horario->profesor->usuario->nombre ?? '' }}</div>
                <div class="col-recinto">{{ $horario->recinto->nombre ?? '' }}</div>
                <div class="col-subarea-seccion">{{ $horario->subareaSeccion->nombre ?? '' }}</div>
                <div class="col-entrada">{{ $horario->horaEntrada }}</div>
                <div class="col-salida">{{ $horario->horaSalida }}</div>
                <div class="col-acciones">
                    <button class="btn p-0" data-bs-toggle="modal" data-bs-target="#modalEditarHorario{{ $horario->id }}">
                        <i class="bi bi-pencil icon-editar"></i>
                    </button>
                    <button class="btn p-0" data-bs-toggle="modal" data-bs-target="#modalEliminarHorario{{ $horario->id }}">
                        <i class="bi bi-trash icon-eliminar"></i>
                    </button>
                </div>
            </div>
            @include('horario.modals.editar', ['horario' => $horario])
            @include('horario.modals.eliminar', ['horario' => $horario])
            @empty
            <div class="d-flex record-row">
                <div class="col text-center" colspan="7">No hay horarios fijos registrados.</div>
            </div>
            @endforelse
        </div>

        {{-- Tabla Horarios Temporales --}}
        <div id="tabla-horarios-temporales" @if(request('tipo')!='temporal') style="display:none;" @endif>
            <div class="d-flex header-row">
                <div class="col-fecha">Fecha</div>
                <div class="col-docente">Docente</div>
                <div class="col-recinto">Recinto</div>
                <div class="col-subarea-seccion">Subárea - Sección</div>
                <div class="col-entrada">Entrada</div>
                <div class="col-salida">Salida</div>
                <div class="col-acciones">Acciones</div>
            </div>
            @forelse($horariosTemporales as $horario)
            <div class="d-flex record-row">
                <div class="col-fecha">{{ \Carbon\Carbon::parse($horario->fecha)->format('d/m/y') }}</div>
                <div class="col-docente">{{ $horario->profesor->usuario->nombre ?? '' }}</div>
                <div class="col-recinto">{{ $horario->recinto->nombre ?? '' }}</div>
                <div class="col-subarea-seccion">{{ $horario->subareaSeccion->nombre ?? '' }}</div>
                <div class="col-entrada">{{ $horario->horaEntrada }}</div>
                <div class="col-salida">{{ $horario->horaSalida }}</div>
                <div class="col-acciones">
                    <button class="btn p-0" data-bs-toggle="modal" data-bs-target="#modalEditarHorario{{ $horario->id }}">
                        <i class="bi bi-pencil icon-editar"></i>
                    </button>
                    <button class="btn p-0" data-bs-toggle="modal" data-bs-target="#modalEliminarHorario{{ $horario->id }}">
                        <i class="bi bi-trash icon-eliminar"></i>
                    </button>
                </div>
            </div>
            @include('horario.modals.editar', ['horario' => $horario])
            @include('horario.modals.eliminar', ['horario' => $horario])
            @empty
            <div class="d-flex record-row">
                <div class="col text-center" colspan="7">No hay horarios temporales registrados.</div>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Modal Crear Horario --}}
<div class="modal fade" id="modalHorario" tabindex="-1" aria-labelledby="modalHorarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-4 shadow-lg">
            <form method="POST" action="{{ route('horario.store') }}">
                @csrf
                <div class="modal-header custom-header text-white px-4 py-3 position-relative justify-content-center">
                    <button type="button" class="btn p-0 d-flex align-items-center position-absolute start-0 ms-3" data-bs-dismiss="modal" aria-label="Cerrar">
                        <div class="circle-yellow d-flex justify-content-center align-items-center">
                            <i class="fas fa-arrow-left text-blue-forced"></i>
                        </div>
                        <div class="linea-vertical-amarilla ms-2"></div>
                    </button>
                    <h5 class="modal-title m-0" id="modalHorarioLabel">Registro de horario</h5>
                </div>
                <div class="linea-divisoria-horizontal"></div>
                <div class="modal-body px-4 pt-3">
                    {{-- Tipo de Horario --}}
                    <div class="mb-4 d-flex align-items-center justify-content-between fw-bold">
                        <label class="w-50 text-start">Tipo de horario:</label>
                        <div class="d-flex align-items-center justify-content-center w-50 bg-info bg-opacity-10 border border-info rounded-3 p-2">
                            <div class="form-check me-3 d-flex align-items-center">
                                <input class="form-check-input" type="radio" name="tipoHorario" id="fijoRadio" value="fijo" required>
                                <label class="form-check-label ms-2" for="fijoRadio">Fijo</label>
                            </div>
                            <div style="width:1px; height:24px; background-color:#0d6efd; opacity:0.7;"></div>
                            <div class="form-check ms-3 d-flex align-items-center">
                                <input class="form-check-input" type="radio" name="tipoHorario" id="temporalRadio" value="temporal" required>
                                <label class="form-check-label ms-2" for="temporalRadio">Temporal</label>
                            </div>
                        </div>
                    </div>
                    {{-- Fecha --}}
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <label class="fw-bold me-3 w-50 text-start">Fecha:</label>
                        <input type="date" name="fecha" class="form-control rounded-4 w-50" @if(old('tipoHorario')!='temporal') disabled @endif>
                    </div>
                    {{-- Día --}}
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <label class="fw-bold me-3 w-50 text-start">Día:</label>
                        <div class="position-relative w-50">
                            <select name="dia" class="form-select rounded-4 pe-5" @if(old('tipoHorario')!='fijo') disabled @endif>
                                <option value="" hidden selected>Seleccione...</option>
                                <option value="Lunes">Lunes</option>
                                <option value="Martes">Martes</option>
                                <option value="Miércoles">Miércoles</option>
                                <option value="Jueves">Jueves</option>
                                <option value="Viernes">Viernes</option>
                                <option value="Sábado">Sábado</option>
                                <option value="Domingo">Domingo</option>
                            </select>
                        </div>
                    </div>
                    {{-- Docente --}}
                   {{-- Docente --}}
<div class="mb-3 d-flex align-items-center justify-content-between">
    <label for="idDocente" class="fw-bold me-3 w-50 text-start">Docente:</label>
    <div class="position-relative w-50">
        <select name="idDocente" id="idDocente" class="form-select rounded-4 pe-5" required>
            <option value="" hidden selected>Seleccione...</option>
            @foreach($profesores as $profesor)
                <option value="{{ $profesor->id }}">{{ $profesor->usuario->nombre }}</option>
            @endforeach
        </select>
    </div>
</div>
                    {{-- Recinto --}}
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <label for="recintoSelect" class="fw-bold me-3 w-50 text-start">Recinto:</label>
                        <div class="position-relative w-50">
                            <select name="idRecinto" class="form-select rounded-4 pe-5" required>
                                <option value="" hidden selected>Seleccione...</option>
                                @foreach($recintos as $recinto)
                                    <option value="{{ $recinto->id }}">{{ $recinto->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                  {{-- Hora de ingreso --}}
<div class="mb-3 d-flex align-items-center justify-content-between">
    <label class="fw-bold me-3 w-50 text-start">Hora de ingreso:</label>
    <div class="d-flex w-50">
        <input type="text" name="horaEntrada" class="form-control rounded-4 me-2 text-center" placeholder="hh:mm" style="max-width: 90px;" required />
        <select name="horaEntradaAmPm" class="form-select rounded-4" style="max-width: 100px;" required>
            <option value="" hidden selected>...</option>
            <option value="am">AM</option>
            <option value="pm">PM</option>
        </select>
    </div>
</div>
{{-- Hora de salida --}}
<div class="mb-3 d-flex align-items-center justify-content-between">
    <label class="fw-bold me-3 w-50 text-start">Hora de salida:</label>
    <div class="d-flex w-50">
        <input type="text" name="horaSalida" class="form-control rounded-4 me-2 text-center" placeholder="hh:mm" style="max-width: 90px;" required />
        <select name="horaSalidaAmPm" class="form-select rounded-4" style="max-width: 100px;" required>
            <option value="" hidden selected>...</option>
            <option value="am">AM</option>
            <option value="pm">PM</option>
        </select>
    </div>
</div>
                    {{-- Subárea + Sección --}}
                    <div class="mb-3 d-flex align-items-center justify-content-between">
    <label for="idSubareaSeccion" class="fw-bold me-3 w-50 text-start">Subárea y sección:</label>
    <div class="position-relative w-50">
        <select name="idSubareaSeccion" id="idSubareaSeccion" class="form-select rounded-4 pe-5" required>
            <option value="" hidden selected>Seleccione...</option>
            @foreach($subareasSecciones as $subareaSeccion)
                <option value="{{ $subareaSeccion->id }}">{{ $subareaSeccion->nombre }}</option>
            @endforeach
        </select>
    </div>
</div>
                </div>
                <div class="modal-footer px-4 pb-3 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-crear">Crear</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modales de edición y eliminación se incluyen por cada horario en el loop de arriba --}}

{{-- Modal Éxito Eliminar --}}
@if(session('eliminado'))
<div class="modal fade show" id="modalExitoEliminar" tabindex="-1" aria-modal="true" style="display:block;">
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
                <p class="mb-0">Horario eliminado con éxito</p>
            </div>
        </div>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>