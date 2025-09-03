@extends('Template-administrador')

@section('title', 'Sistema de Eventos')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('Css/reporte.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>

<style>
/* Responsive adjustments for Eventos */
@media (max-width: 768px) {
    .main-content {
        padding: 0.5rem !important;
    }
    
    .tabla-contenedor {
        margin: 0 !important;
        border-radius: 0.5rem !important;
        overflow: hidden;
    }
    
    .header-row {
        display: none !important; /* Hide desktop header on mobile */
    }
    
    .record-row {
        display: block !important;
        margin-bottom: 1rem;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        padding: 1rem;
    }
    
    .record-row > div {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .record-row > div:last-child {
        border-bottom: none;
        justify-content: center;
        padding-top: 1rem;
    }
    
    .record-row [data-label]:before {
        content: attr(data-label) ":";
        font-weight: 600;
        color: #134496;
        min-width: 100px;
        display: inline-block;
    }
    
    .badge {
        font-size: 0.75rem !important;
        padding: 0.4rem 0.6rem;
    }
    
    .btn {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
}

@media (max-width: 576px) {
    .record-row {
        padding: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    .record-row [data-label]:before {
        min-width: 80px;
        font-size: 0.85rem;
    }
    
    .badge {
        font-size: 0.7rem !important;
    }
    
    .btn {
        font-size: 0.75rem;
        padding: 0.35rem 0.7rem;
    }
}

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

/* Responsive modal content */
@media (max-width: 768px) {
    .modal-cuerpo .row {
        flex-direction: column;
    }
    
    .modal-cuerpo .col {
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .modal-cuerpo label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #134496;
    }
    
    .modal-cuerpo input,
    .modal-cuerpo textarea {
        font-size: 0.85rem;
        padding: 0.5rem;
    }
    
    .observaciones textarea {
        min-height: 80px;
    }
}
</style>

<div id="eventos-container-wrapper" class="wrapper">
    <div id="main-content" class="main-content">
        <!-- Loading spinner -->
        <div id="loadingSpinner" class="text-center d-none mb-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2 text-muted">Cargando eventos...</p>
        </div>

        <!-- Header de la sección -->
        <div id="eventos-header" class="row align-items-center mb-4">
            <div class="col-12">
                <h2 id="eventos-title" class="text-center text-md-start mb-3">Sistema de Eventos</h2>
                <div id="eventos-info" class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <span>Los eventos se actualizan automáticamente cada 3 segundos</span>
                </div>
            </div>
        </div>

        <!-- Tabla de eventos -->
        <div id="tabla-reportes" class="tabla-contenedor shadow-sm rounded">
            <!-- Encabezados -->
            <div id="eventos-header-row" class="header-row text-white" style="background-color: #134496;">
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
                    @if ($evento->condicion == 1)
                        <div id="evento-row-{{ $evento->id }}" class="record-row hover-effect">
                            <div data-label="Docente">{{ $evento->usuario->name ?? 'N/A' }}</div>
                            <div data-label="Recinto">{{ $evento->horario->recinto->nombre ?? 'N/A' }}</div>
                            <div data-label="Fecha">{{ \Carbon\Carbon::parse($evento->fecha)->format('d/m/Y') }}</div>
                            <div data-label="Hora">{{ \Carbon\Carbon::parse($evento->hora_envio)->format('H:i') }}</div>
                            <div data-label="Institución">{{ $evento->horario->recinto->institucion->nombre ?? 'N/A' }}</div>
                            <div data-label="Prioridad">
                                <span id="prioridad-badge-{{ $evento->id }}" class="badge 
                                    @if($evento->prioridad == 'alta') bg-danger
                                    @elseif($evento->prioridad == 'media') bg-warning text-dark
                                    @elseif($evento->prioridad == 'regular') bg-success
                                    @else bg-secondary
                                    @endif">
                                    @if($evento->prioridad == 'alta') 🔴 Alta
                                    @elseif($evento->prioridad == 'media') 🟡 Media
                                    @elseif($evento->prioridad == 'regular') 🟢 Regular
                                    @elseif($evento->prioridad == 'baja') ⚪ Baja
                                    @else {{ ucfirst($evento->prioridad) }}
                                    @endif
                                </span>
                            </div>
                            <div data-label="Estado">
                                <span id="estado-badge-{{ $evento->id }}" class="badge
                                    @if($evento->estado == 'en_espera') bg-warning text-dark
                                    @elseif($evento->estado == 'en_proceso') bg-info
                                    @elseif($evento->estado == 'completado') bg-success
                                    @else bg-secondary
                                    @endif">
                                    @if($evento->estado == 'en_espera')
                                        ⏳ En espera
                                    @elseif($evento->estado == 'en_proceso')
                                        ⚙️ En proceso
                                    @elseif($evento->estado == 'completado')
                                        ✅ Completado
                                    @else
                                        {{ ucfirst($evento->estado) }}
                                    @endif
                                </span>
                            </div>
                            <div data-label="Detalles">
                                <button id="btn-detalles-{{ $evento->id }}" class="btn btn-sm rounded-pill px-3" 
                                        style="background-color: #134496; color: white;"
                                        onclick="abrirModal({{ $evento->id }})"
                                        title="Ver detalles del evento">
                                    <i class="bi bi-eye me-1"></i> Ver Más
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach
                
                @if($eventos->where('condicion', 1)->isEmpty())
                    <div id="no-eventos-message" class="text-center py-5">
                        <div class="text-muted">
                            <i class="bi bi-calendar-x display-4 mb-3"></i>
                            <h5>No hay eventos registrados</h5>
                            <p>Los eventos aparecerán aquí cuando se registren.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modales mejorados -->
@foreach ($eventos as $evento)
    @if ($evento->condicion == 1)
        <div id="modalDetalles-{{ $evento->id }}" class="modal" style="display: none;">
            <div class="modal-contenido">
                <div class="modal-encabezado">
                    <span class="icono-atras" onclick="cerrarModal({{ $evento->id }})" 
                          style="cursor: pointer;" title="Cerrar">
                        <i>
                            <img width="40" height="40" src="https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png" alt="icono volver"/>
                        </i>
                    </span>
                    <h1 class="titulo">Detalles del Evento #{{ $evento->id }}</h1>
                </div>

                <div class="modal-cuerpo">
                    <div class="row">
                        <div class="col">
                            <label>Docente:</label>
                            <input type="text" value="{{ $evento->usuario->name ?? 'N/A' }}" disabled>

                            <label>Institución:</label>
                            <input type="text" value="{{ $evento->horario->recinto->institucion->nombre ?? 'N/A' }}" disabled>

                            <label>SubÁrea:</label>
                            <input type="text" value="{{ $evento->subarea->nombre ?? 'N/A' }}" disabled>

                            <label>Sección:</label>
                            <input type="text" value="{{ $evento->seccion->nombre ?? 'N/A' }}" disabled>

                            <label>Especialidad:</label>
                            <input type="text" value="{{ $evento->subarea->especialidad->nombre ?? 'N/A' }}" disabled>
                        </div>

                        <div class="col">
                            <label>Fecha:</label>
                            <input type="text" value="{{ \Carbon\Carbon::parse($evento->fecha)->format('d/m/Y') }}" disabled>
                            
                            <label>Hora:</label>
                            <input type="text" value="{{ \Carbon\Carbon::parse($evento->hora_envio)->format('H:i') }}" disabled>
                            
                            <label>Recinto:</label>
                            <input type="text" value="{{ $evento->horario->recinto->nombre ?? 'N/A' }}" disabled>

                            <label>Prioridad:</label>
                            <input type="text" value="{{ ucfirst($evento->prioridad) }}" disabled>

                            <label>Estado:</label>
                            <input type="text" value="@if($evento->estado == 'en_espera')En espera@elseif($evento->estado == 'en_proceso')En proceso@elseif($evento->estado == 'completado')Completado@else{{ ucfirst($evento->estado) }}@endif" disabled>
                        </div>
                    </div>

                    <div class="observaciones">
                        <label>Observaciones:</label>
                        <textarea disabled>{{ $evento->observacion ?? 'Sin observaciones' }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingSpinner = document.getElementById('loadingSpinner');
    const eventosContainer = document.getElementById('eventos-container');
    let currentTimestamp = '{{ $eventos->max('updated_at') }}';
    let updateInterval;

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
                
                setTimeout(() => {
                    eventosContainer.style.opacity = '1';
                    loadingSpinner.classList.add('d-none');
                }, 300);
            }
        } catch (error) {
            console.error('Error al cargar eventos:', error);
            loadingSpinner.classList.add('d-none');
            
            // Mostrar mensaje de error temporal
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-warning alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="bi bi-exclamation-triangle me-2"></i>
                Error al actualizar eventos. Reintentando...
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            eventosContainer.parentNode.insertBefore(alertDiv, eventosContainer);
            
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 3000);
        }
    }

    // Función para abrir modal con SweetAlert2
    window.abrirModal = function(id) {
        const modalContent = document.getElementById('modalDetalles-' + id);
        if (modalContent) {
            Swal.fire({
                html: modalContent.innerHTML,
                width: '90%',
                maxWidth: '800px',
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    container: 'modal-detalles-container',
                    popup: 'bg-transparent border-0',
                    content: 'bg-transparent p-0'
                },
                didOpen: () => {
                    // Asegurar que el botón de cerrar funcione
                    const closeBtn = document.querySelector('.swal2-close');
                    if (closeBtn) {
                        closeBtn.onclick = () => Swal.close();
                    }
                }
            });
        }
    };

    window.cerrarModal = function(id) {
        Swal.close();
    };

    // Iniciar la actualización automática
    function startAutoUpdate() {
        updateInterval = setInterval(cargarEventos, 3000);
    }

    // Parar la actualización automática
    function stopAutoUpdate() {
        if (updateInterval) {
            clearInterval(updateInterval);
            updateInterval = null;
        }
    }

    // Control de visibilidad de la página
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoUpdate();
        } else {
            startAutoUpdate();
            cargarEventos(); // Cargar inmediatamente al volver
        }
    });

    // Limpiar intervalo cuando se abandona la página
    window.addEventListener('beforeunload', stopAutoUpdate);

    // Inicializar
    startAutoUpdate();
    cargarEventos();
});
</script>
@endpush
