@extends('Template-administrador')

@section('title', 'Reporte de Bitácoras')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i>Reporte de Bitácoras</h2>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filtros de Búsqueda</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reporte.bitacoras') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                                       value="{{ request('fecha_inicio') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                                       value="{{ request('fecha_fin') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="recinto_id" class="form-label">Recinto</label>
                                <select class="form-select" id="recinto_id" name="recinto_id">
                                    <option value="">Todos los recintos</option>
                                    @foreach($recintos as $recinto)
                                        <option value="{{ $recinto->id }}" 
                                                {{ request('recinto_id') == $recinto->id ? 'selected' : '' }}>
                                            {{ $recinto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-search me-1"></i>Filtrar
                                </button>
                                <a href="{{ route('reporte.bitacoras') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de resultados -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Registros de Bitácoras ({{ $bitacoras->total() }} resultados)</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Fecha Creación</th>
                                    <th>Recinto</th>
                                    <th>Llave</th>
                                    <th>Estado Llave</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bitacoras as $bitacora)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $bitacora->id }}</span></td>
                                        <td>{{ $bitacora->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-building me-2 text-success"></i>
                                                {{ $bitacora->recinto->nombre ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-key me-2 text-warning"></i>
                                                {{ $bitacora->llave->nombre ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($bitacora->llave)
                                                <span class="badge {{ $bitacora->llave->estado_badge_class }}">
                                                    {{ $bitacora->llave->estado_entrega_text }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Activa
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-inbox display-1 d-block mb-3"></i>
                                                <h5>No se encontraron registros</h5>
                                                <p>No hay bitácoras que coincidan con los filtros seleccionados.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($bitacoras->hasPages())
                    <div class="card-footer">
                        {{ $bitacoras->withQueryString()->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
