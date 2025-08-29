@extends('Template-administrador')

@section('title', 'Gestión de QR - Administrador')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><i class="bi bi-qr-code-scan me-2"></i>Gestión de Códigos QR</h2>
                <div>
                    <button class="btn btn-warning me-2" onclick="simularEscaneoMasivo()">
                        <i class="bi bi-lightning me-1"></i>Simular Escaneo Masivo
                    </button>
                    <button class="btn btn-primary" onclick="actualizarDatos()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Actualizar
                    </button>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-key display-4 text-success"></i>
                            <h4 class="mt-2" id="total-llaves">{{ $llaves->count() }}</h4>
                            <p class="text-muted">Total Llaves</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-check-circle display-4 text-success"></i>
                            <h4 class="mt-2" id="llaves-disponibles">{{ $llaves->where('estado', 0)->count() }}</h4>
                            <p class="text-muted">Disponibles</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-exclamation-circle display-4 text-warning"></i>
                            <h4 class="mt-2" id="llaves-entregadas">{{ $llaves->where('estado', 1)->count() }}</h4>
                            <p class="text-muted">Entregadas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <i class="bi bi-building display-4 text-info"></i>
                            <h4 class="mt-2" id="total-recintos">{{ $llaves->flatMap->recinto->count() }}</h4>
                            <p class="text-muted">Recintos</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de llaves -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Estado de Llaves y Bitácoras</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0" id="tablaLlaves">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Estado Llave</th>
                                    <th>Recinto</th>
                                    <th>Estado Bitácora</th>
                                    <th>Última Actualización</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($llaves as $llave)
                                    <tr data-llave-id="{{ $llave->id }}">
                                        <td><span class="badge bg-secondary">{{ $llave->id }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-key me-2 text-warning"></i>
                                                <strong>{{ $llave->nombre }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $llave->estado_badge_class }} estado-llave-badge">
                                                {{ $llave->estado_entrega_text }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($llave->recinto->isNotEmpty())
                                                @foreach($llave->recinto as $recinto)
                                                    <div class="d-flex align-items-center mb-1">
                                                        <i class="bi bi-building me-2 text-success"></i>
                                                        {{ $recinto->nombre }}
                                                    </div>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Sin recinto asignado</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($llave->recinto->isNotEmpty() && $llave->recinto->first()->bitacoras->isNotEmpty())
                                                @php
                                                    $bitacora = $llave->recinto->first()->bitacoras->first();
                                                @endphp
                                                <span class="badge {{ $bitacora->estado_badge_class }} estado-bitacora-badge">
                                                    {{ $bitacora->estado_texto }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Sin bitácora</span>
                                            @endif
                                        </td>
                                        <td class="ultima-actualizacion">{{ $llave->updated_at->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-info" 
                                                        onclick="verDetalleLlave({{ $llave->id }})" 
                                                        data-bs-toggle="tooltip" title="Ver detalle">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        onclick="generarQR({{ $llave->id }})" 
                                                        data-bs-toggle="tooltip" title="Generar QR">
                                                    <i class="bi bi-qr-code"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        onclick="simularEscaneo({{ $llave->id }})" 
                                                        data-bs-toggle="tooltip" title="Simular Escaneo">
                                                    <i class="bi bi-lightning"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar QR -->
<div class="modal fade" id="modalQR" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-qr-code me-2"></i>Código QR de Llave
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center" id="contenidoQR">
                <!-- Contenido del QR se carga aquí -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" onclick="simularEscaneoDesdeModal()">
                    <i class="bi bi-lightning me-1"></i>Simular Escaneo
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para resultados de simulación -->
<div class="modal fade" id="modalResultado" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>Resultado de Simulación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contenidoResultado">
                <!-- Contenido del resultado se carga aquí -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let actualizandoDatos = false;
let llaveActualQR = null;

function simularEscaneo(llaveId) {
    if (!llaveId) {
        console.error('ID de llave no proporcionado');
        return;
    }

    // Mostrar loading
    Swal.fire({
        title: 'Simulando escaneo...',
        text: 'Procesando cambio de estado de la llave',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch('/qr/escanear', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            llave_id: llaveId
        })
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.success) {
            // Mostrar resultado exitoso
            mostrarResultadoSimulacion(data);
            // Actualizar la tabla
            actualizarDatos();
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message || 'Error al simular el escaneo',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({
            title: 'Error',
            text: 'Error de conexión al simular el escaneo',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}

function simularEscaneoDesdeModal() {
    if (llaveActualQR) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalQR'));
        modal.hide();
        simularEscaneo(llaveActualQR);
    }
}

function simularEscaneoMasivo() {
    Swal.fire({
        title: '¿Simular escaneo masivo?',
        text: 'Esto cambiará el estado de todas las llaves disponibles',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, simular',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            ejecutarEscaneoMasivo();
        }
    });
}

function ejecutarEscaneoMasivo() {
    const llaves = document.querySelectorAll('[data-llave-id]');
    let procesadas = 0;
    let exitosas = 0;
    let fallidas = 0;

    Swal.fire({
        title: 'Procesando escaneo masivo...',
        text: `0 de ${llaves.length} llaves procesadas`,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    llaves.forEach((fila, index) => {
        const llaveId = fila.getAttribute('data-llave-id');
        
        setTimeout(() => {
            fetch('/qr/escanear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    llave_id: llaveId
                })
            })
            .then(response => response.json())
            .then(data => {
                procesadas++;
                if (data.success) {
                    exitosas++;
                } else {
                    fallidas++;
                }
                
                // Actualizar progreso
                Swal.getContent().querySelector('.swal2-text').textContent = 
                    `${procesadas} de ${llaves.length} llaves procesadas`;
                
                // Si terminamos con todas
                if (procesadas === llaves.length) {
                    Swal.close();
                    Swal.fire({
                        title: 'Escaneo masivo completado',
                        html: `
                            <div class="text-start">
                                <p><strong>Resultados:</strong></p>
                                <p><i class="bi bi-check-circle text-success"></i> Exitosas: ${exitosas}</p>
                                <p><i class="bi bi-x-circle text-danger"></i> Fallidas: ${fallidas}</p>
                                <p><i class="bi bi-info-circle text-info"></i> Total procesadas: ${procesadas}</p>
                            </div>
                        `,
                        icon: 'info',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        actualizarDatos();
                    });
                }
            })
            .catch(error => {
                procesadas++;
                fallidas++;
                console.error('Error procesando llave', llaveId, error);
                
                if (procesadas === llaves.length) {
                    Swal.close();
                    actualizarDatos();
                }
            });
        }, index * 500); // Esperar 500ms entre cada simulación
    });
}

function mostrarResultadoSimulacion(data) {
    const iconClass = data.accion === 'entrega_llave' ? 'bi-key text-warning' : 
                     data.accion === 'devolucion_llave' ? 'bi-key text-success' : 'bi-arrow-repeat text-info';
    const titulo = data.accion === 'entrega_llave' ? 'Llave Entregada' : 
                  data.accion === 'devolucion_llave' ? 'Llave Devuelta' : 'Ciclo Reiniciado';
    
    document.getElementById('contenidoResultado').innerHTML = `
        <div class="text-center">
            <i class="bi ${iconClass} display-1 mb-3"></i>
            <h4>${titulo}</h4>
            <div class="mt-3">
                <p><strong>Recinto:</strong> ${data.recinto}</p>
                <p><strong>Estado de la llave:</strong> 
                    <span class="badge ${data.estado_llave === 'entregada' ? 'bg-warning' : 'bg-success'}">
                        ${data.estado_llave_texto}
                    </span>
                </p>
                <p><strong>Estado de la bitácora:</strong> 
                    <span class="badge ${getBadgeClassForBitacora(data.estado_bitacora)}">
                        ${data.estado_bitacora_texto}
                    </span>
                </p>
                <p><strong>Bitácora ID:</strong> ${data.bitacora_id}</p>
                <p><strong>Hora:</strong> ${data.timestamp}</p>
            </div>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('modalResultado'));
    modal.show();
}

function getBadgeClassForBitacora(estado) {
    switch(estado) {
        case 'pendiente': return 'bg-secondary';
        case 'activa': return 'bg-warning';
        case 'completada': return 'bg-success';
        default: return 'bg-dark';
    }
}

function actualizarDatos() {
    if (actualizandoDatos) return;
    
    actualizandoDatos = true;
    const btn = document.querySelector('button[onclick="actualizarDatos()"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise spin me-1"></i>Actualizando...';
    btn.disabled = true;

    fetch('/admin/llaves/realtime')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Actualizar estadísticas
                document.getElementById('total-llaves').textContent = data.total;
                document.getElementById('llaves-disponibles').textContent = data.llaves.filter(l => l.estado_texto === 'No Entregada').length;
                document.getElementById('llaves-entregadas').textContent = data.llaves.filter(l => l.estado_texto === 'Entregada').length;

                // Actualizar tabla
                actualizarTablaLlaves(data.llaves);
                
                console.log('Datos actualizados:', data.timestamp);
            }
        })
        .catch(error => {
            console.error('Error al actualizar datos:', error);
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            actualizandoDatos = false;
        });
}

function actualizarTablaLlaves(llaves) {
    const tbody = document.querySelector('#tablaLlaves tbody');
    
    llaves.forEach(llave => {
        const fila = document.querySelector(`tr[data-llave-id="${llave.id}"]`);
        if (fila) {
            // Actualizar badge de estado de llave
            const estadoLlaveBadge = fila.querySelector('.estado-llave-badge');
            if (estadoLlaveBadge) {
                estadoLlaveBadge.className = `badge ${llave.estado_badge_class} estado-llave-badge`;
                estadoLlaveBadge.textContent = llave.estado_texto;
            }
            
            // Actualizar badge de estado de bitácora (si es necesario)
            const estadoBitacoraBadge = fila.querySelector('.estado-bitacora-badge');
            if (estadoBitacoraBadge && llave.bitacora_estado) {
                estadoBitacoraBadge.className = `badge ${getBadgeClassForBitacora(llave.bitacora_estado)} estado-bitacora-badge`;
                estadoBitacoraBadge.textContent = llave.bitacora_estado_texto;
            }
            
            // Actualizar última actualización
            const ultimaActualizacion = fila.querySelector('.ultima-actualizacion');
            if (ultimaActualizacion) {
                ultimaActualizacion.textContent = llave.ultima_actualizacion;
            }
        }
    });
}

function verDetalleLlave(id) {
    // Implementar vista de detalle
    console.log('Ver detalle de llave:', id);
}

function generarQR(llaveId) {
    llaveActualQR = llaveId;
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=llave_${llaveId}`;
    
    document.getElementById('contenidoQR').innerHTML = `
        <div>
            <img src="${qrUrl}" alt="QR Code" class="img-fluid mb-3">
            <p><strong>Llave ID:</strong> ${llaveId}</p>
            <p class="text-muted">Escaneá este código para entregar/devolver la llave</p>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('modalQR'));
    modal.show();
}

// Actualizar datos cada 30 segundos
setInterval(actualizarDatos, 30000);

// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Agregar meta tag para CSRF token si no existe
document.addEventListener('DOMContentLoaded', function() {
    if (!document.querySelector('meta[name="csrf-token"]')) {
        const metaTag = document.createElement('meta');
        metaTag.name = 'csrf-token';
        metaTag.content = '{{ csrf_token() }}';
        document.head.appendChild(metaTag);
    }
});
</script>

<style>
.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
@endpush
