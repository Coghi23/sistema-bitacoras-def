{{-- filepath: resources/views/horario/modals/editar.blade.php --}}
<div class="modal fade" id="modalEditarHorario{{ $horario->id }}" tabindex="-1" aria-labelledby="modalEditarHorarioLabel{{ $horario->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-4 shadow-lg">
            <form method="POST" action="{{ route('horario.update', $horario->id) }}">
                @csrf
                @method('PUT')
                <div class="modal-header custom-header text-white px-4 py-3 position-relative justify-content-center">
                    <button type="button" class="btn p-0 d-flex align-items-center position-absolute start-0 ms-3" data-bs-dismiss="modal" aria-label="Cerrar">
                        <div class="circle-yellow d-flex justify-content-center align-items-center">
                            <i class="fas fa-arrow-left text-blue-forced"></i>
                        </div>
                        <div class="linea-vertical-amarilla ms-2"></div>
                    </button>
                    <h5 class="modal-title m-0" id="modalEditarHorarioLabel{{ $horario->id }}">Modificar horario</h5>
                </div>
                <div class="linea-divisoria-horizontal"></div>
                <div class="modal-body px-4 pt-3">
                    {{-- Tipo de Horario --}}
                    <div class="mb-4 d-flex align-items-center justify-content-between fw-bold">
                        <label class="w-50 text-start">Tipo de horario:</label>
                        <div class="d-flex align-items-center justify-content-center w-50 bg-info bg-opacity-10 border border-info rounded-3 p-2">
                            <div class="form-check me-3 d-flex align-items-center">
                                <input class="form-check-input" type="radio" name="tipoHorario" value="fijo" @if($horario->tipoHorario == 'fijo') checked @endif disabled>
                                <label class="form-check-label ms-2">Fijo</label>
                            </div>
                            <div style="width:1px; height:24px; background-color:#0d6efd; opacity:0.7;"></div>
                            <div class="form-check ms-3 d-flex align-items-center">
                                <input class="form-check-input" type="radio" name="tipoHorario" value="temporal" @if($horario->tipoHorario == 'temporal') checked @endif disabled>
                                <label class="form-check-label ms-2">Temporal</label>
                            </div>
                        </div>
                    </div>
                    {{-- Fecha --}}
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <label class="fw-bold me-3 w-50 text-start">Fecha:</label>
                        <input type="date" name="fecha" class="form-control rounded-4 w-50"
                            value="{{ $horario->fecha ?? '' }}" @if($horario->tipoHorario != 'temporal') disabled @endif>
                    </div>
                    {{-- Día --}}
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <label class="fw-bold me-3 w-50 text-start">Día:</label>
                        <div class="position-relative w-50">
                            <select name="dia" class="form-select rounded-4 pe-5" @if($horario->tipoHorario != 'fijo') disabled @endif>
                                <option value="" hidden>Seleccione...</option>
                                <option value="Lunes" @if($horario->dia == 'Lunes') selected @endif>Lunes</option>
                                <option value="Martes" @if($horario->dia == 'Martes') selected @endif>Martes</option>
                                <option value="Miércoles" @if($horario->dia == 'Miércoles') selected @endif>Miércoles</option>
                                <option value="Jueves" @if($horario->dia == 'Jueves') selected @endif>Jueves</option>
                                <option value="Viernes" @if($horario->dia == 'Viernes') selected @endif>Viernes</option>
                                <option value="Sábado" @if($horario->dia == 'Sábado') selected @endif>Sábado</option>
                                <option value="Domingo" @if($horario->dia == 'Domingo') selected @endif>Domingo</option>
                            </select>
                        </div>
                    </div>
                    {{-- Docente --}}
                    {{-- Docente --}}
<div class="mb-3 d-flex align-items-center justify-content-between">
    <label for="idDocente" class="fw-bold me-3 w-50 text-start">Docente:</label>
    <div class="position-relative w-50">
        <select name="idDocente" id="idDocente" class="form-select rounded-4 pe-5" required>
            <option value="" hidden>Seleccione...</option>
            @foreach($profesores as $profesor)
                <option value="{{ $profesor->id }}"
                    @if($horario->idProfesor == $profesor->id) selected @endif>
                    {{ $profesor->usuario->nombre }}
                </option>
            @endforeach
        </select>
    </div>
</div>
                    {{-- Recinto --}}
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <label class="fw-bold me-3 w-50 text-start">Recinto:</label>
                        <div class="position-relative w-50">
                            <select name="idRecinto" class="form-select rounded-4 pe-5" required>
                                <option value="" hidden>Seleccione...</option>
                                @foreach($recintos as $recinto)
                                    <option value="{{ $recinto->id }}" @if($horario->idRecinto == $recinto->id) selected @endif>
                                        {{ $recinto->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                   {{-- Hora de ingreso --}}
<div class="mb-3 d-flex align-items-center justify-content-between">
    <label class="fw-bold me-3 w-50 text-start">Hora de ingreso:</label>
    <div class="d-flex w-50">
        <input type="text" name="horaEntrada" class="form-control rounded-4 me-2 text-center" placeholder="hh:mm" style="max-width: 90px;" required
            value="{{ \Carbon\Carbon::parse($horario->horaEntrada)->format('h:i') }}" />
        <select name="horaEntradaAmPm" class="form-select rounded-4" style="max-width: 100px;" required>
            <option value="am" @if(\Carbon\Carbon::parse($horario->horaEntrada)->format('A') == 'AM') selected @endif>AM</option>
            <option value="pm" @if(\Carbon\Carbon::parse($horario->horaEntrada)->format('A') == 'PM') selected @endif>PM</option>
        </select>
    </div>
</div>
{{-- Hora de salida --}}
<div class="mb-3 d-flex align-items-center justify-content-between">
    <label class="fw-bold me-3 w-50 text-start">Hora de salida:</label>
    <div class="d-flex w-50">
        <input type="text" name="horaSalida" class="form-control rounded-4 me-2 text-center" placeholder="hh:mm" style="max-width: 90px;" required
            value="{{ \Carbon\Carbon::parse($horario->horaSalida)->format('h:i') }}" />
        <select name="horaSalidaAmPm" class="form-select rounded-4" style="max-width: 100px;" required>
            <option value="am" @if(\Carbon\Carbon::parse($horario->horaSalida)->format('A') == 'AM') selected @endif>AM</option>
            <option value="pm" @if(\Carbon\Carbon::parse($horario->horaSalida)->format('A') == 'PM') selected @endif>PM</option>
        </select>
    </div>
</div>
                    {{-- Subárea + Sección --}}
                    <div class="mb-3 d-flex align-items-center justify-content-between">
    <label for="idSubareaSeccion" class="fw-bold me-3 w-50 text-start">Subárea y sección:</label>
    <div class="position-relative w-50">
        <select name="idSubareaSeccion" id="idSubareaSeccion" class="form-select rounded-4 pe-5" required>
            <option value="" hidden>Seleccione...</option>
            @foreach($subareasSecciones as $subareaSeccion)
                <option value="{{ $subareaSeccion->id }}"
                    @if($horario->idSubareaSeccion == $subareaSeccion->id) selected @endif>
                    {{ $subareaSeccion->nombre }}
                </option>
            @endforeach
        </select>
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