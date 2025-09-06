@extends('Template-administrador')


@section('title', 'Registro de Especialidad')


@section('content')
<style>
	/* Fix for dropdown arrow positioning */
	.form-select {
		background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e") !important;
		background-repeat: no-repeat !important;
		background-position: right 0.75rem center !important;
		background-size: 16px 12px !important;
		padding-right: 2.25rem !important;
	}
   
	/* Ensure no conflicting pseudo-elements */
	.form-select::after,
	.form-select::before {
		display: none !important;
	}
   
	/* Fix for input-group select styling */
	.input-group .form-select {
		position: relative;
		z-index: 1;
	}
</style>
<div class="wrapper">
	<div class="main-content">


		<div class="search-bar-wrapper mb-4">
			<div class="search-bar">
				<form id="busquedaForm" method="GET" action="{{ route('especialidad.index') }}" class="w-100 position-relative">
					<span class="search-icon">
						<i class="bi bi-search"></i>
					</span>
					<input
						type="text"
						class="form-control"
						placeholder="Buscar especialidad..."
						name="busquedaEspecialidad"
						value="{{ request('busquedaEspecialidad') }}"
						id="inputBusqueda"
						autocomplete="off"
					>
					@if(request('busquedaEspecialidad'))
					<button
						type="button"
						class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2"
						id="limpiarBusqueda"
						title="Limpiar búsqueda"
						style="background: transparent;"
					>
						<i class="bi bi-x-circle"></i>
					</button>
					@endif
				</form>
			</div>
			@if(Auth::user() && !Auth::user()->hasRole('director'))
				<button class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
					data-bs-toggle="modal" data-bs-target="#modalAgregarEspecialidad"
					title="Agregar Especialidad" style="background-color: #134496; font-size: 1.2rem;">
					Agregar <i class="bi bi-plus-circle ms-2"></i>
				</button>
			@endif
		</div>


		{{-- Indicador de resultados de búsqueda --}}
		@if(request('busquedaEspecialidad'))
			<div class="alert alert-info d-flex align-items-center" role="alert">
				<i class="bi bi-info-circle me-2"></i>
				<span>
					Mostrando {{ $especialidades->count() }} resultado(s) para "<strong>{{ request('busquedaEspecialidad') }}</strong>"
					<a href="{{ route('especialidad.index') }}" class="btn btn-sm btn-outline-primary ms-2">Ver todas</a>
				</span>
			</div>
		@endif




		{{-- Tabla --}}
		<div class="table-responsive">
			{{-- Botones para mostrar/ocultar especialidades inactivas --}}
			<a href="{{ route('especialidad.index', ['inactivos' => 1]) }}" class="btn btn-warning mb-3">
				Mostrar inactivas
			</a>
			<a href="{{ route('especialidad.index') }}" class="btn btn-primary mb-3">
				Mostrar activas
			</a>
			<table class="table table-striped">
				<thead>
					<tr class="header-row">
						<th class="text-center">Especialidad</th>
						<th class="text-center">Institución</th>
						<th class="text-center">Estado</th>
						<th class="text-center">Acciones</th>
					</tr>
				</thead>
				<tbody>
					@php
						$mostrarInactivas = request('inactivos') == 1;
					@endphp
					@forelse($especialidades as $especialidad)
						@if(($mostrarInactivas && $especialidad->condicion == 0) || (!$mostrarInactivas && $especialidad->condicion == 1))
						<tr class="record-row">
							<td class="text-center">{{ $especialidad->nombre }}</td>
							<td class="text-center">
								@if($especialidad->instituciones->count() > 0)
									@foreach($especialidad->instituciones as $institucion)
										{{ $institucion->nombre }}@if(!$loop->last), @endif
									@endforeach
								@else
									<span class="text-muted">Sin instituciones</span>
								@endif
							</td>
							<td class="text-center">
								<span class="badge {{ isset($especialidad->condicion) && $especialidad->condicion ? 'bg-success' : 'bg-danger' }}">
									{{ isset($especialidad->condicion) && $especialidad->condicion ? 'Activa' : 'Inactiva' }}
								</span>
							</td>
							<td class="text-center">
								@can('edit_especialidad')
									@if($especialidad->condicion == 1)
										<button class="btn btn-link text-info p-0 me-2" data-bs-toggle="modal" data-bs-target="#modalEditarEspecialidad-{{ $especialidad->id }}">
											<i class="bi bi-pencil" style="font-size: 1.5rem;"></i>
										</button>
								@endcan
								@can('delete_especialidad')
										<button class="btn btn-link text-danger p-0" data-bs-toggle="modal" data-bs-target="#modalEliminarEspecialidad-{{ $especialidad->id }}">
											<i class="bi bi-trash" style="font-size: 1.5rem;"></i>
										</button>
									@endcan
								@else
									<button class="btn p-0 me-2" data-bs-toggle="modal" data-bs-target="#modalReactivarEspecialidad-{{ $especialidad->id }}" title="Reactivar especialidad">
										<i class="bi bi-arrow-counterclockwise icon-eliminar" style="font-size: 1.5rem; color: #28a745;"></i>
									</button>
								@endif
							</td>
						</tr>
					{{-- Modal Editar Especialidad --}}
					<div class="modal fade" id="modalEditarEspecialidad-{{ $especialidad->id }}" tabindex="-1" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content">
								<div class="modal-header modal-header-custom">
									<button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
										<i class="bi bi-arrow-left"></i>
									</button>
									<h5 class="modal-title">Registro de especialidad</h5>
								</div>
								 <div class="modal-body px-4 py-4">
									<form action="{{ route('especialidad.update', $especialidad->id) }}" method="POST">
									@csrf
									@method('PATCH')
									<div class="mb-3">
										<label class="form-label fw-bold">Especialidad</label>
										<input type="text" name="nombre" class="form-control @if(session('modal_editar_id') && session('modal_editar_id') == $especialidad->id && $errors->has('nombre')) is-invalid @endif"
											value="{{ old('nombre', $especialidad->nombre) }}" required>
										@if(session('modal_editar_id') && session('modal_editar_id') == $especialidad->id && $errors->has('nombre'))
											<div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
										@endif
									</div>

									<div class="mb-3">
										<label class="form-label fw-bold">Institución</label>
										@if(session('modal_editar_id') && session('modal_editar_id') == $especialidad->id && $errors->has('instituciones'))
											<div class="text-danger small mb-2">
												{{ $errors->first('instituciones') }}
												<br><small><i class="bi bi-info-circle"></i> Una especialidad debe tener al menos una institución asignada.</small>
											</div>
										@endif
											<!-- Instituciones actualmente asignadas como checkboxes ocultos -->
											<div style="display: none;">
												@foreach($instituciones as $inst)
													<input type="checkbox" 
														   id="inst-{{ $especialidad->id }}-{{ $inst->id }}"
														   name="instituciones[]" 
														   value="{{ $inst->id }}"
														   @if($especialidad->instituciones->where('id', $inst->id)->where('pivot.condicion', 1)->count() > 0) checked @endif>
												@endforeach
											</div>
                                            
											<!-- Select para agregar instituciones -->
											<div class="input-group dynamic-group mb-3">
												<select id="selectInstitucionEdit-{{ $especialidad->id }}" class="form-select">
													<option value="">Seleccione una institución para agregar</option>
													@foreach ($instituciones as $inst)
														<option value="{{ $inst->id }}" data-nombre="{{ $inst->nombre }}">{{ $inst->nombre }}</option>
													@endforeach
												</select>
												<button type="button" class="btn btn-success d-flex align-items-center justify-content-center" onclick="agregarInstitucionSimple('{{ $especialidad->id }}');" style="min-width: 38px; padding: 8px;">
													<i class="bi bi-plus"></i>
												</button>
											</div>
                                            
											<!-- Contenedor para mensajes de validación -->
											<div id="mensajeValidacion-{{ $especialidad->id }}" class="alert alert-danger d-none" role="alert">
												<i class="bi bi-exclamation-triangle"></i> <span id="textoMensaje-{{ $especialidad->id }}"></span>
											</div>
                                            
											<!-- Instituciones visibles actualmente asignadas -->
											<div id="institucionesVisuales-{{ $especialidad->id }}">
												@foreach($especialidad->instituciones->where('pivot.condicion', 1) as $instAsignada)
													<div class="input-group mt-2 institucion-visual" data-id="{{ $instAsignada->id }}">
														<input type="text" class="form-control" value="{{ $instAsignada->nombre }}" readonly>
														<button type="button" class="btn btn-danger" onclick="eliminarInstitucionSimple('{{ $especialidad->id }}', '{{ $instAsignada->id }}')">
															<i class="bi bi-x"></i>
														</button>
													</div>
												@endforeach
											</div>
										</div>

										<div class="text-center mt-4">
											<button type="submit" class="btn btn-primary">Modificar</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
						{{-- Modal Eliminar --}}
						<div class="modal fade" id="modalEliminarEspecialidad-{{ $especialidad->id }}" tabindex="-1" aria-labelledby="modalEspecialidadEliminarLabel-{{ $especialidad->id }}" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered">
								<div class="modal-content custom-modal">
									<div class="modal-body text-center">
										<div class="icon-container">
											<div class="circle-icon">
												<i class="bi bi-exclamation-circle"></i>
											</div>
										</div>
										<p class="modal-text">¿Desea eliminar la especialidad?</p>
										<div class="btn-group-custom">
											<form action="{{ route('especialidad.destroy', ['especialidad' => $especialidad->id]) }}" method="post">
												@method('DELETE')
												@csrf
												<button type="submit" class="btn btn-custom {{ $especialidad->condicion == 1 }}">Sí</button>
												<button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
						{{-- Modal Reactivar --}}
						<div class="modal fade" id="modalReactivarEspecialidad-{{ $especialidad->id }}" tabindex="-1" aria-labelledby="modalReactivarEspecialidadLabel-{{ $especialidad->id }}" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered">
								<div class="modal-content custom-modal">
									<div class="modal-body text-center">
										<div class="icon-container">
											<div class="circle-icon" style="background-color: #28a745; color: #fff;">
												<i class="bi bi-arrow-counterclockwise" style="color: #fff;"></i>
											</div>
										</div>
										<p class="modal-text">¿Desea reactivar la especialidad?</p>
										<div class="btn-group-custom">
											<form action="{{ route('especialidad.destroy', ['especialidad' => $especialidad->id]) }}" method="post">
												@method('DELETE')
												@csrf
												<button type="submit" class="btn btn-custom" style="background-color: #28a745; color: #fff;">Sí</button>
												<button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
						@endif
					@empty
					<tr class="record-row">
						<td class="text-center" colspan="4">No hay especialidades registradas.</td>
					</tr>
					@endforelse
				</tbody>
			</table>
		</div>


	</div>
	</div>


{{-- Modal Crear Especialidad --}}
<div class="modal fade" id="modalAgregarEspecialidad" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header modal-header-custom">
				<button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
					<i class="bi bi-arrow-left"></i>
				</button>
				<h5 class="modal-title">Registro de especialidad</h5>
			</div>
			<div class="modal-body px-4 py-4">
				<form id="formCrearEspecialidad" action="{{ route('especialidad.store') }}" method="POST">
					@csrf
					<div class="mb-3">
						<label class="form-label fw-bold">Especialidad</label>
						<input type="text" name="nombre" class="form-control @if(session('modal_crear') && $errors->has('nombre')) is-invalid @endif"
							   value="{{ old('nombre') }}">
						@if(session('modal_crear') && $errors->has('nombre'))
							<div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
						@endif
					</div>


					<div class="mb-3">
						<label class="form-label fw-bold">Institución</label>
						@if(session('modal_crear') && $errors->has('instituciones'))
							<div class="text-danger small mb-2">
								{{ $errors->first('instituciones') }}
								<br><small><i class="bi bi-info-circle"></i> Una especialidad debe tener al menos una institución asignada.</small>
							</div>
						@endif
						<div id="instituciones">
							<div class="input-group dynamic-group">
								<select id="selectInstitucion" class="form-select">
									<option value="">Seleccione una institución</option>
									@foreach ($instituciones as $institucion)
										<option value="{{ $institucion->id }}" data-nombre="{{ $institucion->nombre }}">{{ $institucion->nombre }}</option>
									@endforeach
								</select>
								<button type="button" class="btn btn-success d-flex align-items-center justify-content-center" onclick="agregarInstitucion()" style="height: 9%; min-width: 38px; padding: 0;">
									<i class="bi bi-plus" style="height: 49px;"></i>
								</button>
							</div>
							<!-- Contenedor para instituciones seleccionadas -->
							<div id="institucionesSeleccionadas" class="mt-2"></div>
                           
							<!-- Contenedor para mensajes de validación -->
							<div id="mensajeValidacionCrear" class="alert alert-danger d-none mt-2" role="alert">
								<i class="bi bi-exclamation-triangle"></i> <span id="textoMensajeCrear"></span>
							</div>
						</div>
					</div>
					<div class="text-center mt-4">
						<button type="submit" class="btn btn-primary">Crear</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<script>
	// Variables globales para crear especialidad
	let institucionesAgregadas = [];

	// Recargar la página al cerrar cualquier modal de crear o editar especialidad
	document.addEventListener('DOMContentLoaded', function() {
		var modalCrear = document.getElementById('modalAgregarEspecialidad');
		if (modalCrear) {
			modalCrear.addEventListener('hidden.bs.modal', function () {
				window.location.reload();
			});
		}
		document.querySelectorAll('[id^="modalEditarEspecialidad-"]').forEach(function(modalEditar) {
			modalEditar.addEventListener('hidden.bs.modal', function () {
				window.location.reload();
			});
		});
	});


	// ========== FUNCIONES PARA VALIDACIÓN ==========
   
	function mostrarMensajeValidacion(especialidadId, mensaje) {
		const contenedorMensaje = document.getElementById(`mensajeValidacion-${especialidadId}`);
		const textoMensaje = document.getElementById(`textoMensaje-${especialidadId}`);
       
		if (contenedorMensaje && textoMensaje) {
			textoMensaje.textContent = mensaje;
			contenedorMensaje.classList.remove('d-none');
           
			// Auto-ocultar después de 4 segundos
			setTimeout(() => {
				ocultarMensajeValidacion(especialidadId);
			}, 4000);
		}
	}

	function ocultarMensajeValidacion(especialidadId) {
		const contenedorMensaje = document.getElementById(`mensajeValidacion-${especialidadId}`);
		if (contenedorMensaje) {
			contenedorMensaje.classList.add('d-none');
		}
	}
   
	// Funciones para validación del modal de crear
	function mostrarMensajeValidacionCrear(mensaje) {
		const contenedorMensaje = document.getElementById('mensajeValidacionCrear');
		const textoMensaje = document.getElementById('textoMensajeCrear');
       
		if (contenedorMensaje && textoMensaje) {
			textoMensaje.textContent = mensaje;
			contenedorMensaje.classList.remove('d-none');
           
			// Auto-ocultar después de 4 segundos
			setTimeout(() => {
				ocultarMensajeValidacionCrear();
			}, 4000);
		}
	}
   
	function ocultarMensajeValidacionCrear() {
		const contenedorMensaje = document.getElementById('mensajeValidacionCrear');
		if (contenedorMensaje) {
			contenedorMensaje.classList.add('d-none');
		}
	}


	// ========== FUNCIONES PARA CREAR ESPECIALIDAD ==========


	function agregarInstitucion() {
		// Ocultar mensaje de validación anterior
		ocultarMensajeValidacionCrear();

		const select = document.getElementById('selectInstitucion');
		const selectedOption = select.options[select.selectedIndex];
       
		if (!selectedOption || !selectedOption.value) {
			mostrarMensajeValidacionCrear('Por favor seleccione una institución');
			return;
		}
       
		const id = selectedOption.value;
		const nombre = selectedOption.getAttribute('data-nombre');
       
		// Verificar si ya está agregada
		if (institucionesAgregadas.includes(id)) {
			mostrarMensajeValidacionCrear('Esta institución ya está agregada');
			return;
		}
       
		// Agregar al array de control
		institucionesAgregadas.push(id);
       
		// Crear elemento visual
		const contenedor = document.getElementById('institucionesSeleccionadas');
		const institucionDiv = document.createElement('div');
		institucionDiv.className = 'input-group mt-2';
		institucionDiv.setAttribute('data-id', id);
       
		// Crear los elementos por separado para evitar problemas con comillas
		const inputTexto = document.createElement('input');
		inputTexto.type = 'text';
		inputTexto.className = 'form-control';
		inputTexto.value = nombre;
		inputTexto.readOnly = true;
       
		const inputHidden = document.createElement('input');
		inputHidden.type = 'hidden';
		inputHidden.name = 'instituciones[]';
		inputHidden.value = id;
       
		const btnEliminar = document.createElement('button');
		btnEliminar.type = 'button';
		btnEliminar.className = 'btn btn-danger';
		btnEliminar.innerHTML = '<i class="bi bi-x"></i>';
		btnEliminar.onclick = function() {
			quitarInstitucion(this, id);
		};
       
		institucionDiv.appendChild(inputTexto);
		institucionDiv.appendChild(inputHidden);
		institucionDiv.appendChild(btnEliminar);
       
		contenedor.appendChild(institucionDiv);
		select.selectedIndex = 0;
	}


	function quitarInstitucion(boton, id) {
		// Verificar si es la última institución
		const contenedor = document.getElementById('institucionesSeleccionadas');
		const institucionesActuales = contenedor.querySelectorAll('[data-id]');
       
		if (institucionesActuales.length <= 1) {
			alert('No se puede eliminar la última institución. Una especialidad debe tener al menos una institución asignada.');
			return;
		}
       
		// Remover del array
		institucionesAgregadas = institucionesAgregadas.filter(instId => instId !== id);
		// Remover del DOM
		boton.parentElement.remove();
	}


	// Función para agregar institución cuando se repobla desde old()
	function agregarInstitucionOld(id, nombre) {
		// Verificar si ya está agregada
		if (institucionesAgregadas.includes(id)) {
			return;
		}
       
		// Agregar al array de control
		institucionesAgregadas.push(id);
       
		// Crear elemento visual
		const contenedor = document.getElementById('institucionesSeleccionadas');
		const institucionDiv = document.createElement('div');
		institucionDiv.className = 'input-group mt-2';
		institucionDiv.setAttribute('data-id', id);
       
		// Crear los elementos por separado para evitar problemas con comillas
		const inputTexto = document.createElement('input');
		inputTexto.type = 'text';
		inputTexto.className = 'form-control';
		inputTexto.value = nombre;
		inputTexto.readOnly = true;
       
		const inputHidden = document.createElement('input');
		inputHidden.type = 'hidden';
		inputHidden.name = 'instituciones[]';
		inputHidden.value = id;
       
		const btnEliminar = document.createElement('button');
		btnEliminar.type = 'button';
		btnEliminar.className = 'btn btn-danger';
		btnEliminar.innerHTML = '<i class="bi bi-x"></i>';
		btnEliminar.onclick = function() {
			quitarInstitucion(this, id);
		};
       
		institucionDiv.appendChild(inputTexto);
		institucionDiv.appendChild(inputHidden);
		institucionDiv.appendChild(btnEliminar);
       
		contenedor.appendChild(institucionDiv);
	}


	// ========== ABRIR MODALES CON ERRORES ==========
   
	// Abrir modal de crear si hay errores de validación para crear
	@if (session('modal_crear'))
		document.addEventListener('DOMContentLoaded', function() {
			var modalCrear = new bootstrap.Modal(document.getElementById('modalAgregarEspecialidad'));
			modalCrear.show();
           
			// Repoblar instituciones seleccionadas
			@if(old('instituciones'))
				const institucionesOld = @json(old('instituciones'));
				const institucionesData = @json($instituciones->pluck('nombre', 'id'));
               
				institucionesOld.forEach(function(id) {
					if (institucionesData[id]) {
						agregarInstitucionOld(id, institucionesData[id]);
					}
				});
			@endif
		});
	@endif


	// Abrir modal de editar si hay errores de validación para editar
	@if (session('modal_editar_id'))
		document.addEventListener('DOMContentLoaded', function() {
			var modalEditar = new bootstrap.Modal(document.getElementById('modalEditarEspecialidad-{{ session('modal_editar_id') }}'));
			modalEditar.show();
		});
	@endif


	// ========== FUNCIONES DE BÚSQUEDA ==========


	// Funcionalidad de búsqueda en tiempo real - con verificación de existencia
	document.addEventListener('DOMContentLoaded', function() {
		let timeoutId;
		const inputBusqueda = document.getElementById('inputBusqueda');
		const formBusqueda = document.getElementById('busquedaForm');
		const btnLimpiar = document.getElementById('limpiarBusqueda');
       
		if (inputBusqueda && formBusqueda) {
			inputBusqueda.addEventListener('input', function() {
				clearTimeout(timeoutId);
				timeoutId = setTimeout(function() {
					formBusqueda.submit();
				}, 500);
			});
           
			inputBusqueda.addEventListener('keypress', function(e) {
				if (e.key === 'Enter') {
					e.preventDefault();
					formBusqueda.submit();
				}
			});
		}
       
		if (btnLimpiar) {
			btnLimpiar.addEventListener('click', function() {
				if (inputBusqueda) {
					inputBusqueda.value = '';
				}
				window.location.href = '{{ route("especialidad.index") }}';
			});
		}
       
	});


	// Función simplificada para agregar institución
	function agregarInstitucionSimple(especialidadId) {
		// Ocultar mensaje de validación anterior
		ocultarMensajeValidacion(especialidadId);
       
		const select = document.getElementById(`selectInstitucionEdit-${especialidadId}`);
		const institucionId = select.value;
		const nombreInstitucion = select.options[select.selectedIndex]?.getAttribute('data-nombre');
       
		if (!institucionId) {
			mostrarMensajeValidacion(especialidadId, 'Por favor seleccione una institución');
			return;
		}
       
		// Verificar que no esté ya visible en la interfaz
		const contenedorVisual = document.getElementById(`institucionesVisuales-${especialidadId}`);
		const existeVisual = contenedorVisual.querySelector(`[data-id="${institucionId}"]`);
       
		if (existeVisual) {
			mostrarMensajeValidacion(especialidadId, 'Esta institución ya está asignada a la especialidad');
			return;
		}
       
		// Marcar el checkbox oculto
		const checkbox = document.getElementById(`inst-${especialidadId}-${institucionId}`);
		if (checkbox) {
			checkbox.checked = true;
		}
       
		// Agregar visual
		const elementoVisual = document.createElement('div');
		elementoVisual.className = 'input-group mt-2 institucion-visual';
		elementoVisual.setAttribute('data-id', institucionId);
		elementoVisual.innerHTML = `
			<input type="text" class="form-control" value="${nombreInstitucion}" readonly>
			<button type="button" class="btn btn-danger" onclick="eliminarInstitucionSimple('${especialidadId}', '${institucionId}')">
				<i class="bi bi-x"></i>
			</button>
		`;
		contenedorVisual.appendChild(elementoVisual);
       
		// Resetear select
		select.value = '';
       
		// Actualizar opciones del select para ocultar la institución agregada
		actualizarOpcionesSelect(especialidadId);
	}
   
	// Función simplificada para eliminar institución
	function eliminarInstitucionSimple(especialidadId, institucionId) {
		// Desmarcar checkbox oculto
		const checkbox = document.getElementById(`inst-${especialidadId}-${institucionId}`);
		if (checkbox) {
			checkbox.checked = false;
		}
       
		// Eliminar elemento visual
		const elementoVisual = document.querySelector(`#institucionesVisuales-${especialidadId} .institucion-visual[data-id="${institucionId}"]`);
		if (elementoVisual) {
			elementoVisual.remove();
		}
       
		// Actualizar opciones del select para mostrar la institución nuevamente
		actualizarOpcionesSelect(especialidadId);
	}
   
	// Función para actualizar las opciones del select
	function actualizarOpcionesSelect(especialidadId) {
		const select = document.getElementById(`selectInstitucionEdit-${especialidadId}`);
		const contenedorVisual = document.getElementById(`institucionesVisuales-${especialidadId}`);
       
		// Obtener IDs de instituciones visualmente asignadas
		const institucionesAsignadas = [];
		const elementosVisuales = contenedorVisual.querySelectorAll('.institucion-visual');
		elementosVisuales.forEach(elemento => {
			institucionesAsignadas.push(elemento.getAttribute('data-id'));
		});
       
		// Mostrar/ocultar opciones del select
		Array.from(select.options).forEach(option => {
			if (option.value === '') {
				// Mantener la opción por defecto
				option.style.display = '';
				return;
			}
           
			if (institucionesAsignadas.includes(option.value)) {
				option.style.display = 'none';
			} else {
				option.style.display = '';
			}
		});
       
		// Resetear selección si la opción actual está oculta
		if (select.value && institucionesAsignadas.includes(select.value)) {
			select.value = '';
		}
	}
   
	// Event listener para formularios de editar
	document.addEventListener('DOMContentLoaded', function() {
		// Event listener para formulario de crear
		const modalCrear = document.getElementById('modalAgregarEspecialidad');
		const formCrear = modalCrear.querySelector('form');
       
		if (formCrear) {
			formCrear.addEventListener('submit', function(e) {
				// Ocultar mensajes de validación anteriores
				ocultarMensajeValidacionCrear();
               
				// Validar que haya al menos una institución
				if (institucionesAgregadas.length === 0) {
					e.preventDefault();
					mostrarMensajeValidacionCrear('Debe asignar al menos una institución a la especialidad');
					return false;
				}
			});
		}
       
		// Limpiar mensajes al abrir modal de crear
		modalCrear.addEventListener('shown.bs.modal', function() {
			ocultarMensajeValidacionCrear();
		});
       
		// Limpiar mensajes al cerrar modal de crear
		modalCrear.addEventListener('hidden.bs.modal', function() {
			ocultarMensajeValidacionCrear();
		});
       
		// Event listener para formulario de editar (patrón igual al de Sección)
		const modalEditar = document.getElementById('modalEditarEspecialidad');
       
		if (modalEditar) {
			modalEditar.addEventListener('shown.bs.modal', function(event) {
				const button = event.relatedTarget;
				const especialidadId = button.getAttribute('data-id');
				const form = modalEditar.querySelector('form');
               
				// Ocultar mensajes de validación al abrir el modal
				ocultarMensajeValidacion(especialidadId);
               
				// Actualizar opciones del select al abrir el modal
				actualizarOpcionesSelect(especialidadId);
               
				// Agregar event listener al formulario si no lo tiene
				if (!form.hasAttribute('data-listener-added')) {
					form.addEventListener('submit', function(e) {
						// Ocultar mensajes de validación anteriores
						ocultarMensajeValidacion(especialidadId);
                       
						// Validar instituciones usando checkboxes
						const checkboxesChecked = form.querySelectorAll('input[name="instituciones[]"]:checked');
                       
						if (checkboxesChecked.length === 0) {
							e.preventDefault();
							mostrarMensajeValidacion(especialidadId, 'Debe asignar al menos una institución a la especialidad');
							return false;
						}
					});
                   
					form.setAttribute('data-listener-added', 'true');
				}
			});
           
			// Limpiar mensajes al cerrar modal de editar
			modalEditar.addEventListener('hidden.bs.modal', function() {
				const form = modalEditar.querySelector('form');
				const espIdInput = form ? form.querySelector('input[name="id"]') : null;
				const espId = espIdInput ? espIdInput.value : null;
               
				if (espId) {
					ocultarMensajeValidacion(espId);
				}
			});
		}
	});
</script>
@endsection

