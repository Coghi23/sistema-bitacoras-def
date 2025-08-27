@extends('Template-profesor')

@section('title', 'Escáner QR - Profesor')

@section('content')
<div class="scanner-wrapper">
    <div class="scanner-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Escáner QR</h2>
            <a href="{{ route('profesor-llave.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Estado del escáner -->
        <div class="scanner-status mb-3">
            <div id="scanner-info" class="alert alert-info">
                <i class="bi bi-camera"></i> Iniciando cámara...
            </div>
        </div>

        <!-- Vista de la cámara -->
        <div class="camera-container">
            <video id="preview" class="scanner-video"></video>
            <div class="scanner-overlay">
                <div class="scanner-frame"></div>
                <p class="scanner-text">Enfoca el código QR dentro del marco</p>
            </div>
        </div>

        <!-- Controles -->
        <div class="scanner-controls mt-4">
            <div class="row g-2">
                <div class="col-6">
                    <button id="start-scan" class="btn btn-success w-100">
                        <i class="bi bi-camera"></i> Iniciar
                    </button>
                </div>
                <div class="col-6">
                    <button id="stop-scan" class="btn btn-danger w-100" disabled>
                        <i class="bi bi-camera-video-off"></i> Detener
                    </button>
                </div>
                <div class="col-12 mt-2">
                    <button id="switch-camera" class="btn btn-outline-primary w-100">
                        <i class="bi bi-arrow-repeat"></i> Cambiar Cámara
                    </button>
                </div>
            </div>
        </div>

        <!-- Resultado del escaneo -->
        <div id="scan-result" class="mt-4" style="display: none;">
            <div class="alert alert-success">
                <h5><i class="bi bi-check-circle"></i> QR Escaneado</h5>
                <p class="mb-0">Código: <strong id="scanned-code"></strong></p>
            </div>
        </div>

        <!-- Input manual como alternativa -->
        <div class="manual-input mt-4">
            <h5>Entrada Manual</h5>
            <form id="manual-form">
                <div class="input-group mb-3">
                    <input type="text" id="manual-code" class="form-control" placeholder="Ingresa el código QR manualmente">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de resultado -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="modal-header-success" style="display: none;">
                <h5 class="modal-title text-success">
                    <i class="bi bi-check-circle"></i> ¡Éxito!
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-header" id="modal-header-error" style="display: none;">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle"></i> Error
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="modal-message"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Librería QuaggaJS para escaneo QR -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
$(document).ready(function() {
    let isScanning = false;
    let currentStream = null;
    let cameras = [];
    let currentCameraIndex = 0;

    // Verificar soporte de cámara
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        $('#scanner-info').html('<i class="bi bi-exclamation-triangle"></i> Tu dispositivo no soporta acceso a la cámara').removeClass('alert-info').addClass('alert-warning');
        return;
    }

    // Inicializar escáner
    async function initScanner() {
        try {
            // Obtener lista de cámaras
            const devices = await navigator.mediaDevices.enumerateDevices();
            cameras = devices.filter(device => device.kind === 'videoinput');
            
            if (cameras.length === 0) {
                throw new Error('No se encontraron cámaras');
            }

            // Preferir cámara trasera en móviles
            const backCamera = cameras.find(camera => 
                camera.label.toLowerCase().includes('back') || 
                camera.label.toLowerCase().includes('rear') ||
                camera.label.toLowerCase().includes('trasera')
            );
            
            if (backCamera) {
                currentCameraIndex = cameras.indexOf(backCamera);
            }

            $('#scanner-info').html('<i class="bi bi-check-circle"></i> Cámara lista. Presiona "Iniciar" para escanear').removeClass('alert-info').addClass('alert-success');
            $('#start-scan').prop('disabled', false);
            
            if (cameras.length > 1) {
                $('#switch-camera').prop('disabled', false);
            }

        } catch (error) {
            console.error('Error inicializando cámara:', error);
            $('#scanner-info').html('<i class="bi bi-exclamation-triangle"></i> Error: ' + error.message).removeClass('alert-info').addClass('alert-danger');
        }
    }

    // Iniciar escaneo
    async function startScan() {
        if (isScanning) return;

        try {
            const constraints = {
                video: {
                    deviceId: cameras[currentCameraIndex] ? { exact: cameras[currentCameraIndex].deviceId } : undefined,
                    facingMode: cameras[currentCameraIndex] ? undefined : 'environment', // Preferir cámara trasera
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                }
            };

            currentStream = await navigator.mediaDevices.getUserMedia(constraints);
            const video = document.getElementById('preview');
            video.srcObject = currentStream;
            
            await video.play();
            
            // Configurar Quagga para códigos QR y códigos de barras
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#preview'),
                    constraints: constraints.video
                },
                decoder: {
                    readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "qrcode_reader"]
                }
            }, function(err) {
                if (err) {
                    console.error('Error iniciando Quagga:', err);
                    return;
                }
                Quagga.start();
                isScanning = true;
                $('#start-scan').prop('disabled', true);
                $('#stop-scan').prop('disabled', false);
                $('#scanner-info').html('<i class="bi bi-search"></i> Escaneando... Enfoca el código QR').removeClass().addClass('alert alert-primary');
            });

            // Escuchar detecciones
            Quagga.onDetected(function(data) {
                const code = data.codeResult.code;
                console.log('Código detectado:', code);
                
                // Mostrar código detectado
                $('#scanned-code').text(code);
                $('#scan-result').show();
                
                // Procesar código automáticamente
                setTimeout(() => {
                    processQRCode(code);
                }, 500);
            });

        } catch (error) {
            console.error('Error iniciando escáner:', error);
            $('#scanner-info').html('<i class="bi bi-exclamation-triangle"></i> Error: ' + error.message).removeClass().addClass('alert alert-danger');
        }
    }

    // Detener escaneo
    function stopScan() {
        if (isScanning) {
            Quagga.stop();
            isScanning = false;
        }
        
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
        
        $('#start-scan').prop('disabled', false);
        $('#stop-scan').prop('disabled', true);
        $('#scanner-info').html('<i class="bi bi-camera"></i> Escáner detenido').removeClass().addClass('alert alert-secondary');
        $('#scan-result').hide();
    }

    // Cambiar cámara
    function switchCamera() {
        if (cameras.length <= 1) return;
        
        stopScan();
        currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
        
        setTimeout(() => {
            startScan();
        }, 500);
    }

    // Procesar código QR
    async function processQRCode(code) {
        stopScan(); // Detener escaneo mientras procesamos
        
        try {
            const response = await $.ajax({
                url: '{{ route("profesor-llave.escanear-qr") }}',
                method: 'POST',
                data: {
                    qr_code: code,
                    _token: '{{ csrf_token() }}'
                }
            });

            if (response.success) {
                showModal(true, response.mensaje);
                // Redirigir después de 3 segundos
                setTimeout(() => {
                    window.location.href = '{{ route("profesor-llave.index") }}';
                }, 3000);
            } else {
                showModal(false, response.error || 'Error desconocido');
                // Reiniciar escáner después del error
                setTimeout(startScan, 2000);
            }

        } catch (xhr) {
            const errorMsg = xhr.responseJSON?.error || 'Error de conexión';
            showModal(false, errorMsg);
            // Reiniciar escáner después del error
            setTimeout(startScan, 2000);
        }
    }

    // Mostrar modal de resultado
    function showModal(success, message) {
        if (success) {
            $('#modal-header-success').show();
            $('#modal-header-error').hide();
        } else {
            $('#modal-header-success').hide();
            $('#modal-header-error').show();
        }
        
        $('#modal-message').text(message);
        $('#resultModal').modal('show');
    }

    // Event listeners
    $('#start-scan').click(startScan);
    $('#stop-scan').click(stopScan);
    $('#switch-camera').click(switchCamera);
    
    // Formulario manual
    $('#manual-form').submit(function(e) {
        e.preventDefault();
        const code = $('#manual-code').val().trim();
        if (code) {
            processQRCode(code);
        }
    });

    // Inicializar al cargar
    initScanner();

    // Limpiar recursos al salir
    $(window).on('beforeunload', function() {
        stopScan();
    });
});
</script>
@endpush

@push('styles')
<style>
.scanner-wrapper {
    padding: 20px;
    max-width: 600px;
    margin: 0 auto;
    min-height: 100vh;
}

.scanner-container {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.camera-container {
    position: relative;
    background: #000;
    border-radius: 10px;
    overflow: hidden;
    aspect-ratio: 4/3;
    max-height: 400px;
}

.scanner-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.scanner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    pointer-events: none;
}

.scanner-frame {
    width: 250px;
    height: 250px;
    border: 3px solid #28a745;
    border-radius: 10px;
    position: relative;
    animation: pulse 2s infinite;
}

.scanner-frame::before,
.scanner-frame::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border: 3px solid #28a745;
}

.scanner-frame::before {
    top: -3px;
    left: -3px;
    border-right: none;
    border-bottom: none;
}

.scanner-frame::after {
    bottom: -3px;
    right: -3px;
    border-left: none;
    border-top: none;
}

.scanner-text {
    color: white;
    margin-top: 20px;
    text-align: center;
    background: rgba(0,0,0,0.7);
    padding: 10px 20px;
    border-radius: 20px;
    font-weight: 500;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
    100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
}

.manual-input {
    border-top: 1px solid #dee2e6;
    padding-top: 20px;
}

.btn {
    border-radius: 10px;
    font-weight: 500;
}

.alert {
    border-radius: 10px;
    border: none;
}

/* Responsive */
@media (max-width: 768px) {
    .scanner-wrapper {
        padding: 10px;
    }
    
    .scanner-container {
        padding: 15px;
    }
    
    .scanner-frame {
        width: 200px;
        height: 200px;
    }
    
    .camera-container {
        aspect-ratio: 1/1;
        max-height: 300px;
    }
}

/* Orientación horizontal en móviles */
@media (max-height: 600px) and (orientation: landscape) {
    .camera-container {
        max-height: 250px;
    }
    
    .scanner-frame {
        width: 180px;
        height: 180px;
    }
}
</style>
@endpush