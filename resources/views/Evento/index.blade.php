@extends('Template-administrador')

@section('title', 'Sistema de Eventos')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('Css/reporte.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<div class="wrapper">
    <div class="main-content">
        <!-- Loading spinner -->
        <div id="loadingSpinner" class="text-center d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>

        <!-- Tabla de eventos -->
        <div id="tabla-reportes" class="tabla-contenedor shadow-sm rounded">
            <!-- Encabezados -->
            <div class="header-row text-white" style="background-color: #134496;">
                <div class="col-docente">Docente</div>
                <div class="col-recinto">Recinto</div>
                <div class="col-fecha">Fecha</div>
                <div class="col-hora">Hora</div>
                <div class="col-institucion">Institución</div>
                <div class="col-prioridad">Prioridad</div>
                <div class="col-estado">Estado</div>
                <div class="col-detalles">Detalles</div>
            </div>

            <!-- Contenedor para datos asíncronos -->
            <div id="eventos-container">            
                @foreach ($eventos as $evento)
                    <div class="record-row hover-effect">
                        <div data-label="Docente">{{ $evento->usuario->name ?? 'N/A' }}</div>
                        <div data-label="Recinto">{{ $evento->horario->recinto->nombre ?? '' }}</div>
                        <div data-label="Fecha">{{ \Carbon\Carbon::parse($evento->fecha)->format('d/m/Y') }}</div>
                        <div data-label="Hora">{{ \Carbon\Carbon::parse($evento->hora_envio)->format('H:i') }}</div>
                        <div data-label="Institución">{{ $evento->horario->recinto->institucion->nombre ?? '' }}</div>
                        <div data-label="Prioridad">
                            <span class="badge bg-secondary">
                                {{ ucfirst($evento->prioridad) }}
                            </span>
                        </div>
                        <div data-label="Estado">
                            <span class="badge bg-secondary">
                                @if($evento->estado == 'en_espera')
                                    En espera
                                @elseif($evento->estado == 'en_proceso')
                                    En proceso
                                @elseif($evento->estado == 'completado')
                                    Completado
                                @else
                                    {{ ucfirst($evento->estado) }}
                                @endif
                            </span>
                        </div>
                        <div data-label="Detalles">
                            <button class="btn btn-sm rounded-pill px-3" 
                                    style="background-color: #134496; color: white;"
                                    onclick="abrirModal({{ $evento->id }})">
                                <i class="bi bi-eye me-1"></i> Ver Más
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modales existentes sin cambios -->
@foreach ($eventos as $evento)
    <div id="modalDetalles-{{ $evento->id }}" class="modal">
        <div class="modal-contenido">
            <div class="modal-encabezado">
                <span class="icono-atras" onclick="cerrarModal({{ $evento->id }})">
                    <i>
                        <img width="40" height="40" src="https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png" alt="icono volver"/>
                    </i>
                </span>
                <h1 class="titulo">Detalles</h1>
            </div>

            <div class="modal-cuerpo">
                <div class="row">
                    <div class="col">
                        <label>Docente:</label>
                        <input type="text" value="{{ $evento->usuario->name ?? 'N/A' }}" disabled>

                        <label>Institución:</label>
                        <input type="text" value="{{ $evento->horario->recinto->institucion->nombre ?? '' }}" disabled>

                        <label>SubÁrea:</label>
                        <input type="text" value="{{ $evento->subarea->nombre ?? '' }}" disabled>

                        <label>Sección:</label>
                        <input type="text" value="{{ $evento->seccion->nombre ?? '' }}" disabled>

                        <label>Especialidad:</label>
                        <input type="text" value="{{ $evento->subarea->especialidad->nombre ?? '' }}" disabled>
                    </div>

                    <div class="col">
                        <label>Fecha:</label>
                        <input type="text" value="{{ \Carbon\Carbon::parse($evento->fecha)->format('d/m/Y') }}" disabled>
                        
                        <label>Hora:</label>
                        <input type="text" value="{{ \Carbon\Carbon::parse($evento->hora_envio)->format('H:i') }}" disabled>
                        
                        <label>Recinto:</label>
                        <input type="text" value="{{ $evento->horario->recinto->nombre ?? '' }}" disabled>

                        <label>Prioridad:</label>
                        <input type="text" value="{{ ucfirst($evento->prioridad) }}" disabled>

                        <label>Estado:</label>
                        <input type="text" value="@if($evento->estado == 'en_espera')En espera@elseif($evento->estado == 'en_proceso')En proceso@elseif($evento->estado == 'completado')Completado@else{{ ucfirst($evento->estado) }}@endif" disabled>
                    </div>
                </div>

                <div class="observaciones">
                    <label>Observaciones:</label>
                    <textarea disabled>{{ $evento->observacion }}</textarea>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('styles')
<style>
.hover-effect {
    transition: all 0.3s ease;
}

.hover-effect:hover {
    background-color: rgba(0,0,0,0.02);
    transform: translateY(-1px);
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}

.tabla-contenedor {
    border: 1px solid rgba(0,0,0,0.1);
    background: white;
}

.header-row {
    font-weight: 500;
}

.record-row {
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.form-control, .form-select, .input-group-text {
    border-radius: 20px;
}

.input-group .form-select {
    border-start-start-radius: 0;
    border-end-start-radius: 0;
}

/* Update color variables */
:root {
    --primary-blue: #134496;
}

.bg-primary {
    background-color: var(--primary-blue) !important;
}

.btn-primary {
    background-color: var(--primary-blue) !important;
    border-color: var(--primary-blue) !important;
}

/* Modal styling updates */
.modal-contenido {
    background: none;
    box-shadow: none;
}

.modal-cuerpo {
    background: white;
    border-radius: 8px;
    padding: 20px;
}

.swal2-popup {
    background: transparent !important;
    box-shadow: none !important;
}

.swal2-content {
    background: white;
    border-radius: 8px;
    padding: 20px !important;
}

/* Update spinner color */
.spinner-border.text-primary {
    color: var(--primary-blue) !important;
}

/* Añadir transición suave para actualizaciones */
#eventos-container {
    transition: opacity 0.15s ease-in-out;
}

@media (max-width: 768px) {
    .record-row {
        grid-template-columns: 1fr;
        gap: 0.5rem;
        padding: 1rem;
    }

    .record-row > div {
        padding: 0.5rem;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .record-row > div:last-child {
        border-bottom: none;
    }

    [data-label]:before {
        content: attr(data-label);
        font-weight: 600;
        display: inline-block;
        width: 120px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const loadingSpinner = document.getElementById('loadingSpinner');
const eventosContainer = document.getElementById('eventos-container');
let currentTimestamp = '{{ $eventos->max('updated_at') }}';

async function cargarEventos() {
    try {
        const response = await fetch(`{{ route('eventos.load') }}?timestamp=${currentTimestamp}`, {
            headers: { 
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Error en la red');

        const data = await response.json();
        
        if (data.success && data.hasNewData) {
            loadingSpinner.classList.remove('d-none');
            eventosContainer.style.opacity = '0.6';
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data.html;
            
            const newEventosContainer = tempDiv.querySelector('#eventos-container');
            if (newEventosContainer) {
                eventosContainer.innerHTML = newEventosContainer.innerHTML;
                currentTimestamp = data.timestamp;
            }
            
            eventosContainer.style.opacity = '1';
            loadingSpinner.classList.add('d-none');
        }
    } catch (error) {
        console.error('Error:', error);
        loadingSpinner.classList.add('d-none');
    }
}

// Comprobar cambios cada 3 segundos
const intervalId = setInterval(cargarEventos, 3000);

// Función para abrir modal
function abrirModal(id) {
    Swal.fire({
        html: document.getElementById('modalDetalles-' + id).innerHTML,
        width: '80%',
        showConfirmButton: false,
        showCloseButton: false,
        customClass: {
            container: 'modal-detalles-container',
            popup: 'bg-transparent',
            content: 'bg-transparent'
        }
    });
}

function cerrarModal(id) {
    Swal.close();
}

// Limpiar intervalo cuando se abandona la página
window.addEventListener('beforeunload', () => {
    clearInterval(intervalId);
});

// Cargar datos iniciales
document.addEventListener('DOMContentLoaded', cargarEventos);
</script>
@endpush
