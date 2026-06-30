@extends('home')
@section('title', '- Lista de Participantes Inscritos')

@section('content')
<style>
    .container-fluid { padding: 20px; }
    .table-container { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .table thead th { background-color: #212529 !important; color: white !important; padding: 15px; }
    .sticky-col { position: sticky !important; right: 0; background-color: white !important; z-index: 5; box-shadow: -5px 0 10px rgba(0,0,0,0.05); }
    /* Botones uniformes */
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px !important;
        padding: 6px 15px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
        height: 38px;
        width: auto;
        min-width: 100px;
        margin: 2px;
        border: none;
        color: white !important;
        text-decoration: none;
    }
    .btn-action:hover {
        transform: translateY(-1px);
        opacity: 0.9;
        color: white !important;
    }
    .btn-action i {
        margin-right: 8px;
    }

    .btn-info-p { background-color: #17a2b8; }
    .btn-warning-p { background-color: #ffc107; color: #212529 !important; }
    .btn-danger-p { background-color: #dc3545; }
    .btn-success-p { background-color: #28a745; }
    .btn-secondary-p { background-color: #6c757d; }
    .btn-primary-p { background-color: #0d6efd; }
    .btn-dark-p { background-color: #212529; }
</style>

<div class="container-fluid">
    <div class="table-container">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <h1 class="h3 mb-0" style="font-weight: 300;">Participantes Inscritos</h1>
            </div>
            <div class="col-md-6 text-end">
                <div class="d-flex justify-content-end gap-2 flex-wrap">
                    <button id="toggleFilters" class="btn-action btn-dark-p shadow-sm" style="height: 40px; width: auto; min-width: 110px;">
                        <i class="fas fa-filter"></i> Filtros
                    </button>
                    @if(auth()->user()?->puesto != 'Operacion')
                        <a href="{{ route('exportar.excel') }}" class="btn-action btn-success-p shadow-sm" style="height: 40px; width: auto; min-width: 110px;">
                            <i class="fas fa-file-excel"></i> Excel
                        </a>
                        <a href="{{ route('exportar.csv') }}" class="btn-action btn-primary-p shadow-sm" style="height: 40px; width: auto; min-width: 110px;">
                            <i class="fas fa-file-csv"></i> CSV
                        </a>
                        <a href="{{ route('participantes.papelera') }}" class="btn-action btn-secondary-p shadow-sm" style="height: 40px; width: auto; min-width: 110px;">
                            <i class="fas fa-trash-alt"></i> Papelera
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div id="filtersContainer" class="card p-3 mb-4 bg-light" style="display: none;">
            <form action="{{ route('participantes.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label>Curso:</label>
                    <select name="curso" class="form-control">
                        <option value="">Todos</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->id }}" {{ request('curso') == $curso->id ? 'selected' : '' }}>{{ $curso->nombre ?: $curso->NombredelCurso }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Estado Pago:</label>
                    <select name="estado_pago" class="form-control">
                        <option value="">Todos</option>
                        <option value="Pagado" {{ request('estado_pago') == 'Pagado' ? 'selected' : '' }}>Pagado</option>
                        <option value="Pendiente" {{ request('estado_pago') == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
                        <option value="Anticipo" {{ request('estado_pago') == 'Anticipo' ? 'selected' : '' }}>Anticipo</option>
                        <option value="Cancelado" {{ request('estado_pago') == 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-p me-2" style="height: 38px; min-width: 100px;">
                        <i class="fas fa-filter me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('participantes.index') }}" class="btn btn-secondary-p" style="height: 38px; min-width: 100px;">
                        <i class="fas fa-sync-alt me-1"></i> Limpiar
                    </a>
                </div>
            </form>
        </div>

    <form method="GET" action="{{ route('participantes.index') }}" class="mb-4">
        <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
            <input type="text" name="search" class="form-control border-0" placeholder="Buscar por nombre, correo, empresa..." value="{{ request('search') }}" style="height: 45px;">
            <button class="btn btn-dark" type="submit" style="min-width: 80px; border-radius: 6px; margin: 0; height: 45px;"><i class="fas fa-search"></i></button>
        </div>
    </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Nombre</th>
                        <th>Cursos Inscritos</th>
                        <th>Correo</th>
                        <th>Empresa</th>
                        <th>Pago</th>
                        <th>Estado</th>
                        <th class="text-center sticky-col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participantes as $participante)
                    <tr>
                        <td>{{ $participante->N }}</td>
                        <td class="fw-bold">{{ $participante->NombredelPostulante }}</td>
                        <td>
                            @if($participante->cursos->count() > 0)
                                @foreach($participante->cursos as $curso)
                                    <span class="badge bg-light text-dark border mb-1" style="font-size: 0.65rem; display: block; white-space: normal; text-align: left; font-weight: 500;">
                                        <i class="fas fa-book text-muted me-1"></i> {{ $curso->nombre ?: $curso->NombredelCurso }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted small" style="font-size: 0.65rem;">Sin cursos</span>
                            @endif
                        </td>
                        <td>{{ $participante->Correo }}</td>
                        <td>{{ $participante->Empresa }}</td>
                        <td>${{ number_format(floatval($participante->Pago) ?: 0, 2) }}</td>
                        <td>
                            <span class="badge bg-light text-dark border" style="padding: 8px; min-width: 85px; font-weight: 500;">{{ $participante->EstadoDePago }}</span>
                        </td>
                        <td class="text-center sticky-col">
                            <div class="d-flex justify-content-center align-items-center flex-nowrap" style="gap: 5px;">
                                <a href="{{ route('participantes.detalles', ['id' => $participante->id]) }}" class="btn-action btn-info-p" title="Ver">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                @if(auth()->user()?->puesto != 'Operacion')
                                    <a href="{{ route('participantes.edit', ['id' => $participante->id]) }}" class="btn-action btn-warning-p" title="Editar">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <form action="{{ route('participantes.destroy', ['id' => $participante->id]) }}" method="POST" onsubmit="return confirm('¿Mover participante a la papelera?')" class="m-0 p-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action btn-danger-p" title="Eliminar">
                                            <i class="fas fa-trash"></i> Borrar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">No hay participantes.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $participantes->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<script>
document.getElementById('toggleFilters').addEventListener('click', function () {
    var filtersContainer = document.getElementById('filtersContainer');
    filtersContainer.style.display = filtersContainer.style.display === 'none' ? 'block' : 'none';
});
</script>
@endsection
