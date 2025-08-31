@extends('Template-profesor')

@section('title', 'Bitácoras')

@section('content')
<head>
    <link rel="stylesheet" href="{{ asset('CSS/Bitacora.css') }}">
</head>

<head> 
      <link rel="stylesheet" href="{{ asset('Css/Bitacoras.css') }}">
</head>

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

              <!-- Lección - VERSIÓN SIMPLIFICADA -->
              <div class="col-md-6 position-relative">
                <i class="bi bi-book position-absolute top-50 start-0 translate-middle-y ms-3" id="iconoInformacion"></i>
                <form method="GET" action="{{ route('bitacora.index') }}" id="leccionForm">
                  <select name="leccion" id="leccionSelect" class="form-control ps-5">
                      <option value="">Seleccione un horario</option>
                      @foreach($horarios as $horario)
                          <option value="{{ $horario->id }}" 
                                  data-recinto="{{ optional($horario->recinto)->nombre ?? '' }}"
                                  data-seccion="{{ optional($horario->seccion)->nombre ?? '' }}"
                                  data-subarea="{{ optional($horario->subarea)->nombre ?? '' }}"
                                  @if(request('leccion') == $horario->id) selected @endif>
                              Horario {{ $horario->id }}
                          </option>
                      @endforeach
                  </select>
                </form>
              </div>

          </div>
        </div>

        <!-- RESTO DEL CONTENIDO SIMPLIFICADO -->
        <div class="container mt-4">
            <p>Esta es una versión simplificada de la vista para debugging</p>
            <p>Total horarios: {{ count($horarios) }}</p>
        </div>

      </div>
  </div>
</div>

@endsection
