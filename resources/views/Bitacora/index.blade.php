{{-- filepath: resources/views/Evento/create.blade.php --}}
{{-- Vista de listado y creación de reportes de eventos para bitácoras --}}
@extends('layouts.app')

@section('content')
<style>
/* Responsive adjustments for bitacora */
@media (max-width: 768px) {
    .main-content {
        margin-left: 0 !important;
        padding: 0.5rem !important;
    }
    
    .form-container {
        padding: 1rem;
    }
    
    .form-responsive {
        flex-direction: column !important;
    }
    
    .form-responsive .form-label {
        width: 100% !important;
        margin-bottom: 0.5rem !important;
        text-align: left !important;
    }
    
    .form-responsive .form-control,
    .form-responsive .form-select {
        width: 100% !important;
    }
    
    .btn-submit {
        width: 100% !important;
        margin-top: 1rem;
    }
    
    h2 {
        font-size: 1.5rem !important;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    
    .alert {
        font-size: 0.9rem;
        padding: 0.75rem;
    }
    
    .mb-3 {
        margin-bottom: 1.5rem !important;
    }
}

@media (max-width: 576px) {
    .form-container {
        padding: 0.5rem;
    }
    
    .form-control,
    .form-select {
        font-size: 16px; /* Evita zoom en iOS */
    }
    
    h2 {
        font-size: 1.25rem !important;
    }
}
</style>

<div id="bitacora-container" class="container-fluid">
    <div id="main-content" class="main-content p-3">
        {{-- Título principal de la página --}}
        <div id="page-header" class="row">
            <div class="col-12">
                <h2 id="page-title" class="text-center text-md-start mb-4">Crear Reporte de Evento</h2>
            </div>
        </div>

        {{-- Muestra errores de validación si existen --}}
        @if ($errors->any())
            <div id="error-alerts" class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <h6><i class="bi bi-exclamation-triangle-fill me-2"></i>Errores de validación:</h6>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Formulario para crear un nuevo evento asociado a una bitácora --}}
        <div id="form-container" class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-6">
                <div class="form-container bg-light rounded-3 shadow-sm">
                    <form id="evento-form" action="{{ route('evento.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf

                        {{-- Selector de bitácora (recinto) --}}
                        <div id="bitacora-field" class="mb-3">
                            <div class="form-responsive d-flex flex-column flex-md-row align-items-md-center">
                                <label for="id_bitacora" class="form-label fw-bold mb-2 mb-md-0 me-md-3" style="min-width: 150px;">
                                    <i class="bi bi-building me-2 text-primary"></i>Bitácora (Recinto):
                                </label>
                                <select name="id_bitacora" id="id_bitacora" class="form-select flex-grow-1" required>
                                    <option value="">Seleccione una bitácora</option>
                                    @foreach($bitacoras as $bitacora)
                                        <option value="{{ $bitacora->id }}">
                                            {{ $bitacora->recinto->nombre ?? 'Sin recinto' }} - Bitácora #{{ $bitacora->id }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione una bitácora.
                                </div>
                            </div>
                        </div>

                        {{-- Selector de sección --}}
                        <div id="seccion-field" class="mb-3">
                            <div class="form-responsive d-flex flex-column flex-md-row align-items-md-center">
                                <label for="id_seccion" class="form-label fw-bold mb-2 mb-md-0 me-md-3" style="min-width: 150px;">
                                    <i class="bi bi-grid-3x3-gap me-2 text-info"></i>Sección:
                                </label>
                                <select name="id_seccion" id="id_seccion" class="form-select flex-grow-1" required>
                                    <option value="">Seleccione una sección</option>
                                    @foreach($seccione as $seccion)
                                        <option value="{{ $seccion->id }}">{{ $seccion->nombre }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione una sección.
                                </div>
                            </div>
                        </div>

                        {{-- Selector de subárea --}}
                        <div id="subarea-field" class="mb-3">
                            <div class="form-responsive d-flex flex-column flex-md-row align-items-md-center">
                                <label for="id_subarea" class="form-label fw-bold mb-2 mb-md-0 me-md-3" style="min-width: 150px;">
                                    <i class="bi bi-diagram-3 me-2 text-warning"></i>Subárea:
                                </label>
                                <select name="id_subarea" id="id_subarea" class="form-select flex-grow-1" required>
                                    <option value="">Seleccione una subárea</option>
                                    @foreach($subareas as $subarea)
                                        <option value="{{ $subarea->id }}">{{ $subarea->nombre }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione una subárea.
                                </div>
                            </div>
                        </div>

                        {{-- Selector de horario --}}
                        <div id="horario-field" class="mb-3">
                            <div class="form-responsive d-flex flex-column flex-md-row align-items-md-center">
                                <label for="id_horario" class="form-label fw-bold mb-2 mb-md-0 me-md-3" style="min-width: 150px;">
                                    <i class="bi bi-calendar-event me-2 text-success"></i>Horario:
                                </label>
                                <select name="id_horario" id="id_horario" class="form-select flex-grow-1" required>
                                    <option value="">Seleccione un horario</option>
                                    @foreach($horarios as $horario)
                                        <option value="{{ $horario->id }}">
                                            {{ $horario->fecha }} - {{ $horario->recinto->nombre ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Por favor seleccione un horario.
                                </div>
                            </div>
                        </div>

                        {{-- Campo para la hora de envío del evento --}}
                        <div id="hora-envio-field" class="mb-3">
                            <div class="form-responsive d-flex flex-column flex-md-row align-items-md-center">
                                <label for="hora_envio" class="form-label fw-bold mb-2 mb-md-0 me-md-3" style="min-width: 150px;">
                                    <i class="bi bi-clock me-2 text-danger"></i>Hora de Envío:
                                </label>
                                <input type="time" name="hora_envio" id="hora_envio" class="form-control flex-grow-1" required>
                                <div class="invalid-feedback">
                                    Por favor ingrese la hora de envío.
                                </div>
                            </div>
                        </div>

                        {{-- Campo para observaciones adicionales --}}
                        <div id="observacion-field" class="mb-3">
                            <div class="form-responsive d-flex flex-column">
                                <label for="observacion" class="form-label fw-bold mb-2">
                                    <i class="bi bi-chat-text me-2 text-secondary"></i>Observación:
                                </label>
                                <textarea name="observacion" id="observacion" class="form-control" rows="3" 
                                    placeholder="Ingrese observaciones adicionales (opcional)"></textarea>
                            </div>
                        </div>

                        {{-- Selector de prioridad del evento --}}
                        <div id="prioridad-field" class="mb-4">
                            <div class="form-responsive d-flex flex-column flex-md-row align-items-md-center">
                                <label for="prioridad" class="form-label fw-bold mb-2 mb-md-0 me-md-3" style="min-width: 150px;">
                                    <i class="bi bi-exclamation-diamond me-2 text-dark"></i>Prioridad:
                                </label>
                                <select name="prioridad" id="prioridad" class="form-select flex-grow-1">
                                    <option value="">Seleccione prioridad</option>
                                    <option value="alta">🔴 Alta</option>
                                    <option value="media">🟡 Media</option>
                                    <option value="regular">🟢 Regular</option>
                                    <option value="baja">⚪ Baja</option>
                                </select>
                            </div>
                        </div>

                        {{-- Botones de acción --}}
                        <div id="form-actions" class="d-flex flex-column flex-md-row gap-2 justify-content-end">
                            <button type="button" id="btn-cancel" class="btn btn-secondary btn-submit" onclick="history.back()">
                                <i class="bi bi-arrow-left me-2"></i>Cancelar
                            </button>
                            <button type="submit" id="btn-save" class="btn btn-primary btn-submit">
                                <i class="bi bi-check-circle me-2"></i>Guardar Evento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación de formulario Bootstrap
    const form = document.getElementById('evento-form');
    
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
    
    // Auto-focus en el primer campo
    document.getElementById('id_bitacora').focus();
    
    // Establecer hora actual por defecto
    const horaInput = document.getElementById('hora_envio');
    const now = new Date();
    const timeString = now.toTimeString().slice(0, 5);
    horaInput.value = timeString;
});
</script>
@endsection