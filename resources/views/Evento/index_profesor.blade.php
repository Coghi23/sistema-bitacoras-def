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
                            aria-labelledby="modalEventoEliminarLabel-{{ $evento->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content custom-modal">
                                    <div class="modal-body text-center">
                                        <div class="icon-container">
                                            <div class="circle-icon">
                                                <i class="bi bi-exclamation-circle"></i>
                                            </div>
                                        </div>
                                        <p class="modal-text">¿Desea Eliminar el Evento?</p>
                                        <div class="btn-group-custom">
                                            <form action="{{ route('evento.destroy', ['evento' => $evento->id]) }}"
                                                method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-custom">Sí</button>
                                                <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                            </form>
                                        </div>
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
        function abrirModal(id) {
            const evento = @json($eventos);
            const e = evento.find(ev => ev.id === id);
            if (!e) {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Evento no encontrado' });
                return;
            }
            Swal.fire({
                title: '',
                html: `
                <div class='modal-contenido'>
                    <div class='modal-encabezado d-flex align-items-center mb-3'>
                        <span class='icono-atras' onclick='Swal.close()' style='cursor:pointer;'>
                            <img width='40' height='40' src='https://img.icons8.com/external-solid-adri-ansyah/64/FAB005/external-ui-basic-ui-solid-adri-ansyah-26.png' alt='icono volver'/>
                        </span>
                        <h1 class='titulo ms-3 mb-0' style='font-size:1.5rem;'>Editar Evento</h1>
                    </div>
                    <div class='modal-cuerpo'>
                        <div class='row'>
                            <div class='col'>
                                <label>Docente:</label>
                                <input type='text' class='form-control mb-2' value='${e.usuario?.name ?? ''}' disabled>
                                <label>Institución:</label>
                                <input type='text' class='form-control mb-2' value='${e.horario?.recinto?.institucion?.nombre ?? ''}' disabled>
                                <label>SubÁrea:</label>
                                <input type='text' class='form-control mb-2' value='${e.subarea?.nombre ?? ''}' disabled>
                                <label>Sección:</label>
                                <input type='text' class='form-control mb-2' value='${e.seccion?.nombre ?? ''}' disabled>
                                <label>Especialidad:</label>
                                <input type='text' class='form-control mb-2' value='${e.subarea?.especialidad?.nombre ?? ''}' disabled>
                            </div>
                            <div class='col'>
                                <label>Fecha:</label>
                                <input type='text' class='form-control mb-2' value='${e.fecha ?? ''}' disabled>
                                <label>Hora:</label>
                                <input type='text' class='form-control mb-2' value='${e.hora_envio ?? ''}' disabled>
                                <label>Recinto:</label>
                                <input type='text' class='form-control mb-2' value='${e.horario?.recinto?.nombre ?? ''}' disabled>
                                <label>Prioridad:</label>
                                <select class='form-select mb-2' id='prioridadInput'>
                                    <option value='alta' ${e.prioridad === 'alta' ? 'selected' : ''}>Alta</option>
                                    <option value='media' ${e.prioridad === 'media' ? 'selected' : ''}>Media</option>
                                    <option value='regular' ${e.prioridad === 'regular' ? 'selected' : ''}>Regular</option>
                                    <option value='baja' ${e.prioridad === 'baja' ? 'selected' : ''}>Baja</option>
                                </select>
                                <label>Estado:</label>
                                <input type='text' class='form-control mb-2' value='@if($evento->estado == "en_espera")En espera@elseif($evento->estado == "en_proceso")En proceso@elseif($evento->estado == "completado")Completado@else{{ ucfirst($evento->estado) }}@endif' disabled>
                            </div>
                        </div>
                        <div class='observaciones mt-3'>
                            <label>Observaciones:</label>
                            <textarea id='observacionInput' class='form-control mb-2'>${e.observacion ?? ''}</textarea>
                        </div>
                    </div>
                </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Guardar Cambios',
                cancelButtonText: 'Cerrar',
                width: '800px',
                customClass: {
                    popup: 'bg-transparent',
                    content: 'swal2-content',
                    container: 'modal-detalles-container'
                },
                preConfirm: () => {
                    return {
                        prioridad: document.getElementById('prioridadInput').value,
                        observacion: document.getElementById('observacionInput').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarCambios(id, result.value);
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



        function guardarCambios(id, data) {
            const formData = new FormData();
            formData.append('prioridad', data.prioridad);
            formData.append('observacion', data.observacion);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
            formData.append('_method', 'PATCH'); // Importante para spoofing de método
            fetch(`/evento/${id}`, {
                method: 'POST', // Laravel reconoce _method para spoofing
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Cambios guardados',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    setTimeout(() => location.reload(), 1200);
                } else {
                    throw new Error(result.message || 'Error al guardar cambios');
                }
            })
            .catch(error => {
                Swal.fire({ icon: 'error', title: 'Error', text: error.message });
            });
        }


        // Limpiar intervalo cuando se abandona la página
        window.addEventListener('beforeunload', () => {
            clearInterval(intervalId);
        });

        // Cargar datos iniciales
        document.addEventListener('DOMContentLoaded', cargarEventos);
    </script>
@endpush