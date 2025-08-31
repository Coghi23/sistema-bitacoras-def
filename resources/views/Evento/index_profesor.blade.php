@extends('Template-profesor')

@section('title', 'Sistema de Eventos')

@section('content')

    <head>
        <link rel="stylesheet" href="{{ asset('Css/reporte.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <meta name="csrf-token" content="{{ csrf_token() }}">
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
                            <div data-label="Detalles" class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-primary rounded-pill px-3" style="background-color: #134496;"
                                    onclick="abrirModal({{ $evento->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" data-bs-toggle="modal"
                                    data-bs-target="#modalConfirmacionEliminar-{{ $evento->id }}" aria-label="Eliminar Evento">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Modal eliminar -->
                        <div class="modal fade" id="modalConfirmacionEliminar-{{ $evento->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="border-radius: 15px; border: none;">
                                    <div class="modal-body p-4 text-center">
                                        <div class="mb-4">
                                            <i class="bi bi-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                                        </div>
                                        <h4 class="mb-3" style="color: #2c3e50;">¿Desea desactivar este evento?</h4>
                                        <p class="text-muted mb-4">Esta acción no se puede deshacer</p>
                                        <div class="d-flex justify-content-center gap-3">
                                            <form action="{{ route('evento.destroy', $evento->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-primary px-4"
                                                    style="background-color: #134496; border: none;">
                                                    <i class="bi bi-check-lg me-2"></i>Sí, desactivar
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                                <i class="bi bi-x-lg me-2"></i>Cancelar
                                            </button>
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
                                        <p class="mb-0">Reporte eliminado con éxito</p>
                                    </div>
                                    </div>
                                </div>
                            </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>





@endsection

@push('styles')
    <style>
        .hover-effect {
            transition: all 0.3s ease;
        }

        .hover-effect:hover {
            background-color: rgba(0, 0, 0, 0.02);
            transform: translateY(-1px);
        }

        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
        }

        .tabla-contenedor {
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: white;
        }

        .header-row {
            font-weight: 500;
        }

        .record-row {
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .form-control,
        .form-select,
        .input-group-text {
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

        /* Add to your styles section */
        .form-select:not([disabled]) {
            background-color: white;
            border: 1px solid #134496;
            cursor: pointer;
            text-align-last: center;
        }

        .form-select:not([disabled]):focus {
            border-color: #134496;
            box-shadow: 0 0 0 0.25rem rgba(19, 68, 150, 0.25);
        }

        .delete-modal-popup {
            border-radius: 15px !important;
            padding: 2rem !important;
        }

        .delete-modal-title {
            font-size: 1.5rem !important;
            color: #2c3e50 !important;
            margin-bottom: 0.5rem !important;
        }

        .delete-modal-content {
            margin: 1rem 0 !important;
        }

        .swal2-icon {
            border-color: #134496 !important;
            color: #134496 !important;
        }

        @media (max-width: 768px) {
            .record-row {
                grid-template-columns: 1fr;
                gap: 0.5rem;
                padding: 1rem;
            }

            .record-row>div {
                padding: 0.5rem;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            }

            .record-row>div:last-child {
                border-bottom: none;
            }

            [data-label]:before {
                content: attr(data-label);
                font-weight: 600;
                display: inline-block;
                width: 120px;
            }
        }

        .swal2-custom-popup {
            background-color: #ffffff !important;
            /* Fondo blanco */
            border-radius: 12px !important;
            /* Bordes redondeados */
            padding: 20px !important;
            /* Espaciado interno */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2) !important;
            /* Sombra */
        }

        .swal2-custom-popup .swal2-title {
            font-size: 1.5rem !important;
            font-weight: 600;
            color: #134496;
        }

        .swal2-custom-popup .form-control,
        .swal2-custom-popup .form-select {
            border-radius: 10px;
            margin-bottom: 10px;
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
                const response = await fetch(`{{ route('eventos.soporte.load') }}?timestamp=${currentTimestamp}`, {
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
            }
        }

        // Comprobar cambios cada 3 segundos
        const intervalId = setInterval(cargarEventos, 3000);

        // Función para abrir modal
        // Función para abrir modal
        function abrirModal(evento) {
            Swal.fire({
                title: 'Detalles del Evento',
                html: `
                <div>
                    <label>Docente:</label>
                    <input type="text" class="form-control" value="${evento.usuario.name ?? 'N/A'}" disabled>

                    <label>Institución:</label>
                    <input type="text" class="form-control" value="${evento.horario.recinto.institucion?.nombre ?? ''}" disabled>

                    <label>SubÁrea:</label>
                    <input type="text" class="form-control" value="${evento.subarea?.nombre ?? ''}" disabled>

                    <label>Sección:</label>
                    <input type="text" class="form-control" value="${evento.seccion?.nombre ?? ''}" disabled>

                    <label>Especialidad:</label>
                    <input type="text" class="form-control" value="${evento.subarea?.especialidad?.nombre ?? ''}" disabled>

                    <label>Fecha:</label>
                    <input type="text" class="form-control" value="${evento.fecha_formateada}" disabled>

                    <label>Hora:</label>
                    <input type="text" class="form-control" value="${evento.hora_formateada}" disabled>

                    <label>Recinto:</label>
                    <input type="text" class="form-control" value="${evento.horario.recinto.nombre ?? ''}" disabled>

                    <label>Prioridad:</label>
                    <select class="form-select" id="prioridadInput">
                        <option value="alta" ${evento.prioridad == 'alta' ? 'selected' : ''}>Alta</option>
                        <option value="media" ${evento.prioridad == 'media' ? 'selected' : ''}>Media</option>
                        <option value="regular" ${evento.prioridad == 'regular' ? 'selected' : ''}>Regular</option>
                        <option value="baja" ${evento.prioridad == 'baja' ? 'selected' : ''}>Baja</option>
                    </select>

                    <label>Observaciones:</label>
                    <textarea id="observacionInput" class="form-control">${evento.observacion}</textarea>
                </div>
            `,
                showCancelButton: true,
                confirmButtonText: 'Guardar Cambios',
                cancelButtonText: 'Cerrar',
                customClass: {
                    popup: 'swal2-custom-popup'
                },
                preConfirm: () => {
                    // Retornar datos a enviar
                    return {
                        prioridad: document.getElementById('prioridadInput').value,
                        observacion: document.getElementById('observacionInput').value
                    };
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    // Llamar a la función de guardado
                    await guardarCambios(evento.id, result.value);
                }
            });
        }



        function cerrarModal(id) {
            Swal.close();
        }

        // Add to your existing scripts section
        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Desea desactivar este evento?',
                html: '<div class="text-muted">Esta acción no se puede deshacer</div>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#134496',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check-lg me-1"></i>Sí, desactivar',
                cancelButtonText: '<i class="bi bi-x-lg me-1"></i>Cancelar',
                customClass: {
                    container: 'delete-modal-container',
                    popup: 'delete-modal-popup',
                    title: 'delete-modal-title',
                    htmlContainer: 'delete-modal-content',
                    confirmButton: 'btn btn-primary px-4',
                    cancelButton: 'btn btn-secondary px-4'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    eliminarEvento(id);
                }
            });
        }



        async function guardarCambios(id, data) {
            try {
                const formData = new FormData();
                formData.append('prioridad', data.prioridad);
                formData.append('observacion', data.observacion);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const response = await fetch(`/evento/${id}/update`, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cambios guardados',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500
                    });
                    location.reload(); // recarga la página para reflejar cambios
                } else {
                    throw new Error(result.message || 'Error al guardar cambios');
                }

            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            }
        }


        // Limpiar intervalo cuando se abandona la página
        window.addEventListener('beforeunload', () => {
            clearInterval(intervalId);
        });

        // Cargar datos iniciales
        document.addEventListener('DOMContentLoaded', cargarEventos);
    </script>
@endpush