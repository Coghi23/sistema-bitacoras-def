@extends('Template-profesor')

@section('title', 'Gestión de Llaves - Profesor')

@section('content')
<style>
/* Diseño especial para móviles - Vista de Llaves */
@media (max-width: 991px) {
    .main-content {
        padding: 0.75rem !important;
    }
    
    .header-mobile {
        background: linear-gradient(135deg, #134496 0%, #1e5bb8 100%);
        border-radius: 12px;
        padding: 1rem;
        color: white;
        margin-bottom: 1rem;
        box-shadow: 0 4px 8px rgba(19, 68, 150, 0.2);
    }
    
    .header-mobile h2 {
        font-size: 1.3rem !important;
        margin-bottom: 0.5rem;
    }
    
    .header-mobile .badge {
        background: rgba(255, 255, 255, 0.2) !important;
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
        border-radius: 20px;
    }
    
    /* Instrucciones compactas para móvil */
    .alert-info-mobile {
        background: #e8f4fd !important;
        border: none;
        border-radius: 12px;
        padding: 1rem;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }
    
    .alert-info-mobile .collapse-toggle {
        color: #134496;
        text-decoration: none;
        font-weight: bold;
        border: none !important;
        background: none !important;
        padding: 0 !important;
    }
    
    .alert-info-mobile ul {
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }
    
    /* Tarjetas de recinto optimizadas para móvil */
    .recinto-card-mobile {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }
    
    .recinto-header-mobile {
        background: linear-gradient(135deg, #134496 0%, #1e5bb8 100%);
        color: white;
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .recinto-header-mobile h6 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
    }
    
    .recinto-header-mobile .building-icon {
        font-size: 1.2rem;
        opacity: 0.8;
    }
    
    .recinto-body-mobile {
        padding: 1.25rem;
    }
    
    /* Info grid para móvil */
    .info-grid-mobile {
        display: grid;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .info-item-mobile {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 0.75rem;
        border-left: 3px solid #134496;
    }
    
    .info-item-mobile .label {
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 500;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
    }
    
    .info-item-mobile .label i {
        margin-right: 0.5rem;
        font-size: 0.9rem;
    }
    
    .info-item-mobile .value {
        font-weight: 600;
        font-size: 0.95rem;
        color: #212529;
    }
    
    /* Estado badge especial */
    .estado-badge-mobile {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    /* Próxima acción destacada */
    .proxima-accion-mobile {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 1.5rem;
    }
    
    .proxima-accion-mobile .titulo {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.25rem;
        font-weight: 500;
    }
    
    .proxima-accion-mobile .accion {
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
    }
    
    .proxima-accion-mobile .accion i {
        margin-right: 0.5rem;
        font-size: 1rem;
    }
    
    /* Botón QR especial para móvil */
    .btn-qr-mobile {
        width: 100%;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        border-radius: 12px;
        padding: 1rem;
        color: white;
        font-weight: 600;
        font-size: 1rem;
        box-shadow: 0 3px 6px rgba(40, 167, 69, 0.3);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .btn-qr-mobile:hover, .btn-qr-mobile:active, .btn-qr-mobile:focus {
        background: linear-gradient(135deg, #218838 0%, #1dc490 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
        color: white !important;
    }
    
    .btn-qr-mobile i {
        font-size: 1.2rem;
    }
    
    /* Sin llave asignada */
    .sin-llave-mobile {
        text-align: center;
        padding: 2rem 1rem;
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 50%);
        border-radius: 12px;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .sin-llave-mobile i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        opacity: 0.7;
        color: #f39c12;
    }
    
    .sin-llave-mobile strong {
        display: block;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .sin-llave-mobile .text-muted {
        font-size: 0.9rem;
        color: #856404 !important;
        opacity: 0.8;
    }
    
    /* Estado vacío optimizado para móvil */
    .estado-vacio-mobile {
        text-align: center;
        padding: 3rem 1.5rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 16px;
        margin: 1rem 0;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid #e9ecef;
    }
    
    .estado-vacio-mobile i {
        font-size: 4rem;
        color: #6c757d;
        margin-bottom: 1.5rem;
        opacity: 0.6;
    }
    
    .estado-vacio-mobile h4 {
        color: #495057;
        font-size: 1.3rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    
    .estado-vacio-mobile p {
        color: #6c757d;
        font-size: 1rem;
        line-height: 1.6;
        margin-bottom: 1.5rem;
        max-width: 300px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .estado-vacio-mobile .alert {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 50%);
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        color: #856404;
        font-size: 0.9rem;
        padding: 0.75rem;
        margin-top: 1.5rem;
    }
    
    /* Modal optimizado para móvil */
    .modal-mobile .modal-dialog {
        margin: 1rem;
        max-width: calc(100% - 2rem);
    }
    
    .modal-mobile .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .modal-mobile .modal-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-radius: 16px 16px 0 0;
        padding: 1.25rem;
    }
    
    .modal-mobile #qrcode img {
        max-width: 180px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .modal-mobile .alert {
        border-radius: 8px;
        font-size: 0.9rem;
    }
    
    /* Ocultar diseño desktop en móvil */
    .desktop-only {
        display: none !important;
    }
    
    /* Ajustar la clase mobile-only */
    .mobile-only {
        display: block !important;
    }
}

/* Mostrar solo en desktop (pantallas grandes) */
@media (min-width: 992px) {
    .mobile-only {
        display: none !important;
    }
    
    .desktop-only {
        display: block !important;
    }
}

/* Toast container para móvil */
@media (max-width: 991px) {
    .toast-container {
        top: 10px !important;
        right: 10px !important;
        left: 10px !important;
        width: auto !important;
        z-index: 1055;
    }
    
    .toast {
        border-radius: 8px;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
}

/* Ajustes adicionales para pantallas muy pequeñas */
@media (max-width: 576px) {
    .main-content {
        padding: 0.5rem !important;
    }
    
    .header-mobile {
        padding: 0.75rem;
        margin-bottom: 0.75rem;
    }
    
    .header-mobile h2 {
        font-size: 1.1rem !important;
    }
    
    .recinto-card-mobile {
        margin-bottom: 1rem;
    }
    
    .recinto-body-mobile {
        padding: 1rem;
    }
    
    .estado-vacio-mobile {
        padding: 2rem 1rem;
    }
    
    .estado-vacio-mobile h4 {
        font-size: 1.1rem;
    }
    
    .estado-vacio-mobile p {
        font-size: 0.9rem;
    }
}
</style>

<div id="llaves-container" class="wrapper">
    <div id="main-content" class="main-content">
        {{-- Header Desktop --}}
        <div class="desktop-only d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Gestión de Llaves</h2>
            <div class="badge bg-info fs-6">
                <i class="bi bi-person"></i> {{ $profesor->usuario->name }}
            </div>
        </div>

        {{-- Header Mobile --}}
        <div class="mobile-only header-mobile">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="bi bi-key-fill me-2"></i>Mis Llaves
                    </h2>
                </div>
                <div class="badge">
                    <i class="bi bi-person me-1"></i>{{ $profesor->usuario->name }}
                </div>
            </div>
        </div>

        {{-- Instrucciones Desktop --}}
        <div class="desktop-only alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Instrucciones:</strong> Aquí aparecen únicamente los recintos asignados a ti según tu horario.
            Para cada recinto, puedes generar un código QR temporal que:
            <ul class="mb-0 mt-2">
                <li><strong>Al primer escaneo:</strong> Cambia el estado de la llave a "Entregada" (retiras la llave)</li>
                <li><strong>Al segundo escaneo:</strong> Cambia el estado a "No Entregada" (devuelves la llave)</li>
                <li><strong>Expira en 30 minutos</strong> después de generarlo</li>
            </ul>
        </div>

        {{-- Instrucciones Mobile --}}
        <div class="mobile-only alert-info-mobile">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>¿Cómo funciona?</strong>
                </div>
                <button class="collapse-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#instruccionesMobile">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
            <div class="collapse" id="instruccionesMobile">
                <ul class="mb-0 mt-2">
                    <li><strong>Primer escaneo:</strong> Retiras la llave</li>
                    <li><strong>Segundo escaneo:</strong> Devuelves la llave</li>
                    <li><strong>Expira en 30 minutos</strong></li>
                </ul>
            </div>
        </div>

        {{-- Lista de Recintos Desktop --}}
        <div class="desktop-only row">
            @foreach($recintos as $recinto)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-building"></i> {{ $recinto->nombre }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong><i class="bi bi-key"></i> Llave:</strong>
                                {{ $recinto->llave->nombre ?? 'Sin llave asignada' }}
                            </div>
                           
                            @if($recinto->llave)
                                <div class="mb-3">
                                    <strong><i class="bi bi-info-circle"></i> Estado actual:</strong>
                                    <span class="badge {{ $recinto->llave->estadoBadgeClass }}">
                                        {{ $recinto->llave->estadoEntregaText }}
                                    </span>
                                </div>

                                <div class="mb-3">
                                    <strong><i class="bi bi-building-gear"></i> Tipo:</strong>
                                    {{ $recinto->tipoRecinto->nombre ?? 'N/A' }}
                                </div>

                                <div class="alert alert-light p-2 mb-3">
                                    <small class="text-muted">
                                        <strong>Próxima acción:</strong><br>
                                        @if($recinto->llave->estaDisponible())
                                            <span class="text-success">Al escanear → Retirar llave</span>
                                        @else
                                            <span class="text-warning">Al escanear → Devolver llave</span>
                                        @endif
                                    </small>
                                </div>

                                <div class="text-center">
                                    <button class="btn btn-success btn-generar-qr"
                                            data-recinto-id="{{ $recinto->id }}"
                                            data-recinto-nombre="{{ $recinto->nombre }}">
                                        <i class="bi bi-qr-code"></i> Generar QR
                                    </button>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    No hay llave asignada a este recinto
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Lista de Recintos Mobile --}}
        <div class="mobile-only">
            @foreach($recintos as $recinto)
                <div class="recinto-card-mobile">
                    <div class="recinto-header-mobile">
                        <div>
                            <h6>{{ $recinto->nombre }}</h6>
                        </div>
                        <i class="bi bi-building building-icon"></i>
                    </div>
                    
                    <div class="recinto-body-mobile">
                        @if($recinto->llave)
                            <div class="info-grid-mobile">
                                <div class="info-item-mobile">
                                    <div class="label">
                                        <i class="bi bi-key"></i>Llave
                                    </div>
                                    <div class="value">{{ $recinto->llave->nombre }}</div>
                                </div>
                                
                                <div class="info-item-mobile">
                                    <div class="label">
                                        <i class="bi bi-info-circle"></i>Estado
                                    </div>
                                    <div class="value">
                                        <span class="estado-badge-mobile {{ $recinto->llave->estadoBadgeClass }}">
                                            {{ $recinto->llave->estadoEntregaText }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="info-item-mobile">
                                    <div class="label">
                                        <i class="bi bi-building-gear"></i>Tipo
                                    </div>
                                    <div class="value">{{ $recinto->tipoRecinto->nombre ?? 'N/A' }}</div>
                                </div>
                            </div>

                            <div class="proxima-accion-mobile">
                                <div class="titulo">Próxima acción:</div>
                                <div class="accion">
                                    @if($recinto->llave->estaDisponible())
                                        <i class="bi bi-arrow-down-circle text-success"></i>
                                        <span class="text-success">Retirar llave del aula</span>
                                    @else
                                        <i class="bi bi-arrow-up-circle text-warning"></i>
                                        <span class="text-warning">Devolver llave al aula</span>
                                    @endif
                                </div>
                            </div>

                            <button class="btn-qr-mobile btn-generar-qr"
                                    data-recinto-id="{{ $recinto->id }}"
                                    data-recinto-nombre="{{ $recinto->nombre }}">
                                <i class="bi bi-qr-code"></i>
                                Generar Código QR
                            </button>
                        @else
                            <div class="sin-llave-mobile">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Sin llave asignada</strong>
                                <div class="text-muted mt-1">Este recinto no tiene llave configurada</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Estado vacío Desktop --}}
        @if($recintos->isEmpty())
            <div class="desktop-only text-center py-5">
                <i class="bi bi-building-x" style="font-size: 4rem; color: #6c757d;"></i>
                <h4 class="mt-3 text-muted">No tienes recintos asignados</h4>
                <p class="text-muted">
                    No hay recintos asignados a tu horario para hoy.<br>
                    Contacta al administrador si crees que esto es un error.
                </p>
                <div class="alert alert-warning mt-3">
                    <strong>Nota:</strong> Solo aparecen los recintos donde tienes clases programadas según tu horario.
                </div>
            </div>
        @endif

        {{-- Estado vacío Mobile --}}
        @if($recintos->isEmpty())
            <div class="mobile-only estado-vacio-mobile">
                <i class="bi bi-building-x"></i>
                <h4>Sin recintos asignados</h4>
                <p>
                    No hay recintos asignados a tu horario para hoy.<br>
                    Contacta al administrador si crees que esto es un error.
                </p>
                <div class="alert">
                    <strong>Nota:</strong> Solo aparecen los recintos donde tienes clases programadas según tu horario.
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal QR Mobile --}}
<div class="modal fade mobile-only modal-mobile" id="modalQR" tabindex="-1" aria-labelledby="modalQRLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalQRLabel">
                    <i class="bi bi-qr-code"></i> Código QR Generado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qr-info" class="mb-4">
                    <h6 id="recinto-nombre"></h6>
                    <p class="text-muted" id="profesor-nombre">Profesor: {{ $profesor->usuario->name }}</p>
                </div>
               
                <div id="qr-container" class="mb-4">
                    <div id="qrcode" class="d-flex justify-content-center mb-3"></div>
                    <div class="alert alert-info">
                        <strong>Código:</strong> <span id="qr-text"></span>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="bi bi-clock"></i>
                    <strong>Expira a las:</strong> <span id="expira-tiempo"></span>
                </div>

                <div class="alert alert-success">
                    <i class="bi bi-info-circle"></i>
                    <strong>¿Cómo usar el QR?</strong><br>
                    <small>
                        1. Lleva tu celular al aula/recinto<br>
                        2. Escanea este QR para retirar la llave<br>
                        3. Al finalizar, escanea de nuevo para devolverla
                    </small>
                </div>

                <div class="d-grid">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal QR Desktop --}}
<div class="modal fade desktop-only" id="modalQRDesktop" tabindex="-1" aria-labelledby="modalQRDesktopLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalQRDesktopLabel">
                    <i class="bi bi-qr-code"></i> Código QR Generado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qr-info-desktop" class="mb-4">
                    <h6 id="recinto-nombre-desktop"></h6>
                    <p class="text-muted" id="profesor-nombre-desktop">Profesor: {{ $profesor->usuario->name }}</p>
                </div>
               
                <div id="qr-container-desktop" class="mb-4">
                    <div id="qrcode-desktop" class="d-flex justify-content-center mb-3"></div>
                    <div class="alert alert-info">
                        <strong>Código:</strong> <span id="qr-text-desktop"></span>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="bi bi-clock"></i>
                    <strong>Expira a las:</strong> <span id="expira-tiempo-desktop"></span>
                </div>

                <div class="alert alert-success">
                    <i class="bi bi-info-circle"></i>
                    <strong>¿Cómo usar el QR?</strong><br>
                    <small>
                        1. Lleva tu celular al aula/recinto<br>
                        2. Escanea este QR para retirar la llave<br>
                        3. Al finalizar, escanea de nuevo para devolverla
                    </small>
                </div>

                <div class="d-grid">
                    <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast container --}}
<div class="toast-container"></div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
$(document).ready(function() {
    $('.btn-generar-qr').click(function() {
        const button = $(this);
        const recintoId = button.data('recinto-id');
        const recintoNombre = button.data('recinto-nombre');
        const isMobile = window.innerWidth <= 991;
       
        button.prop('disabled', true);
        
        if (button.hasClass('btn-qr-mobile')) {
            button.html('<i class="spinner-border spinner-border-sm me-2"></i>Generando...');
        } else {
            button.html('<i class="spinner-border spinner-border-sm"></i> Generando...');
        }
       
        $.ajax({
            url: '{{ route("qr.generar") }}',
            method: 'POST',
            data: {
                recinto_id: recintoId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    const modalId = isMobile ? '#modalQR' : '#modalQRDesktop';
                    const suffix = isMobile ? '' : '-desktop';
                    
                    // Actualizar modal con información
                    $(`#recinto-nombre${suffix}`).text(recintoNombre);
                    $(`#qr-text${suffix}`).text(response.qr_code);
                    $(`#expira-tiempo${suffix}`).text(response.expira_en);
                   
                    // Limpiar QR anterior
                    $(`#qrcode${suffix}`).empty();
                   
                    // Mostrar QR usando API externa
                    const qrImg = $('<img>')
                        .attr('src', response.qr_url)
                        .attr('alt', 'QR Code')
                        .addClass('img-fluid')
                        .css('max-width', isMobile ? '180px' : '200px');
                   
                    $(`#qrcode${suffix}`).append(qrImg);
                   
                    // Mostrar modal correspondiente
                    $(modalId).modal('show');
                   
                    // Mostrar mensaje de éxito
                    if (response.mensaje) {
                        showToast(response.mensaje, 'success');
                    }
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.error || 'Error al generar QR';
                showToast(error, 'error');
            },
            complete: function() {
                button.prop('disabled', false);
                if (button.hasClass('btn-qr-mobile')) {
                    button.html('<i class="bi bi-qr-code me-2"></i>Generar Código QR');
                } else {
                    button.html('<i class="bi bi-qr-code"></i> Generar QR');
                }
            }
        });
    });

    // Manejar colapso de instrucciones móviles
    $('.collapse-toggle').click(function() {
        const icon = $(this).find('i');
        if (icon.hasClass('bi-chevron-down')) {
            icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
        } else {
            icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
        }
    });
});

function showToast(message, type) {
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
   
    $('.toast-container').append(toast);
    toast.toast('show');
   
    setTimeout(() => toast.remove(), 5000);
}
</script>
@endpush

@push('styles')
<style>
/* Estilos base para desktop */
.desktop-only .card {
    transition: transform 0.2s;
}

.desktop-only .card:hover {
    transform: translateY(-2px);
}

.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1055;
}
</style>
@endpush


