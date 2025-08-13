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
                <form method="GET" action="{{ route('horario.index') }}" class="d-flex w-100">
                    <input type="text" class="form-control border-start-0 shadow-sm" style="border-radius: 20px;" placeholder="Buscar docente..." name="busquedaDocente" value="{{ request('busquedaDocente') }}" autocomplete="off" />
                </form>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center" 
                    data-bs-toggle="modal" data-bs-target="#modalHorario" 
                    title="Agregar Horario" style="background-color: #134496; font-size: 1.2rem;">
                    Agregar <i class="bi bi-plus-circle ms-2"></i>
                </button>
                <div class="dropdown">
                    <button class="btn btn-outline-primary rounded-pill px-4 d-flex align-items-center dropdown-toggle" 
                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                        style="font-size: 1.2rem;">
                        <i class="bi bi-funnel me-2"></i>
                        Filtros
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('horario.index') }}">Todos</a></li>
                        <li><a class="dropdown-item" href="{{ route('horario.index', ['tipo' => 'fijo']) }}">Horarios fijos</a></li>
                        <li><a class="dropdown-item" href="{{ route('horario.index', ['tipo' => 'temporal']) }}">Horarios temporales</a></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Tabla Horarios Fijos --}}
        <div id="tabla-horarios-fijos">
            <table class="table">
                <thead>
                    <tr class="header-row">
                        <th class="col-dia">Día</th>
                        <th class="col-recinto">Recinto</th>
                        <th class="col-especialidad">Especialidad</th>
                        <th class="col-seccion">Sección</th>
                        <th class="col-entrada">Entrada</th>
                        <th class="col-salida">Salida</th>
                        <th class="col-docente">Docente</th>
                        <th class="col-acciones">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($horarios as $horario)
                    <tr class="record-row">
                        @if($horario->condicion == 1)
                            <td class="col-dia">
                            @if($horario->tipoHorario == false)
                                {{ $horario->fecha->format('Y/m/d') }}
                            @endif
                            {{ $horario->dia }}
                            </td>
                            <td class="col-recinto">{{ $horario->recinto->nombre ?? '' }}</td>
                            <td class="col-especialidad">{{ $horario->subarea->nombre ?? '' }}</td>
                            <td class="col-seccion">{{ $horario->seccion->nombre ?? '' }}</td>
                            <td class="col-entrada">{{ $horario->hora_entrada }}</td>
                            <td class="col-salida">{{ $horario->hora_salida }}</td>
                            <td class="col-docente">{{ $horario->profesor->name ?? '' }}</td>
                            <td class="col-acciones">
                                <button type="button" class="btn p-0" data-bs-toggle="modal" data-bs-target="#modalEditarHorario{{ $horario->id }}">
                                    <i class="bi bi-pencil icon-editar"></i>
                                </button>
                                <button class="btn p-0" data-bs-toggle="modal" data-bs-target="#modalEliminarHorario{{ $horario->id }}">
                                    <i class="bi bi-trash icon-eliminar"></i>
                                </button>
                            </td>
                        @endif
                    </tr>
                    @empty
                    <tr class="record-row">
                        <td class="col text-center" colspan="8">No hay horarios fijos registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    {{-- Modal Crear Horario --}}
    <div class="modal fade" id="modalHorario" tabindex="-1" aria-labelledby="modalHorarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header modal-header-custom">
                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h5 class="modal-title">Crear Horario</h5>
                    </div>
                <form method="POST" action="{{ route('horario.store') }}">
                    @csrf
                    <div class="modal-header custom-header text-white px-4 py-3 position-relative justify-content-center">
                        <button type="button" class="btn p-0 d-flex align-items-center position-absolute start-0 ms-3" data-bs-dismiss="modal" aria-label="Cerrar">
                            <div class="circle-yellow d-flex justify-content-center align-items-center">
                                <i class="fas fa-arrow-left text-blue-forced"></i>
                            </div>
                            <div class="linea-vertical-amarilla ms-2"></div>
                        </button>
                    </div>
                    <div class="linea-divisoria-horizontal"></div>
                    <div class="modal-body px-4 pt-3">
                        {{-- Tipo de Horario --}}
                        <div class="mb-4 d-flex align-items-center justify-content-between fw-bold">
                            <label class="w-50 text-start">Tipo de horario:</label>
                            <div class="d-flex align-items-center justify-content-center w-50 bg-info bg-opacity-10 border border-info rounded-3 p-2">
                                <div class="form-check me-3 d-flex align-items-center">
                                    <input class="form-check-input {{ request('tipoHorario') == '1' ? 'active' : '' }}" type="radio" name="tipoHorario" id="fijoRadio" value="fijo" required>
                                    <label class="form-check-label ms-2" for="fijoRadio">Fijo</label>
                                </div>
                                <div style="width:1px; height:24px; background-color:#0d6efd; opacity:0.7;"></div>
                                <div class="form-check ms-3 d-flex align-items-center">
                                    <input class="form-check-input {{ request('tipoHorario') == '0' ? 'active' : '' }}" type="radio" name="tipoHorario" id="temporalRadio" value="temporal" required>
                                    <label class="form-check-label ms-2" for="temporalRadio">Temporal</label>
                                </div>
                            </div>
                        </div>
                        {{-- Fecha --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">Fecha:</label>
                            <input type="date" name="fecha" class="form-control rounded-4 w-50" @if(old('tipoHorario')=='1') disabled @endif>
                        </div>
                        {{-- Día --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">Día:</label>
                            <div class="position-relative w-50">
                                <select name="dia" class="form-select rounded-4 pe-5" @if(old('tipoHorario')=='0') disabled @endif>
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
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label for="idDocente" class="fw-bold me-3 w-50 text-start">Docente:</label>
                            <div class="position-relative w-50">
                                <select data-size="4" title="Seleccione un docente" data-live-search="true" name="user_id" id="user_id" class="form-select rounded-4 pe-5" required>
                                    <option value="">Seleccione un docente</option>
                                    @foreach ($profesores as $profesor)
                                        <option value="{{$profesor->id}}" {{ old('user_id') == $profesor->id ? 'selected' : '' }}>{{$profesor->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        {{-- Recinto --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label for="recintoSelect" class="fw-bold me-3 w-50 text-start">Recinto:</label>
                            <div class="position-relative w-50">
                                <select data-size="4" title="Seleccione un docente" data-live-search="true" name="idRecinto" id="idRecinto" class="form-select rounded-4 pe-5" required>
                                    <option value="">Seleccione un recinto</option>
                                    @foreach ($recintos as $recinto)
                                        <option value="{{$recinto->id}}" {{ old('idRecinto') == $recinto->id ? 'selected' : '' }}>{{$recinto->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                            {{-- Subárea --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label for="idSubarea" class="fw-bold me-3 w-50 text-start">Subárea:</label>
                            <div class="position-relative w-50">
                                <select data-size="4" title="Seleccione un docente" data-live-search="true" name="idSubarea" id="idSubarea" class="form-select rounded-4 pe-5" required>
                                    <option value="">Seleccione un docente</option>
                                    @foreach ($subareas as $subarea)
                                        <option value="{{$subarea->id}}" {{ old('idSubarea') == $subarea->id ? 'selected' : '' }}>{{$subarea->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                            {{-- Sección --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label for="idSubareaSeccion" class="fw-bold me-3 w-50 text-start">Sección:</label>
                            <div class="position-relative w-50">
                                <select data-size="4" title="Seleccione un docente" data-live-search="true" name="idSeccion" id="idSeccion" class="form-select rounded-4 pe-5" required>
                                    <option value="">Seleccione un sección</option>
                                    @foreach ($secciones as $seccion)
                                        <option value="{{$seccion->id}}" {{ old('idSeccion') == $seccion->id ? 'selected' : '' }}>{{$seccion->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Lecciones --}}
                        <div class="mb-3">
                            <label class="fw-bold mb-3">Lecciones:</label>
                            <div class="border rounded-4 p-3" style="max-height: 300px; overflow-y: auto;">
                                {{-- Lecciones Académicas --}}
                                <div class="mb-3">
                                    <h6 class="text-primary mb-2">Lecciones Académicas</h6>
                                    <div class="row">
                                        @foreach($lecciones as $leccion)
                                            @if($leccion->tipoLeccion == 'Academica')
                                                <div class="col-12 col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="lecciones[]" 
                                                            value="{{ $leccion->id }}" id="leccion{{ $leccion->id }}">
                                                        <label class="form-check-label small" for="leccion{{ $leccion->id }}">
                                                            {{ $leccion->leccion }} ({{ $leccion->hora_inicio }} - {{ $leccion->hora_final }})
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Lecciones Tecnicas --}}
                                <div>
                                    <h6 class="text-success mb-2">Lecciones Técnicas</h6>
                                    <div class="row">
                                        @foreach($lecciones as $leccion)
                                            @if($leccion->tipoLeccion == 'Tecnica')
                                                <div class="col-12 col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="lecciones[]" 
                                                            value="{{ $leccion->id }}" id="leccion{{ $leccion->id }}">
                                                        <label class="form-check-label small" for="leccion{{ $leccion->id }}">
                                                            {{ $leccion->leccion }} ({{ $leccion->hora_inicio }} - {{ $leccion->hora_final }})
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Botones para seleccionar/deseleccionar todos --}}
                                <div class="mt-3 d-flex gap-2 justify-content-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="seleccionarTodasLecciones()">
                                        Seleccionar todas
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deseleccionarTodasLecciones()">
                                        Deseleccionar todas
                                    </button>
                                </div>
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

    @foreach($horarios as $horario )
    {{-- Modales de edición y eliminación se incluyen por cada horario en el loop de arriba --}}
    <div class="modal fade" id="modalEditarHorario{{ $horario->id }}" tabindex="-1" aria-labelledby="modalEditarHorarioLabel{{ $horario->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 shadow-lg">
                <form method="POST" action="{{ route('horario.update', ['horario' => $horario]) }}">
                    @csrf
                    @method('PUT')
                    <div class="modal-header custom-header text-white px-4 py-3 position-relative justify-content-center">
                        <button type="button" class="btn p-0 d-flex align-items-center position-absolute start-0 ms-3" data-bs-dismiss="modal" aria-label="Cerrar">
                            <div class="circle-yellow d-flex justify-content-center align-items-center">
                                <i class="fas fa-arrow-left text-blue-forced"></i>
                            </div>
                            <div class="linea-vertical-amarilla ms-2"></div>
                        </button>
                        <h5 class="modal-title m-0" id="modalEditarHorarioLabel">Modificar horario</h5>
                    </div>
                    <div class="linea-divisoria-horizontal"></div>
                    <input type="hidden" name="id" id="editarIdHorario">
                    <div class="modal-body px-4 pt-3">
                        {{-- Tipo de Horario --}}
                        <div class="mb-4 d-flex align-items-center justify-content-between fw-bold">
                            <label class="w-50 text-start">Tipo de horario:</label>
                            <div class="d-flex align-items-center justify-content-center w-50 bg-info bg-opacity-10 border border-info rounded-3 p-2">
                                <div class="form-check me-3 d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="tipoHorario" id="fijoRadio{{ $horario->id }}" value="fijo" 
                                    {{ $horario->tipoHorario == 1 ? 'checked' : ''}} required>
                                    <label class="form-check-label ms-2" for="fijoRadio{{ $horario->id }}">Fijo</label>
                                </div>
                                <div style="width:1px; height:24px; background-color:#0d6efd; opacity:0.7;"></div>
                                <div class="form-check ms-3 d-flex align-items-center">
                                    <input class="form-check-input" type="radio" name="tipoHorario" id="temporalRadio{{ $horario->id }}" value="temporal"
                                    {{ $horario->tipoHorario == 0 ? 'checked' : ''}} required>
                                    <label class="form-check-label ms-2" for="temporalRadio{{ $horario->id }}">Temporal</label>
                                </div>
                            </div>
                        </div>
                        {{-- Fecha --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">Fecha:</label>
                            <input type="date" name="fecha" class="form-control rounded-4 w-50" value="{{ old('fecha', $horario->fecha ? $horario->fecha->format('Y-m-d') : '') }}">
                        </div>
                        {{-- Día --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label class="fw-bold me-3 w-50 text-start">Día:</label>
                            <div class="position-relative w-50">
                                <select name="dia" class="form-select rounded-4 pe-5">
                                    <option value="" hidden>Seleccione...</option>
                                    <option value="Lunes" {{ old('dia', $horario->dia) == 'Lunes' ? 'selected' : '' }}>Lunes</option>
                                    <option value="Martes" {{ old('dia', $horario->dia) == 'Martes' ? 'selected' : '' }}>Martes</option>
                                    <option value="Miércoles" {{ old('dia', $horario->dia) == 'Miércoles' ? 'selected' : '' }}>Miércoles</option>
                                    <option value="Jueves" {{ old('dia', $horario->dia) == 'Jueves' ? 'selected' : '' }}>Jueves</option>
                                    <option value="Viernes" {{ old('dia', $horario->dia) == 'Viernes' ? 'selected' : '' }}>Viernes</option>
                                    <option value="Sábado" {{ old('dia', $horario->dia) == 'Sábado' ? 'selected' : '' }}>Sábado</option>
                                    <option value="Domingo" {{ old('dia', $horario->dia) == 'Domingo' ? 'selected' : '' }}>Domingo</option>
                                </select>
                            </div>
                        </div>
                        
                        {{-- Docente --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label for="idDocente" class="fw-bold me-3 w-50 text-start">Docente:</label>
                            <div class="position-relative w-50">
                                <select data-size="4" title="Seleccione un docente" data-live-search="true" name="user_id" id="user_id" class="form-select rounded-4 pe-5" required>
                                    <option value="">Seleccione un docente</option>
                                    @foreach ($profesores as $profesor)
                                        <option value="{{$profesor->id}}"
                                         {{ $horario->user_id == $profesor->id ? 'selected' : '' }}>
                                            {{ $profesor->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        {{-- Recinto --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label for="recintoSelect" class="fw-bold me-3 w-50 text-start">Recinto:</label>
                            <div class="position-relative w-50">
                                <select data-size="4" title="Seleccione un docente" data-live-search="true" name="idRecinto" id="idRecinto" class="form-select rounded-4 pe-5" required>
                                    <option value="">Seleccione un recinto</option>
                                    @foreach ($recintos as $recinto)
                                        <option value="{{$recinto->id}}"
                                        {{ $horario->idRecinto == $recinto->id ? 'selected' : '' }}>
                                            {{ $recinto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                            {{-- Subárea --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label for="idSubarea" class="fw-bold me-3 w-50 text-start">Subárea:</label>
                            <div class="position-relative w-50">
                                <select data-size="4" title="Seleccione un docente" data-live-search="true" name="idSubarea" id="idSubarea" class="form-select rounded-4 pe-5" required>
                                    <option value="">Seleccione un docente</option>
                                    @foreach ($subareas as $subarea)
                                        <option value="{{$subarea->id}}" 
                                            {{ $horario->idSubarea == $subarea->id ? 'selected' : '' }}>
                                            {{ $subarea->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                            {{-- Sección --}}
                        <div class="mb-3 d-flex align-items-center justify-content-between">
                            <label for="idSubareaSeccion" class="fw-bold me-3 w-50 text-start">Sección:</label>
                            <div class="position-relative w-50">
                                <select data-size="4" title="Seleccione un docente" data-live-search="true" name="idSeccion" id="idSeccion" class="form-select rounded-4 pe-5" required>
                                    <option value="">Seleccione un sección</option>
                                    @foreach ($secciones as $seccion)
                                        <option value="{{$seccion->id}}" 
                                        {{ $horario->idSeccion == $seccion->id ? 'selected' : '' }}>
                                            {{ $seccion->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                                            {{-- Lecciones --}}
                                            <div class="mb-3">
                                                <label class="fw-bold mb-3">Lecciones:</label>
                                                <div class="border rounded-4 p-3" style="max-height: 300px; overflow-y: auto;">
                                                    {{-- Lecciones Académicas --}}
                                                    <div class="mb-3">
                                                        <h6 class="text-primary mb-2">Lecciones Académicas</h6>
                                                        <div class="row">
                                                            @foreach($lecciones as $leccion)
                                                                @if($leccion->tipoLeccion == 'Academica')
                                                                    <div class="col-12 col-md-6 mb-2">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox" name="lecciones[]" 
                                                                                value="{{ $leccion->id }}" id="leccion{{ $leccion->id }}"
                                                                                {{ $horario->leccion->contains($leccion->id) ? 'checked' : '' }}>
                                                                            <label class="form-check-label small" for="leccion{{ $leccion->id }}">
                                                                                {{ $leccion->leccion }} ({{ $leccion->hora_inicio }} - {{ $leccion->hora_final }})
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    {{-- Lecciones Tecnicas --}}
                                                    <div>
                                                        <h6 class="text-success mb-2">Lecciones Técnicas</h6>
                                                        <div class="row">
                                                            @foreach($lecciones as $leccion)
                                                                @if($leccion->tipoLeccion == 'Tecnica')
                                                                    <div class="col-12 col-md-6 mb-2">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox" name="lecciones[]" 
                                                                                value="{{ $leccion->id }}" id="leccion{{ $leccion->id }}"
                                                                                {{ $horario->leccion->contains($leccion->id) ? 'checked' : '' }}>
                                                                            <label class="form-check-label small" for="leccion{{ $leccion->id }}">
                                                                                {{ $leccion->leccion }} ({{ $leccion->hora_inicio }} - {{ $leccion->hora_final }})
                                                                            </label>
                                                                        </div>
                                                                    </div>  
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    {{-- Botones para seleccionar/deseleccionar todos --}}
                                                    <div class="mt-3 d-flex gap-2 justify-content-center">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="seleccionarTodasLecciones()">
                                                            Seleccionar todas
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deseleccionarTodasLecciones()">
                                                            Deseleccionar todas
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer px-4 pb-3 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary btn-modificar">Modificar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Modal eliminar -->
                        <div class="modal fade" id="modalEliminarHorario{{ $horario->id }}" tabindex="-1" aria-labelledby="modalEliminarHorarioLabel{{ $horario->id }}" 
                        aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content custom-modal">
                                    <div class="modal-body text-center">
                                        <div class="icon-container">
                                            <div class="circle-icon">
                                            <i class="bi bi-exclamation-circle"></i>
                                            </div>
                                        </div>
                                        <p class="modal-text">¿Desea eliminar el usuario?</p>
                                        <div class="btn-group-custom">
                                            <form action="{{ route('horario.destroy', ['horario' => $horario->id]) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-custom {{ $horario->condicion == 1 }}">Sí</button>
                                                <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
    @endforeach
    
</div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Obtener elementos
    const fijoRadio = document.getElementById('fijoRadio');
    const temporalRadio = document.getElementById('temporalRadio');
    const fechaInput = document.querySelector('input[name="fecha"]');
    const diaSelect = document.querySelector('select[name="dia"]');

    console.log('Elementos encontrados:', {
        fijoRadio: fijoRadio,
        temporalRadio: temporalRadio,
        fechaInput: fechaInput,
        diaSelect: diaSelect
    });

    // Función para manejar el estado de los campos
    function toggleFields() {
        if (fijoRadio && fijoRadio.checked) {
            // Si se selecciona "Fijo": deshabilitar fecha, habilitar día
            if (fechaInput) {
                fechaInput.disabled = true;
                fechaInput.style.backgroundColor = '#f8f9fa';
                fechaInput.style.color = '#6c757d';
                fechaInput.style.cursor = 'not-allowed';
            }
            if (diaSelect) {
                diaSelect.disabled = false;
                diaSelect.style.backgroundColor = '';
                diaSelect.style.color = '';
                diaSelect.style.cursor = '';
            }
            console.log('Fijo seleccionado - Fecha deshabilitada, Día habilitado');
        } else if (temporalRadio && temporalRadio.checked) {
            // Si se selecciona "Temporal": deshabilitar día, habilitar fecha
            if (diaSelect) {
                diaSelect.disabled = true;
                diaSelect.style.backgroundColor = '#f8f9fa';
                diaSelect.style.color = '#6c757d';
                diaSelect.style.cursor = 'not-allowed';
            }
            if (fechaInput) {
                fechaInput.disabled = false;
                fechaInput.style.backgroundColor = '';
                fechaInput.style.color = '';
                fechaInput.style.cursor = '';
            }
            console.log('Temporal seleccionado - Día deshabilitado, Fecha habilitada');
        }
    }

    // Verificar que los elementos existen
    if (fijoRadio && temporalRadio && fechaInput && diaSelect) {
        console.log('Todos los elementos existen, configurando eventos...');
        
        // Agregar event listeners a los radio buttons
        fijoRadio.addEventListener('change', function() {
            console.log('Fijo radio cambiado');
            toggleFields();
        });

        temporalRadio.addEventListener('change', function() {
            console.log('Temporal radio cambiado');
            toggleFields();
        });

        // Estado inicial - por defecto deshabilitar fecha y habilitar día
        

        console.log('Estado inicial configurado - Fecha deshabilitada por defecto');
        
    } else {
        console.error('Algunos elementos no se encontraron:', {
            fijoRadio: !!fijoRadio,
            temporalRadio: !!temporalRadio,
            fechaInput: !!fechaInput,
            diaSelect: !!diaSelect
        });
    }
});

// Funciones para manejar selección de lecciones
function seleccionarTodasLecciones() {
    const checkboxes = document.querySelectorAll('input[name="lecciones[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = true);
}

function deseleccionarTodasLecciones() {
    const checkboxes = document.querySelectorAll('input[name="lecciones[]"]');
    checkboxes.forEach(checkbox => checkbox.checked = false);
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="modalEditarHorario"]').forEach(function(modal) {
        const id = modal.id.replace('modalEditarHorario', '');
        const fijoRadio = modal.querySelector(`#fijoRadio${id}`);
        const temporalRadio = modal.querySelector(`#temporalRadio${id}`);
        const fechaInput = modal.querySelector('input[name="fecha"]');
        const diaSelect = modal.querySelector('select[name="dia"]');

        function toggleFieldsEditar() {
            if (fijoRadio && fijoRadio.checked) {
                if (fechaInput) {
                    fechaInput.disabled = true;
                    fechaInput.style.backgroundColor = '#f8f9fa';
                    fechaInput.style.color = '#6c757d';
                    fechaInput.style.cursor = 'not-allowed';
                }
                if (diaSelect) {
                    diaSelect.disabled = false;
                    diaSelect.style.backgroundColor = '';
                    diaSelect.style.color = '';
                    diaSelect.style.cursor = '';
                }
            } else if (temporalRadio && temporalRadio.checked) {
                if (diaSelect) {
                    diaSelect.disabled = true;
                    diaSelect.style.backgroundColor = '#f8f9fa';
                    diaSelect.style.color = '#6c757d';
                    diaSelect.style.cursor = 'not-allowed';
                }
                if (fechaInput) {
                    fechaInput.disabled = false;
                    fechaInput.style.backgroundColor = '';
                    fechaInput.style.color = '';
                    fechaInput.style.cursor = '';
                }
            }
        }

        if (fijoRadio && temporalRadio && fechaInput && diaSelect) {
            fijoRadio.addEventListener('change', toggleFieldsEditar);
            temporalRadio.addEventListener('change', toggleFieldsEditar);

            modal.addEventListener('shown.bs.modal', function() {
                toggleFieldsEditar();
            });

            // Estado inicial
            toggleFieldsEditar();
        }
    });
});
</script>