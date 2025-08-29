@extends('Template-profesor')

@section('title', 'Bitácoras')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('CSS/Bitacora.css') }}">
</head>

<head> 
      <link rel="stylesheet" href="{{ asset('Css/Bitacoras.css') }}">
</head>

<!-- Mostrar mensajes de éxito o error -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!------------------------------------------------------------------------------------------------------------------------->
<div class="wrapper">
    <div class="main-content">
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
                <input class="form-control ps-5" disabled id="docenteInput" value="Docente: {{ Auth::user()->name }}" />
              </div>

              <!-- Recinto -->
              <div class="col-md-6 position-relative d-none" id="recintoGroup">
                <i class="bi bi-pc-display position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <input class="form-control ps-5" disabled id="recintoInput" value="Recinto: {{ $recinto }}" />
              </div>

              <!-- Fecha -->
              <div class="col-md-6 position-relative">
                <i class="bi bi-calendar-week position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <input class="form-control ps-5" id="fechaDispositivo" readonly />
              </div>

              <!-- Sección -->
              <div class="col-md-6 position-relative d-none" id="seccionGroup">
                <i class="bi bi-easel position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <input class="form-control ps-5" disabled id="seccionInput" value="Sección: {{ $seccion }}" />
              </div>

              <!-- SubÁrea -->
              <div class="col-md-6 position-relative d-none" id="subareaGroup">
                <i class="bi bi-border-style position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <input class="form-control ps-5" disabled id="subareaInput" value="Subárea: {{ $subarea }}" />
              </div>

              <!-- Lección -->
              <div class="col-md-6 position-relative">
                <i class="bi bi-book position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <form method="GET" action="{{ route('bitacora.index') }}" id="leccionForm">
                  <select name="leccion" id="leccionSelect" class="form-control ps-5">
                      <option value="">Seleccione un horario</option>
                      @foreach($horarios as $horario)
                          @if($horario->leccion && $horario->leccion->count() > 0)
                              @foreach($horario->leccion as $leccion)
                                  <option value="{{ $horario->id }}" 
                                          data-recinto="{{ optional($horario->recinto)->nombre ?? '' }}"
                                          data-seccion="{{ optional($horario->seccion)->nombre ?? '' }}"
                                          data-subarea="{{ optional($horario->subarea)->nombre ?? '' }}"
                                          data-recinto-id="{{ optional($horario->recinto)->id ?? '' }}"
                                          data-seccion-id="{{ optional($horario->seccion)->id ?? '' }}"
                                          data-subarea-id="{{ optional($horario->subarea)->id ?? '' }}"
                                          data-leccion-id="{{ $leccion->id }}"
                                          @if(request('leccion') == $horario->id) selected @endif>
                                      {{ $leccion->leccion ?? 'Sin nombre' }} 
                                      @if($leccion->tipoLeccion)
                                          ({{ $leccion->tipoLeccion }})
                                      @endif
                                      @if($leccion->hora_inicio && $leccion->hora_final)
                                          - {{ $leccion->hora_inicio }} a {{ $leccion->hora_final }}
                                      @endif
                                      - {{ optional($horario->recinto)->nombre ?? 'Sin recinto' }}
                                  </option>
                              @endforeach
                          @else
                              <option value="{{ $horario->id }}" 
                                      data-recinto="{{ optional($horario->recinto)->nombre ?? '' }}"
                                      data-seccion="{{ optional($horario->seccion)->nombre ?? '' }}"
                                      data-subarea="{{ optional($horario->subarea)->nombre ?? '' }}"
                                      data-recinto-id="{{ optional($horario->recinto)->id ?? '' }}"
                                      data-seccion-id="{{ optional($horario->seccion)->id ?? '' }}"
                                      data-subarea-id="{{ optional($horario->subarea)->id ?? '' }}"
                                      @if(request('leccion') == $horario->id) selected @endif>
                                  Horario {{ $horario->id }} (Sin lecciones asignadas)
                              </option>
                          @endif
                      @endforeach
                  </select>
                </form>
              </div>

          </div>
        </div>

    <!-- Formulario simplificado para bitácoras -->
    <form id="formBitacora" method="POST" action="{{ route('bitacora.store') }}" style="display: none;">
        @csrf
        <input type="hidden" name="id_recinto" id="hiddenRecinto">
        <input type="hidden" name="id_seccion" id="hiddenSeccion">
        <input type="hidden" name="id_subarea" id="hiddenSubarea">
        <input type="hidden" name="id_horario" id="hiddenHorario">
        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
        <input type="hidden" name="hora_envio" id="horaEnvio">
    </form>

          </div>
        </div>


    <div class="container-fluid d-flex justify-content-center mb-3">
        <div class="container-fluid" id="estadoRec">
          <div class="container-fluid button-box">
            <div class="container-fluid" id="btn"></div>
            <button type="button" class="toggle-btn" id="btn-orden" onclick="leftClick()">
                <h5><i class="bi bi-check2-circle icono-estado"></i>Todo en Orden</h5>
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
                              <div class="form-check" id="OpcionPrioridad"><input class="form-check-input" type="radio" name="prioridad" value="alta" /> Alta</div>
                              <div class="form-check" id="OpcionPrioridad"><input class="form-check-input" type="radio" name="prioridad" value="media" /> Media</div>
                          </div>
                          <div class="col">
                              <div class="form-check" id="OpcionPrioridad"><input class="form-check-input" type="radio" name="prioridad" value="regular" /> Regular</div>
                              <div class="form-check" id="OpcionPrioridad"><input class="form-check-input" type="radio" name="prioridad" value="baja" /> Baja</div>
                          </div>
                      </div>
                  </div>
                  <div class="col-md-6">
                      <h5>Observaciones</h5>
                      <div class="row"  id="observaciones">
                          <textarea class="form-control" rows="4" id="observacionesTextarea"></textarea>
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
          ¿Está Seguro de que Todo<br>se Encuentra en Orden Dentro del Recinto?
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
          ¿Está Seguro de Reportar<br>un Problema Dentro del Recinto?
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
    <script src="{{ asset('JS/alertas.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar la fecha del dispositivo
        var fechaInput = document.getElementById('fechaDispositivo');
        if (fechaInput) {
            var now = new Date();
            var fechaLocal = now.toLocaleDateString('es-ES');
            fechaInput.value = "Fecha: " + fechaLocal;
        }

        // Mostrar campos si hay una lección seleccionada
        var leccionSelect = document.getElementById('leccionSelect');
        if (leccionSelect && leccionSelect.value) {
            mostrarCamposLeccion();
        }
    });

    function mostrarCamposLeccion() {
        var selected = document.getElementById('leccionSelect').selectedOptions[0];
        
        if (selected && selected.value) {
            document.getElementById('recintoGroup').classList.remove('d-none');
            document.getElementById('seccionGroup').classList.remove('d-none');
            document.getElementById('subareaGroup').classList.remove('d-none');
            
            var recinto = selected.getAttribute('data-recinto') || '';
            var seccion = selected.getAttribute('data-seccion') || '';
            var subarea = selected.getAttribute('data-subarea') || '';
            
            document.getElementById('recintoInput').value = 'Recinto: ' + recinto;
            document.getElementById('seccionInput').value = 'Sección: ' + seccion;
            document.getElementById('subareaInput').value = 'SubÁrea: ' + subarea;
        } else {
            document.getElementById('recintoGroup').classList.add('d-none');
            document.getElementById('seccionGroup').classList.add('d-none');
            document.getElementById('subareaGroup').classList.add('d-none');
        }
    }

    // Funciones para botones de estado
    window.leftClick = function() {
        var btn = document.getElementById('btn');
        if (btn) btn.style.left = '2px';
        
        var contenidoProblema = document.getElementById('contenido-problema');
        if (contenidoProblema) contenidoProblema.classList.remove('active');
        
        var btnEnviarOrden = document.getElementById('btnEnviarOrden');
        if (btnEnviarOrden) btnEnviarOrden.style.display = 'block';
    };

    window.rightClick = function() {
        var btn = document.getElementById('btn');
        if (btn) btn.style.left = 'calc(50% - 2px)';
        
        var contenidoProblema = document.getElementById('contenido-problema');
        if (contenidoProblema) contenidoProblema.classList.add('active');
        
        var btnEnviarOrden = document.getElementById('btnEnviarOrden');
        if (btnEnviarOrden) btnEnviarOrden.style.display = 'none';
    };

    window.limpiarFormularioProblema = function() {
        document.querySelectorAll('input[name="prioridad"]').forEach(radio => radio.checked = false);
        
        var textarea = document.getElementById('observacionesTextarea');
        if (textarea) textarea.value = '';
        
        var mensajeError = document.getElementById('mensajeError');
        if (mensajeError) mensajeError.classList.add('d-none');
        
        leftClick();
    };

    window.validarDatos = function() {
        var prioridadSeleccionada = document.querySelector('input[name="prioridad"]:checked');
        var observaciones = document.getElementById('observacionesTextarea').value.trim();
        var mensajeError = document.getElementById('mensajeError');
        
        if (!prioridadSeleccionada || !observaciones) {
            mensajeError.classList.remove('d-none');
            return false;
        } else {
            mensajeError.classList.add('d-none');
            var modalProblema = new bootstrap.Modal(document.getElementById('modalProblema'));
            modalProblema.show();
            return true;
        }
    };

    window.confirmarEnvioComentario = function() {
        var leccionSelect = document.getElementById('leccionSelect');
        
        if (!leccionSelect.value) {
            Swal.fire({
                title: 'Error',
                text: 'Por favor seleccione un horario antes de enviar la bitácora',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }

        var selectedOption = leccionSelect.selectedOptions[0];
        var now = new Date();
        var hora = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
        
        var recintoId = selectedOption.getAttribute('data-recinto-id');
        var seccionId = selectedOption.getAttribute('data-seccion-id');
        var subareaId = selectedOption.getAttribute('data-subarea-id');
        
        if (!recintoId || !seccionId || !subareaId) {
            Swal.fire({
                title: 'Error',
                text: 'Faltan datos del horario seleccionado.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        // Llenar formulario único
        document.getElementById('hiddenRecinto').value = recintoId;
        document.getElementById('hiddenSeccion').value = seccionId;
        document.getElementById('hiddenSubarea').value = subareaId;
        document.getElementById('hiddenHorario').value = leccionSelect.value;
        document.getElementById('horaEnvio').value = hora;
        
        // Enviar formulario
        document.getElementById('formBitacora').submit();
    };

    // Event listener para cambio de lección
    document.addEventListener('DOMContentLoaded', function() {
        var leccionSelect = document.getElementById('leccionSelect');
        
        if (leccionSelect && leccionSelect.value) {
            mostrarCamposLeccion();
        }
        
        leccionSelect.addEventListener('change', function() {
            if (this.value) {
                mostrarCamposLeccion();
                document.getElementById('leccionForm').submit();
            } else {
                document.getElementById('recintoGroup').classList.add('d-none');
                document.getElementById('seccionGroup').classList.add('d-none');
                document.getElementById('subareaGroup').classList.add('d-none');
            }
        });
    });
    </script>
@endpush

<!------------------------------------------------------------------------------------------------------------------------->