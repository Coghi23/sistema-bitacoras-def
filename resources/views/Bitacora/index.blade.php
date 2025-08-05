@extends('Template-profesor')

@section('title', 'Bitácoras')

@section('content')

<!------------------------------------------------------------------------------------------------------------------------->
<div class="container my-4">
    <div class="row">
      <div class="col-14">

        <h5><strong>Información</strong></h5>
                <!-- Tarjeta Información -->
        <div class="card p-3 mb-3" id="cuadInfo">
          <div class="row g-3">

              <!-- Docente -->
              <div class="col-md-6 position-relative">
                <i class="bi bi-person-circle position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <input class="form-control ps-5" disabled value="Docente: {{ $profesor }}" />
              </div>

              <!-- Hora ingreso -->
              <div class="col-md-6 position-relative">
                <i class="bi bi-clock position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <input class="form-control ps-5" disabled value="Hora ingreso: {{ $horaIngreso }}" />
              </div>

              <!-- Recinto -->
              <div class="col-md-6 position-relative">
                <i class="bi bi-pc-display position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <input class="form-control ps-5" disabled value="Recinto: {{ $recinto }}" />
              </div>

              <!-- Hora salida -->
              <div class="col-md-6 position-relative">
                <i class="bi bi-clock-fill position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <input class="form-control ps-5" disabled value="Hora salida: {{ $horaSalida }}" />
              </div>

              <!-- Fecha -->
              <div class="col-md-6 position-relative">
                <i class="bi bi-calendar-week position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <input class="form-control ps-5" disabled value="Fecha: {{ $fecha }}" />
              </div>

              <!-- Hora envío -->
              <div class="col-md-6 position-relative">
                <i class="bi bi-send-fill position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <input class="form-control ps-5" disabled value="Hora envío: {{ $horaEnvio }}" />
              </div>

              <!-- Sección -->
              <div class="col-md-6 position-relative">
                <i class="bi bi-easel position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <input class="form-control ps-5" disabled value="Sección: {{ $seccion }}" />
              </div>

              <!-- SubÁrea -->
              <div class="col-md-6 position-relative">
                <i class="bi bi-border-style position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <input class="form-control ps-5" disabled value="SubÁrea: {{ $subarea }}" />
              </div>

          </div>
        </div>


    <div class="container-fluid d-flex justify-content-center mb-3">
        <div class="container-fluid" id="estadoRec">
          <div class="container-fluid button-box">
            <div class="container-fluid" id="btn"></div>
            <button type="button" class="toggle-btn" id="btn-orden" onclick="leftClick()">
                <h5><i class="bi bi-check2-circle icono-estado"></i>Todo en orden</h5>
              </button>
              
              <button type="button" class="toggle-btn" id="btn-problema" onclick="rightClick()">
                <h5><i class="bi bi-exclamation-circle icono-estado"></i>Reportar Problema</h5>
              </button>
              
          </div>
        </div>
      </div>

      <div class="container-fluid justify-content-center d-flex" >
        <button class="btn" id="btnEnviarOrden" data-bs-toggle="modal"
        data-bs-target="#modalOrden">Enviar Bitácora</button>
      </div>

     
    
      <!-- Contenido que se muestra/oculta -->
      <div id="contenido-problema" class="content-slide">
        <div class="row position-relative">
            <div class="col-md-6" >
                <h5>Prioridad</h5>
                <div class="row d-flex" id="prioridad">
                    <div class="col">
                        <div class="form-check" id="OpcionPrioridad"><input class="form-check-input" type="radio" name="prioridad" /> Alta</div>
                        <div class="form-check" id="OpcionPrioridad"><input class="form-check-input" type="radio" name="prioridad" /> Media</div>
                    </div>
                    <div class="col">
                        <div class="form-check" id="OpcionPrioridad"><input class="form-check-input" type="radio" name="prioridad" /> Regular</div>
                        <div class="form-check" id="OpcionPrioridad"><input class="form-check-input" type="radio" name="prioridad" /> Baja</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h5>Observaciones</h5>
                <div class="row"  id="observaciones">
                    <textarea class="form-control" rows="4"></textarea>
                </div>
            </div>

           <div class="d-flex flex-column align-items-end">
              <!-- Mensaje de error -->
              <div id="mensajeError" class="alert alert-danger d-none" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i>Por favor ingrese todos los datos.
              </div>
          </div>

            <!-- Botones -->
        <div class="d-flex justify-content-end gap-2 mt-3">
            <button class="btn" id="btnCancelar" onclick="limpiarFormularioProblema()">Cancelar</button>
            <button class="btn" id="btnEnviar" onclick="validarDatos()">Enviar Bitácora</button>
        </div>
    </div>


    </div>
</div>
</div>

<!------------------------------------------------------------------------------------------------------------------------------------------------->
    
<!-- Modal -->
<div class="modal fade" id="modalOrden" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="icon-container mx-auto">
          <i class="bi bi-question-circle-fill" id="iconomodal"></i>
        </div>
        <p class="modal-text text-center">
          ¿Está usted seguro de que todo<br>se encuentra en orden dentro del recinto?
        </p>
        <div class="d-flex justify-content-center gap-3">
          <button type="button" class="btn" data-bs-dismiss="modal" id="BtnAfirmacion" onclick="confirmarEnvioComentario()">Sí</button>
          <button type="button" class="btn" data-bs-dismiss="modal" id="BtnNegacion">No</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modal -->

  <!-- Modal -->
<div class="modal fade" id="modalProblema" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content custom-modal">
        <div class="icon-container mx-auto">
          <i class="bi bi-question-circle-fill" id="iconomodal"></i>
        </div>
        <p class="modal-text text-center">
          ¿Está usted seguro de reportar<br>un problema dentro del recinto?
        </p>
        <div class="d-flex justify-content-center gap-3">
          <button type="button" class="btn" data-bs-dismiss="modal" id="BtnAfirmacion" onclick="confirmarEnvioComentario()">Sí</button>
          <button type="button" class="btn" data-bs-dismiss="modal" id="BtnNegacion">No</button>
        </div>
      </div>
    </div>
  </div>

  @endsection
<!------------------------------------------------------------------------------------------------------------------------->
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('JS/indexBitacoras.js') }}"></script>
@endpush

<!------------------------------------------------------------------------------------------------------------------------->