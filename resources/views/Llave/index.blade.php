@extends('Template-administrador')

@section('title', 'Sistema de Bitácoras')

@section('content')


<div class="wrapper">
    <div class="main-content">
        {{-- Búsqueda + botón agregar --}}
        <div class="search-bar-wrapper mb-4">
            <div class="search-bar">
                <form id="busquedaForm" method="GET" action="{{ route('llave.index') }}" class="w-100 position-relative">
                    <span class="search-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control"
                        placeholder="Buscar llave..." name="busquedaLlave" 
                        value="{{ request('busquedaLlave') }}" id="inputBusqueda" autocomplete="off">
                    @if(request('busquedaLlave'))
                    <button type="button" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" id="limpiarBusqueda" title="Limpiar búsqueda" style="background: transparent;">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    @endif
                </form>
            </div>
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
                data-bs-toggle="modal" data-bs-target="#modalAgregarLlave"
                title="Agregar Llave" style="background-color: #134496; font-size: 1.2rem; @if(Auth::user() && Auth::user()->hasRole('director')) display: none; @endif">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
        </div>



        <!-- Modal Crear Llave -->
        <div class="modal fade" id="modalAgregarLlave" tabindex="-1" aria-labelledby="modalAgregarLlaveLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom">
                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h5 class="modal-title">Crear Nueva Llave</h5>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <form action="{{ route('llave.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="nombreLlave" class="form-label fw-bold">Nombre de la Llave</label>
                                <input type="text" name="nombre" id="nombreLlave" class="form-control" placeholder="Ingrese el Nombre de la Llave" required>
                            </div>
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-crear">Crear</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

       
        <!-- Modal Editar Llave -->
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>

                        <th class="text-center" style="width: 50%;">Nombre de la llave</th>
                        <th class="text-center" style="width: 25%;">Estado</th>
                        <th class="text-center" style="width: 10%;">Última actualización</th>
                        <th class="text-center" style="width: 15%;">Acciones</th>

                        

                    </tr>
                </thead>
                <tbody id="llaves-table-body">
                    @foreach ($llaves as $llave)
                        <tr id="llave-row-{{ $llave->id }}" data-llave-id="{{ $llave->id }}">
                            @if ($llave->condicion == 1)
                                <td class="text-center">{{ $llave->nombre }}</td>
                                <td class="text-center">
                                    <span class="badge estado-badge {{ $llave->estadoBadgeClass }}" data-estado="{{ $llave->estado }}">
                                        {{ $llave->estadoEntregaText }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <small class="text-muted ultima-actualizacion">
                                        {{ $llave->updated_at->format('d/m/Y H:i:s') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-link text-info p-0 me-2 btn-editar"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarLlave-{{ $llave->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    
                                    <button type="button" class="btn btn-link text-info p-0" data-bs-toggle="modal" data-bs-target="#modalConfirmacionEliminar-{{ $llave->id }}" aria-label="Eliminar Llave">
                                            <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            @endif
                            
                        </tr>

                        <div class="modal fade" id="modalEditarLlave-{{ $llave->id }}" tabindex="-1" aria-labelledby="modalEditarLlaveLabel-{{ $llave->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header modal-header-custom">
                                        <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                                            <i class="bi bi-arrow-left"></i>
                                        </button>
                                        <h5 class="modal-title">Editar Llave</h5>
                                    </div>
                                    <div class="modal-body px-4 py-4">
                                        <div class="card text-bg-light">
                                        <form action="{{ route('llave.update',['llave'=>$llave]) }}" method="post">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="id" id="editarIdLlave">
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label for="editarNombreLlave" class="form-label fw-bold">Nombre de la Llave</label>
                                                        <input type="text" name="nombre" id="nombre" class="form-control"
                                                value="{{old('nombre',$llave->nombre)}}">
                                                    </div>
                                                </div>
                                                <div class="card-footer text-center">
                                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                            <!-- Modal eliminar -->
                        <div class="modal fade" id="modalConfirmacionEliminar-{{ $llave->id }}" tabindex="-1" aria-labelledby="modalLlaveEliminarLabel-{{ $llave->id }}" 
                        aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content custom-modal">
                                    <div class="modal-body text-center">
                                        <div class="icon-container">
                                            <div class="circle-icon">
                                            <i class="bi bi-exclamation-circle"></i>
                                            </div>
                                        </div>
                                        <p class="modal-text">¿Desea Eliminar la Llave?</p>
                                        <div class="btn-group-custom">
                                            <form action="{{ route('llave.destroy', ['llave' => $llave->id]) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-custom {{ $llave->condicion == 1 }}">Sí</button>
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
                                    <p class="mb-0">Llave eliminada con éxito</p>
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
$(document).ready(function() {
    console.log('🔧 Sistema de Llaves - Tiempo Real Iniciado');
    
    let pollingInterval;
    let lastUpdate = '';
    
    // Inicializar sistema de tiempo real
    function initRealTimeSystem() {
        console.log('🚀 Iniciando polling de llaves cada 2 segundos');
        updateLlavesRealTime();
        pollingInterval = setInterval(updateLlavesRealTime, 2000); // Cada 2 segundos
    }
    
    // Actualizar llaves en tiempo real
    function updateLlavesRealTime() {
        console.log('🔄 Actualizando estado de llaves...');
        
        $.ajax({
            url: '{{ route("admin.llaves.realtime") }}',
            method: 'GET',
            timeout: 5000,
            cache: false,
            success: function(response) {
                if (response.status === 'success') {
                    console.log('✅ Datos recibidos:', response.llaves.length, 'llaves');
                    
                    response.llaves.forEach(function(llave) {
                        updateLlaveRow(llave);
                    });
                    
                    // Mostrar indicador de última actualización (comentado para ocultar mensaje)
                    // showUpdateIndicator(response.timestamp);
                } else {
                    console.warn('⚠️ Respuesta sin datos:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error al actualizar llaves:', error);
                
                // Si hay error, reducir frecuencia de polling
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    setTimeout(() => {
                        pollingInterval = setInterval(updateLlavesRealTime, 5000); // Reducir a 5 segundos
                    }, 5000);
                }
            }
        });
    }
    
    // Actualizar fila individual de llave
    function updateLlaveRow(llave) {
        const row = $(`#llave-row-${llave.id}`);
        if (row.length === 0) {
            console.log(`⚠️ Fila no encontrada para llave ID: ${llave.id}`);
            return;
        }
        
        const estadoBadge = row.find('.estado-badge');
        const ultimaActualizacion = row.find('.ultima-actualizacion');
        
        // Verificar si el estado cambió
        const currentEstado = estadoBadge.data('estado');
        if (currentEstado !== llave.estado) {
            console.log(`🔄 Estado cambiado para ${llave.nombre}: ${currentEstado} → ${llave.estado}`);
            
            // Actualizar badge de estado con animación
            estadoBadge.removeClass('bg-success bg-warning text-dark');
            estadoBadge.addClass(llave.estado_badge_class);
            estadoBadge.text(llave.estado_texto);
            estadoBadge.data('estado', llave.estado);
            
            // Agregar animación de cambio
            estadoBadge.addClass('estado-actualizado');
            row.addClass('fila-actualizada');
            
            // Remover animación después de un tiempo
            setTimeout(() => {
                estadoBadge.removeClass('estado-actualizado');
                row.removeClass('fila-actualizada');
            }, 2000);
            
            // Mostrar notificación
            showToast(`🔑 ${llave.nombre}: ${llave.estado_texto}`, 'info', 3000);
        }
        
        // Actualizar tiempo de última actualización
        ultimaActualizacion.text(llave.ultima_actualizacion);
    }
    
    // Mostrar indicador de actualización
    function showUpdateIndicator(timestamp) {
        if (lastUpdate !== timestamp) {
            lastUpdate = timestamp;
            
            // Agregar indicador visual en la esquina superior derecha
            let indicator = $('#update-indicator');
            if (indicator.length === 0) {
                $('body').append(`
                    <div id="update-indicator" class="position-fixed top-0 end-0 m-3 p-2 bg-success text-white rounded-pill" style="z-index: 9999; opacity: 0;">
                        <i class="bi bi-arrow-clockwise"></i> Actualizado
                    </div>
                `);
                indicator = $('#update-indicator');
            }
            
            indicator.stop().animate({opacity: 1}, 200).delay(1000).animate({opacity: 0}, 500);
        }
    }
    
    // Función para mostrar notificaciones toast
    function showToast(message, type = 'info', duration = 3000) {
        const toastTypes = {
            success: 'bg-success',
            error: 'bg-danger',
            warning: 'bg-warning',
            info: 'bg-info'
        };
        
        const toastClass = toastTypes[type] || 'bg-info';
        const toastId = 'toast-' + Date.now();
        
        const toastHTML = `
            <div id="${toastId}" class="toast align-items-center text-white ${toastClass} border-0 mb-2" role="alert" style="opacity: 0;">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="$('#${toastId}').remove()"></button>
                </div>
            </div>
        `;
        
        // Agregar al contenedor de toasts
        let container = $('.toast-container');
        if (container.length === 0) {
            $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>');
            container = $('.toast-container');
        }
        
        container.append(toastHTML);
        const toast = $(`#${toastId}`);
        
        // Mostrar con animación
        toast.animate({opacity: 1}, 300);
        
        // Auto-remover después del tiempo especificado
        setTimeout(() => {
            toast.animate({opacity: 0}, 300, function() {
                $(this).remove();
            });
        }, duration);
    }
    
    // Inicializar sistema al cargar la página
    initRealTimeSystem();
    
    // Limpiar interval al salir de la página
    $(window).on('beforeunload', function() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }
    });
    
    console.log('✅ Sistema de tiempo real configurado correctamente');
});
</script>

<style>
/* Animaciones para cambios de estado */
.estado-actualizado {
    animation: pulso 1s ease-in-out;
}

.fila-actualizada {
    background-color: #e3f2fd !important;
    transition: background-color 2s ease-out;
}

@keyframes pulso {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Indicador de actualización */
#update-indicator {
    font-size: 0.8rem;
    transition: opacity 0.3s ease;
}
</style>
@endpush