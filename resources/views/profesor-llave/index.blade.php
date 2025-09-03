@extends('Template-profesor')


@section('title', 'Gestión de Llaves - Profesor')


@section('content')
<style>
.spin {
    animation: spin 1s linear infinite;
}


@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>


<div class="wrapper">
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Gestión de Llaves</h2>
            <div class="d-flex align-items-center gap-3">
                @if(isset($profesor) && $profesor)
                    <div class="badge bg-info fs-6">
                        <i class="bi bi-person"></i> {{ $profesor->usuario->name }}
                    </div>
                @endif
                <!-- Botón manual para debug -->
                <button id="btn-actualizar-qrs" class="btn btn-outline-secondary btn-sm" title="Actualizar QRs manualmente">
                    <i class="bi bi-arrow-clockwise"></i> Debug QRs
                </button>
                <!-- Botón para escanear QR -->
                <a href="{{ route('profesor-llave.scanner') }}" class="btn btn-primary">
                    <i class="bi bi-camera"></i> Escanear QR
                </a>
            </div>
        </div>


        @if(isset($error))
            <div class="alert alert-warning">
                <h4><i class="bi bi-exclamation-triangle"></i> Atención</h4>
                <p>{{ $error }}</p>
            </div>
        @endif


        @if(!isset($error) && $recintos->count() > 0)
            <div class="row">
                @foreach($recintos as $item)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card shadow-sm border-primary">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="bi bi-building"></i> {{ $item->recinto_nombre }}
                                    </h6>
                                    <span class="badge {{ $item->llave_estado == 0 ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ $item->llave_estado == 0 ? 'Entregada' : 'No Entregada' }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong><i class="bi bi-key"></i> Llave:</strong>
                                    <span class="text-primary">{{ $item->llave_nombre }}</span>
                                </div>
                               
                                <div class="text-center">
                                    <button class="btn btn-success btn-generar-qr"
                                            data-recinto-id="{{ $item->recinto_id }}"
                                            data-llave-id="{{ $item->llave_id }}"
                                            data-recinto-nombre="{{ $item->recinto_nombre }}"
                                            data-llave-nombre="{{ $item->llave_nombre }}">
                                        <i class="bi bi-qr-code"></i> Generar QR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif


        @if(!isset($error) && $recintos->count() == 0 && isset($profesor))
            <div class="text-center py-5">
                <i class="bi bi-building" style="font-size: 4rem; color: #6c757d;"></i>
                <h4 class="mt-3 text-muted">No tienes recintos asignados</h4>
                <p class="text-muted">Contacta al administrador para que te asigne recintos en tus horarios.</p>
            </div>
        @endif


        <!-- QRs Temporales Activos -->
        @if(!isset($error) && $qrsTemporales->count() > 0)
            <div class="mt-5">
                <h4><i class="bi bi-clock-history"></i> QRs Temporales Activos</h4>
                <div class="row" id="qrs-container">
                    @foreach($qrsTemporales as $qr)
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $qr->codigo_qr }}</h6>
                                    <p class="card-text">
                                        <strong>Recinto:</strong> {{ $qr->recinto_nombre }}<br>
                                        <strong>Llave:</strong> {{ $qr->llave_nombre }}<br>
                                        <strong>Expira:</strong> {{ \Carbon\Carbon::parse($qr->expira_en)->format('d/m/Y H:i') }}
                                    </p>
                                    <button class="btn btn-outline-primary btn-sm btn-ver-qr"
                                            data-qr-code="{{ $qr->codigo_qr }}"
                                            data-recinto-nombre="{{ $qr->recinto_nombre }}"
                                            data-llave-nombre="{{ $qr->llave_nombre }}">
                                        <i class="bi bi-eye"></i> Ver QR
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>


<!-- Modal para mostrar QR -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="qrModalLabel">
                    <i class="bi bi-qr-code"></i> Código QR Generado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qr-info" class="mb-3">
                    <p><strong>Recinto:</strong> <span id="modal-recinto"></span></p>
                    <p><strong>Llave:</strong> <span id="modal-llave"></span></p>
                    <p><strong>Código:</strong> <span id="modal-codigo"></span></p>
                </div>
               
                <div id="qr-image-container">
                    <img id="qr-image" src="" alt="Código QR" style="max-width: 100%; height: auto;">
                </div>
               
                <div class="mt-3">
                    <small class="text-muted">El código QR expira en 30 minutos</small>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection


@push('scripts')
<script>
$(document).ready(function() {
    // Generar QR
    $('.btn-generar-qr').click(function() {
        const button = $(this);
        const recintoId = button.data('recinto-id');
        const llaveId = button.data('llave-id');
        const recintoNombre = button.data('recinto-nombre');
        const llaveNombre = button.data('llave-nombre');
       
        button.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Generando...');
       
        $.ajax({
            url: '{{ route("profesor-llave.generar-qr") }}',
            method: 'POST',
            data: {
                recinto_id: recintoId,
                llave_id: llaveId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar modal
                    $('#modal-recinto').text(recintoNombre);
                    $('#modal-llave').text(llaveNombre);
                    $('#modal-codigo').text(response.codigo_qr);
                    $('#qr-image').attr('src', response.qr_url);
                   
                    // Mostrar mensaje informativo si existe
                    if (response.mensaje) {
                        // Crear o actualizar mensaje informativo en el modal
                        let messageHtml = '';
                        if (response.mensaje.includes('Ya existe')) {
                            messageHtml = `<div class="alert alert-info alert-dismissible fade show mt-3" role="alert">
                                <i class="bi bi-info-circle"></i> ${response.mensaje}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>`;
                        } else {
                            messageHtml = `<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                                <i class="bi bi-check-circle"></i> ${response.mensaje}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>`;
                        }
                       
                        // Remover mensaje anterior si existe
                        $('#qrModal .modal-body .alert').remove();
                        // Agregar nuevo mensaje
                        $('#qrModal .modal-body').append(messageHtml);
                    }
                   
                    // Mostrar modal
                    $('#qrModal').modal('show');
                   
                    // Recargar página después de cerrar modal
                    $('#qrModal').on('hidden.bs.modal', function () {
                        location.reload();
                    });
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Error al generar QR';
                alert('Error: ' + error);
            },
            complete: function() {
                button.prop('disabled', false).html('<i class="bi bi-qr-code"></i> Generar QR');
            }
        });
    });
   
    // Ver QR existente
    $('.btn-ver-qr').click(function() {
        const qrCode = $(this).data('qr-code');
        const recintoNombre = $(this).data('recinto-nombre');
        const llaveNombre = $(this).data('llave-nombre');
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${qrCode}`;
       
        // Llenar información del modal
        $('#modal-recinto').text(recintoNombre);
        $('#modal-llave').text(llaveNombre);
        $('#modal-codigo').text(qrCode);
        $('#qr-image').attr('src', qrUrl);
       
        // Mostrar modal
        $('#qrModal').modal('show');
    });
   
    // ===== SISTEMA DE TIEMPO REAL =====
    let pollingInterval;
   
    function initRealTimeSystem() {
        console.log('🚀 Iniciando sistema de tiempo real - QRs cada 5 segundos');
        pollingInterval = setInterval(function() {
            updateQRsRealTime();
        }, 5000);
    }
   
    function updateQRsRealTime() {
        console.log('🔄 Actualizando QRs del profesor...');
       
        $.ajax({
            url: '{{ route("profesor-llave.qrs-realtime") }}' + '?t=' + Date.now(),
            method: 'GET',
            timeout: 5000,
            cache: false,
            headers: {
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            },
            success: function(response) {
                if (response.status === 'success') {
                    console.log('✅ QRs actualizados:', response.total);
                    console.log('📋 Debug info:', response.debug);
                    console.log('📊 QRs data:', response.qrs);
                   
                    // Si no hay QRs activos, ocultar la sección
                    if (response.total === 0) {
                        $('#qrs-container').parent().hide();
                        console.log('ℹ️ No hay QRs activos, sección oculta');
                       
                        // Detener polling si no hay QRs
                        if (pollingInterval) {
                            clearInterval(pollingInterval);
                            console.log('⏹️ Polling detenido - no hay QRs activos');
                        }
                    } else {
                        // Mostrar la sección si estaba oculta
                        $('#qrs-container').parent().show();
                        // Actualizar la sección de QRs
                        updateQRsDisplay(response.qrs);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.warn('⚠️ Error actualizando QRs:', error);
                // En caso de error, continuar pero con intervalo más largo
                clearInterval(pollingInterval);
                pollingInterval = setInterval(updateQRsRealTime, 10000); // 10 segundos
            }
        });
    }
   
    function updateQRsDisplay(qrs) {
        let html = '';
       
        qrs.forEach(function(qr) {
            html += `
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="card-title">${qr.codigo_qr}</h6>
                            <p class="card-text">
                                <strong>Recinto:</strong> ${qr.recinto_nombre}<br>
                                <strong>Llave:</strong> ${qr.llave_nombre}<br>
                                <small class="text-muted">Expira: ${qr.expira_en_humano}</small>
                            </p>
                            <button class="btn btn-primary btn-sm btn-ver-qr"
                                    data-qr-code="${qr.codigo_qr}"
                                    data-recinto-nombre="${qr.recinto_nombre}"
                                    data-llave-nombre="${qr.llave_nombre}">
                                <i class="bi bi-eye"></i> Ver QR
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
       
        $('#qrs-container').html(html);
       
        // Reactivar eventos para los botones ver QR
        bindVerQREvents();
    }
   
    function bindVerQREvents() {
        $('.btn-ver-qr').off('click').on('click', function() {
            const qrCode = $(this).data('qr-code');
            const recintoNombre = $(this).data('recinto-nombre');
            const llaveNombre = $(this).data('llave-nombre');
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${qrCode}`;
           
            $('#modal-recinto').text(recintoNombre);
            $('#modal-llave').text(llaveNombre);
            $('#modal-codigo').text(qrCode);
            $('#qr-image').attr('src', qrUrl);
            $('#qrModal').modal('show');
        });
    }
   
    // Inicializar sistema de tiempo real solo si hay QRs activos
    const hasActiveQRs = $('#qrs-container').length > 0;
    if (hasActiveQRs) {
        initRealTimeSystem();
    }
   
    // Botón manual para debug
    $('#btn-actualizar-qrs').on('click', function() {
        console.log('🔧 Actualización manual de QRs...');
        $(this).find('i').addClass('spin');
        updateQRsRealTime();
       
        setTimeout(() => {
            $(this).find('i').removeClass('spin');
        }, 1000);
    });
});
</script>
@endpush


@push('styles')
<style>
/* Estilos base */
.card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.wrapper {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.main-content {
    background: #f8f9fa;
    min-height: 100vh;
    padding: 20px;
    border-radius: 10px;
}

.btn-primary {
    background-color: #134496;
    border-color: #134496;
}

.btn-primary:hover {
    background-color: #0e326c;
    border-color: #0e326c;
}

.btn-warning {
    background-color: #f5c002;
    border-color: #f5c002;
    color: #134496;
    font-weight: bold;
}

.btn-warning:hover {
    background-color: #dba600;
    border-color: #dba600;
    color: #134496;
}

.text-primary {
    color: #134496 !important;
}

.badge-success {
    background-color: #28a745;
}

.badge-danger {
    background-color: #dc3545;
}

.alert {
    border-radius: 10px;
    margin-bottom: 20px;
}

.form-control:focus {
    border-color: #f5c002;
    box-shadow: 0 0 0 0.2rem rgba(245, 192, 2, 0.25);
}

.card-header {
    background: linear-gradient(135deg, #134496 0%, #1e5bb3 100%);
    color: white;
    border-radius: 10px 10px 0 0 !important;
    border: none;
}

.card-body {
    padding: 1.5rem;
}

.table th {
    background-color: #134496;
    color: white;
    border: none;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(19, 68, 150, 0.05);
}

/* Estilos para QR codes */
.qr-container {
    padding: 15px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    text-align: center;
    margin: 10px 0;
}

.qr-code {
    margin: 10px auto;
    display: block;
}

/* Estilos móviles específicos */
@media (max-width: 768px) {
    .wrapper {
        padding: 10px;
        margin: 0;
    }

    .main-content {
        padding: 15px;
        border-radius: 0;
        margin: 0;
    }

    .card {
        margin-bottom: 15px;
        border-radius: 10px;
    }

    .card-header {
        padding: 12px 15px;
        font-size: 1rem;
    }

    .card-body {
        padding: 15px;
    }

    .btn {
        font-size: 0.9rem;
        padding: 8px 15px;
        margin: 2px;
    }

    .btn-block {
        width: 100%;
        margin-bottom: 10px;
    }

    /* Tabla responsive */
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
    }

    .table {
        font-size: 0.85rem;
        margin-bottom: 0;
    }

    .table th,
    .table td {
        padding: 8px 6px;
        text-align: center;
    }

    .table th {
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Columnas específicas para móvil */
    .table .col-recinto {
        min-width: 120px;
    }

    .table .col-estado {
        min-width: 80px;
    }

    .table .col-acciones {
        min-width: 100px;
    }

    /* QR codes en móvil */
    .qr-container {
        padding: 10px;
        margin: 5px 0;
    }

    .qr-code {
        max-width: 150px;
        height: auto;
    }

    /* Alertas y mensajes */
    .alert {
        font-size: 0.9rem;
        padding: 12px 15px;
        margin-bottom: 15px;
    }

    .alert h5 {
        font-size: 1rem;
        margin-bottom: 8px;
    }

    /* Formularios en móvil */
    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        font-size: 1rem;
        padding: 12px 15px;
    }

    .form-label {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 5px;
    }

    /* Badges y estados */
    .badge {
        font-size: 0.75rem;
        padding: 4px 8px;
    }

    /* Espaciado general */
    .mb-3 {
        margin-bottom: 15px !important;
    }

    .mt-3 {
        margin-top: 15px !important;
    }

    /* Headers */
    h1 {
        font-size: 1.5rem;
        margin-bottom: 15px;
    }

    h4 {
        font-size: 1.2rem;
        margin-bottom: 12px;
    }

    h5 {
        font-size: 1.1rem;
        margin-bottom: 10px;
    }

    /* Contenedor de botones */
    .btn-group-mobile {
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: 100%;
    }

    .btn-group-mobile .btn {
        width: 100%;
        margin: 0;
    }

    /* Toast y modales móviles */
    .toast {
        font-size: 0.9rem;
    }

    .modal-content {
        margin: 10px;
        border-radius: 15px;
    }

    .modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid #e9ecef;
    }

    /* Scroll horizontal para tablas grandes */
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-container::-webkit-scrollbar {
        height: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #134496;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #0e326c;
    }
}

/* Estilos para pantallas muy pequeñas */
@media (max-width: 480px) {
    .wrapper {
        padding: 5px;
    }

    .main-content {
        padding: 10px;
    }

    .card-body {
        padding: 12px;
    }

    .btn {
        font-size: 0.85rem;
        padding: 6px 12px;
    }

    .table {
        font-size: 0.8rem;
    }

    .table th,
    .table td {
        padding: 6px 4px;
    }

    h1 {
        font-size: 1.3rem;
    }

    h4 {
        font-size: 1.1rem;
    }

    .alert {
        font-size: 0.85rem;
        padding: 10px 12px;
    }

    .qr-code {
        max-width: 120px;
    }
}

/* Animaciones y transiciones */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fadeIn 0.5s ease-out;
}

/* Loading spinner */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #134496;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush




