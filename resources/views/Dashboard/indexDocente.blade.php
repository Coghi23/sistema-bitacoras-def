@extends('Template-profesor')

@section('title', 'Sistema de Bitácoras')

@section('content')
    <div id="sidebar-container"></div>
<script type="module">
import { inicializarSidebar } from './JS/Sidebar.js'; 

fetch("sideDocente.html")
.then(response => response.text())
.then(data => {
 document.getElementById("sidebar-container").innerHTML = data;
 inicializarSidebar();
   });
</script>
<!------------------------------------------------------------------------------------------------------------------------->
        <div class="container-fluid" >
            <div class="row">

                <div class="col">
                    <div class="container my-10">
                        <div class="row vh-100">
                          <div class="container mt-5 d-flex flex-desktop flex-wrap justify-content-between align-items-start">
                            <div class="mb-4" id="mensaje-bienvenida">
                              <h5 class="fw-bold text-white">¡Hola! Gracias por acceder al sistema.</h5>
                              <p class="text-white mb-0">Mantener un registro claro y preciso es fundamental para una gestión eficiente.</p>
                            </div>
                    
                            <div class="container d-flex flex-desktop flex-wrap justify-content-between align-items-center p-3" id="mensaje-seguridad">
                                <div class="d-flex align-items-start mb-2">
                                    <i class="bi bi-clipboard-check me-2"></i>
                                    <p class="mb-0">
                                    No olvides completar tu bitácora.
                                    </p>
                                </div>
                                <div class="d-flex align-items-start mb-2">
                                    <i class="bi bi-person-gear me-2"></i>
                                    <p class="mb-0">
                                    Si necesitas revisar registros anteriores, usa la opción de reporte.
                                    </p>
                                </div>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-lock-fill me-2" ></i>
                                    <p class="mb-0">
                                    La seguridad de los datos es nuestra prioridad. Cierra sesión al finalizar.
                                    </p>
                                </div>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
    
                <div class="col flex-desktop flex-wrap" id="Img-Oficina">
                    
                </div>
            
        </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush