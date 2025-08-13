@extends('Template-administrador')

@section('title', 'QR Temporales Activos')

@section('content')
<div class="wrapper">
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bi bi-qr-code-scan"></i> QR Temporales Activos
            </h2>
            <div class="badge bg-primary fs-6">
                Total: {{ $qrsTemporales->count() }}
            </div>
        </div>

        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Vista Administrativa:</strong> Aquí puedes ver todos los códigos QR temporales generados por los profesores que aún están activos.
        </div>

        @if($qrsTemporales->count() > 0)
            <div class="row">
                @foreach($qrsTemporales as $qr)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card shadow-sm {{ $qr->usado ? 'border-secondary' : 'border-success' }}">
                            <div class="card-header {{ $qr->usado ? 'bg-secondary' : 'bg-success' }} text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="bi bi-qr-code"></i> {{ $qr->codigo_qr }}
                                    </h6>
                                    <span class="badge {{ $qr->usado ? 'bg-light text-dark' : 'bg-warning text-dark' }}">
                                        {{ $qr->usado ? 'Usado' : 'Activo' }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong><i class="bi bi-person"></i> Profesor:</strong>
                                    <span class="text-primary">{{ $qr->profesor_nombre }}</span>
                                </div>
                                
                                <div class="mb-2">
                                    <strong><i class="bi bi-building"></i> Recinto:</strong>
                                    {{ $qr->recinto_nombre }}
                                </div>
                                
                                <div class="mb-2">
                                    <strong><i class="bi bi-key"></i> Llave:</strong>
                                    {{ $qr->llave_nombre }}
                                    <span class="badge {{ $qr->llave_estado == 0 ? 'bg-success' : 'bg-warning' }} ms-1">
                                        {{ $qr->llave_estado == 0 ? 'No Entregada' : 'Entregada' }}
                                    </span>
                                </div>
                                
                                <div class="mb-2">
                                    <strong><i class="bi bi-clock"></i> Generado:</strong>
                                    {{ \Carbon\Carbon::parse($qr->created_at)->format('d/m/Y H:i:s') }}
                                </div>
                                
                                <div class="mb-3">
                                    <strong><i class="bi bi-alarm"></i> Expira:</strong>
                                    <span class="text-{{ \Carbon\Carbon::parse($qr->expira_en) < now() ? 'danger' : 'warning' }}">
                                        {{ \Carbon\Carbon::parse($qr->expira_en)->format('d/m/Y H:i:s') }}
                                    </span>
                                </div>

                                <div class="text-center">
                                    <button class="btn btn-outline-primary btn-sm btn-ver-qr" 
                                            data-qr-code="{{ $qr->codigo_qr }}"
                                            data-profesor-nombre="{{ $qr->profesor_nombre }}"
                                            data-recinto-nombre="{{ $qr->recinto_nombre }}"
                                            data-llave-nombre="{{ $qr->llave_nombre }}">
                                        <i class="bi bi-eye"></i> Ver QR
                                    </button>
                                    
                                    @if(!$qr->usado && \Carbon\Carbon::parse($qr->expira_en) > now())
                                        <button class="btn btn-outline-danger btn-sm ms-2 btn-escanear"
                                                data-qr-code="{{ $qr->codigo_qr }}">
                                            <i class="bi bi-upc-scan"></i> Simular Escaneo
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-qr-code" style="font-size: 4rem; color: #6c757d;"></i>
                <h4 class="mt-3 text-muted">No hay códigos QR activos</h4>
                <p class="text-muted">Los profesores aún no han generado códigos QR temporales.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal Ver QR -->
<div class="modal fade" id="modalVerQR" tabindex="-1" aria-labelledby="modalVerQRLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalVerQRLabel">
                    <i class="bi bi-qr-code"></i> Código QR - Detalles
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qr-info" class="mb-3">
                    <p><strong>Profesor:</strong> <span id="modal-profesor"></span></p>
                    <p><strong>Recinto:</strong> <span id="modal-recinto"></span></p>
                    <p><strong>Llave:</strong> <span id="modal-llave"></span></p>
                    <p><strong>Código:</strong> <span id="modal-codigo"></span></p>
                </div>
                
                <div id="qr-image-container" class="mb-3">
                    <img id="qr-image" src="" alt="Código QR" style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 8px; padding: 10px;">
                </div>
                
                <div class="mt-3">
                    <small class="text-muted">Este código QR puede ser escaneado por el sistema</small>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Debug: Verificar que jQuery y Bootstrap están cargados
    console.log('jQuery cargado:', typeof $ !== 'undefined');
    console.log('Bootstrap cargado:', typeof bootstrap !== 'undefined');
    
    // Ver QR
    $('.btn-ver-qr').click(function() {
        console.log('Botón Ver QR clickeado');
        
        const button = $(this);
        const qrCode = button.data('qr-code');
        const profesor = button.data('profesor-nombre');
        const recinto = button.data('recinto-nombre');
        const llave = button.data('llave-nombre');
        
        console.log('Datos:', { qrCode, profesor, recinto, llave });
        
        // Generar URL del QR usando API externa
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=256x256&data=${encodeURIComponent(qrCode)}`;
        
        // Actualizar modal con la información
        $('#modal-profesor').text(profesor);
        $('#modal-recinto').text(recinto);
        $('#modal-llave').text(llave);
        $('#modal-codigo').text(qrCode);
        $('#qr-image').attr('src', qrUrl);
        
        // Mostrar modal
        $('#modalVerQR').modal('show');
    });

    // Simular escaneo
    $('.btn-escanear').click(function() {
        console.log('Botón Simular Escaneo clickeado');
        
        const button = $(this);
        const qrCode = button.data('qr-code');
        
        console.log('QR Code para escanear:', qrCode);
        
        if (!confirm('¿Estás seguro de que quieres simular el escaneo de este QR? Esto cambiará el estado de la llave.')) {
            return;
        }
        
        button.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Escaneando...');
        
        $.ajax({
            url: '{{ route("qr.escanear") }}',
            method: 'POST',
            data: {
                qr_code: qrCode,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                console.log('Respuesta exitosa:', response);
                if (response.success) {
                    showToast(response.mensaje, 'success');
                    // Recargar la página para ver los cambios
                    setTimeout(() => location.reload(), 1500);
                }
            },
            error: function(xhr) {
                console.log('Error en AJAX:', xhr);
                const error = xhr.responseJSON?.error || 'Error al escanear QR';
                showToast(error, 'error');
                button.prop('disabled', false).html('<i class="bi bi-upc-scan"></i> Simular Escaneo');
            }
        });
    });

    // Auto-refresh cada 30 segundos (comentado para debug)
    // setInterval(() => {
    //     location.reload();
    // }, 30000);
});

function showToast(message, type) {
    console.log('Mostrando toast:', message, type);
    
    const toast = $(`
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    if (!$('.toast-container').length) {
        $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11000;"></div>');
    }
    
    $('.toast-container').append(toast);
    toast.toast('show');
    
    setTimeout(() => toast.remove(), 5000);
}
</script>
@endpush

@push('styles')
<style>
.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1055;
}

.border-success {
    border-color: #198754 !important;
}

.border-secondary {
    border-color: #6c757d !important;
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
</style>
@endpush
