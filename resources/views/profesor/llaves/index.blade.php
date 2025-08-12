@extends('Template-profesor')

@section('title', 'Gestión de Llaves - Profesor')

@section('content')
<div class="wrapper">
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Gestión de Llaves</h2>
            <div class="badge bg-info fs-6">
                <i class="bi bi-person"></i> {{ $profesor->usuario->name }}
            </div>
        </div>

        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Instrucciones:</strong> Aquí aparecen únicamente los recintos asignados a ti según tu horario. 
            Para cada recinto, puedes generar un código QR temporal que:
            <ul class="mb-0 mt-2">
                <li><strong>Al primer escaneo:</strong> Cambia el estado de la llave a "Entregada" (retiras la llave)</li>
                <li><strong>Al segundo escaneo:</strong> Cambia el estado a "No Entregada" (devuelves la llave)</li>
                <li><strong>Expira en 30 minutos</strong> después de generarlo</li>
            </ul>
        </div>

        <div class="row">
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

        @if($recintos->isEmpty())
            <div class="text-center py-5">
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
    </div>
</div>

<!-- Modal QR -->
<div class="modal fade" id="modalQR" tabindex="-1" aria-labelledby="modalQRLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
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
                    <!-- Aquí se mostrará el QR code -->
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

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
$(document).ready(function() {
    $('.btn-generar-qr').click(function() {
        const button = $(this);
        const recintoId = button.data('recinto-id');
        const recintoNombre = button.data('recinto-nombre');
        
        button.prop('disabled', true).html('<i class="spinner-border spinner-border-sm"></i> Generando...');
        
        $.ajax({
            url: '{{ route("qr.generar") }}',
            method: 'POST',
            data: {
                recinto_id: recintoId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar modal con información
                    $('#recinto-nombre').text(recintoNombre);
                    $('#qr-text').text(response.qr_code);
                    $('#expira-tiempo').text(response.expira_en);
                    
                    // Limpiar QR anterior
                    $('#qrcode').empty();
                    
                    // Mostrar QR usando API externa
                    const qrImg = $('<img>')
                        .attr('src', response.qr_url)
                        .attr('alt', 'QR Code')
                        .addClass('img-fluid')
                        .css('max-width', '200px');
                    
                    $('#qrcode').append(qrImg);
                    
                    // Mostrar modal
                    $('#modalQR').modal('show');
                    
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
                button.prop('disabled', false).html('<i class="bi bi-qr-code"></i> Generar QR');
            }
        });
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
</style>
@endpush
