@extends('home')
@section('title', '- Lista de Cursos')

@section('content')
<style>
    /* Estilo del cursor (scrollbar) en negro */
    ::-webkit-scrollbar {
        width: 10px;
    }
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 5px;
    }
    ::-webkit-scrollbar-thumb {
        background: #212529;
        border-radius: 5px;
    }
    ::-webkit-scrollbar-thumb:hover {
        background: #000;
    }

    .container-fluid { padding: 20px; }
    .table-container { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
    .table thead th { background-color: #000 !important; color: white !important; padding: 15px; white-space: nowrap; }
    .table td { vertical-align: middle; }
    
    .btn-expand { background: none; border: none; color: #0d6efd; font-size: 1.2rem; cursor: pointer; transition: transform 0.2s; }
    .btn-expand.active { transform: rotate(45deg); color: #dc3545; }
    
    .sticky-col { 
        position: sticky !important; 
        right: 0; 
        background-color: white !important; 
        z-index: 5; 
        box-shadow: -5px 0 10px rgba(0,0,0,0.05); 
    }
    tr:hover .sticky-col { background-color: #f8f9fa !important; }
    
    /* Botones uniformes */
    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
        height: 38px;
        width: 120px;
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

    .btn-info-c { background-color: #17a2b8; }
    .btn-warning-c { background-color: #ffc107; color: #212529 !important; }
    .btn-success-c { background-color: #28a745; }
    .btn-secondary-c { background-color: #6c757d; }
    .btn-danger-c { background-color: #dc3545; }
    .btn-primary-c { background-color: #0d6efd; }

    .expanded-section { background-color: #fcfcfc; padding: 25px; border-radius: 0 0 10px 10px; border: 1px solid #eee; border-top: none; }
    .info-label { font-weight: bold; color: #555; }

    /* Paginación estilo LOGS (DataTables/Dark) */
    .pagination-wrapper {
        display: flex;
        justify-content: flex-end;
        margin-top: 30px;
    }
    .pagination {
        gap: 5px;
    }
    .pagination .page-link {
        color: #333 !important;
        border: none !important;
        background: transparent !important;
        border-radius: 6px !important;
        padding: 8px 16px !important;
        font-weight: 500;
        transition: all 0.3s ease;
        margin: 0 2px;
    }
    .pagination .page-item.active .page-link {
        background-color: #000 !important;
        color: white !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .pagination .page-link:hover {
        background-color: #000 !important;
        color: #fff !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        transform: translateY(-1px);
    }
    .pagination .page-item.disabled .page-link {
        background-color: transparent !important;
        color: #ccc !important;
        box-shadow: none !important;
        transform: none !important;
    }
</style>

<div class="container-fluid">
    <div class="table-container">
        <div class="row mb-4 align-items-center">
            <div class="col-md-4">
                <h1 class="h3 mb-0" style="font-weight: 300;">Gestión de Cursos</h1>
            </div>
            <div class="col-md-8 text-end">
                <div class="d-flex justify-content-end gap-2 flex-wrap">
                    <form action="{{ route('cursos.index') }}" method="GET" class="d-flex me-2">
                        <div class="input-group shadow-sm" style="border-radius: 6px; overflow: hidden;">
                            <input type="text" name="search" class="form-control border-0" placeholder="Buscar curso..." value="{{ request('search') }}" style="height: 40px;">
                            <button type="submit" class="btn btn-dark" style="border-radius: 6px; margin: 0; height: 40px; min-width: 50px;"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                    <a href="{{ route('exportar.cursos.excel') }}" class="btn-action btn-success-c shadow-sm" style="width: auto; min-width: 110px; height: 40px;">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <a href="{{ route('exportar.cursos.csv') }}" class="btn-action btn-info-c shadow-sm" style="width: auto; min-width: 110px; height: 40px;">
                        <i class="fas fa-file-csv"></i> CSV
                    </a>
                    <a href="{{ route('curso.iniciar') }}" class="btn-action btn-primary-c shadow-sm" style="width: auto; min-width: 110px; height: 40px;">
                        <i class="fas fa-plus"></i> Nuevo
                    </a>
                    <a href="{{ route('cursos.papelera') }}" class="btn-action btn-secondary-c shadow-sm" style="width: auto; min-width: 110px; height: 40px;">
                        <i class="fas fa-trash-alt"></i> Papelera
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;"></th>
                        <th>Nomenclatura</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Instructor</th>
                        <th>Duración</th>
                        <th>Costo</th>
                        <th>Inicio</th>
                        <th>Término</th>
                        <th>Estatus</th>
                        <th class="text-center sticky-col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cursos as $curso)
                        <tr id="row-{{ $curso->id }}">
                            <td class="text-center">
                                <button class="btn-expand" onclick="toggleDetails({{ $curso->id }})">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            </td>
                            <td class="fw-bold text-primary">{{ $curso->nomenclatura }}</td>
                            <td class="fw-bold">{{ $curso->nombre }}</td>
                            <td title="{{ $curso->descripcion }}">{{ \Illuminate\Support\Str::limit($curso->descripcion, 30) }}</td>
                            <td>{{ $curso->instructor_responsable }}</td>
                            <td>{{ $curso->duracion }}</td>
                            <td class="fw-bold text-success">${{ number_format((float)$curso->costo, 2) }}</td>
                            <td class="small">{{ $curso->fecha_inicio ? \Carbon\Carbon::parse($curso->fecha_inicio)->format('d/m/Y') : '-' }}</td>
                            <td class="small">{{ $curso->fecha_termino ? \Carbon\Carbon::parse($curso->fecha_termino)->format('d/m/Y') : '-' }}</td>
                            <td>
                                @php $estatus = $curso->estatus_progreso; @endphp
                                <span class="badge {{ $estatus['color'] }}" style="padding: 8px; min-width: 80px;">
                                    {{ $estatus['texto'] }}
                                </span>
                            </td>
                            <td class="text-center sticky-col">
                                <div class="d-flex justify-content-center flex-wrap" style="min-width: 260px;">
                                    <a href="{{ route('cursos.show', $curso->id) }}" class="btn-action btn-info-c" title="Ver">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    @if(auth()->user()?->puesto != 'Operacion')
                                        <a href="{{ route('cursos.edit', $curso->id) }}" class="btn-action btn-warning-c {{ $curso->status != 1 ? 'disabled' : '' }}" title="Editar">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form action="{{ route('cursos.toggle-status', $curso->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn-action {{ $curso->status == 1 ? 'btn-secondary-c' : 'btn-success-c' }}" title="{{ $curso->status == 1 ? 'Desactivar' : 'Activar' }}">
                                                <i class="fas fa-power-off"></i> {{ $curso->status == 1 ? 'Pausar' : 'Activar' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('cursos.destroy', $curso->id) }}" method="POST" onsubmit="return confirm('¿Enviar curso a la papelera?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-action btn-danger-c" title="Eliminar">
                                                <i class="fas fa-trash"></i> Borrar
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        <tr id="details-{{ $curso->id }}" style="display: none;">
                            <td colspan="11" class="p-0">
                                <div class="expanded-section">
                                    <div class="mb-4 text-start">
                                        <h5 class="mb-3 text-dark" style="font-weight: 300;">
                                             Subcursos de: <span class="fw-bold text-primary">{{ $curso->nombre }}</span>
                                        </h5>
                                        @if(auth()->user()?->puesto != 'Operacion')
                                            <a href="{{ route('subcursos.iniciar', $curso->id) }}" class="btn-action btn-primary-c shadow-sm" style="width: auto; padding: 0 20px; height: 38px;">
                                                <i class="fas fa-plus me-2"></i> Agregar Subcurso
                                            </a>
                                        @endif
                                    </div>
                                    <div id="sub-list-{{ $curso->id }}">
                                        <div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-muted"></i></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-center py-5 text-muted">No se encontraron cursos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">
            {{ $cursos->links('vendor.pagination.custom-dark') }}
        </div>
    </div>
</div>

<script>
    function toggleDetails(id) {
        const detailsRow = document.getElementById(`details-${id}`);
        const button = document.querySelector(`#row-${id} .btn-expand`);
        const icon = button.querySelector('i');
        if (detailsRow.style.display === 'none') {
            detailsRow.style.display = 'table-row';
            button.classList.add('active');
            icon.classList.replace('fa-plus-circle', 'fa-minus-circle');
            loadSubcourses(id);
        } else {
            detailsRow.style.display = 'none';
            button.classList.remove('active');
            icon.classList.replace('fa-minus-circle', 'fa-plus-circle');
        }
    }

    function loadSubcourses(parentId) {
        const container = document.getElementById(`sub-list-${parentId}`);
        
        fetch(`/cursos/subcursos/${parentId}`)
            .then(response => response.json())
            .then(data => {
                let html = `<div class="table-responsive"><table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nomenclatura</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Instructor</th>
                                        <th>Duración</th>
                                        <th>Costo</th>
                                        <th>Inicio</th>
                                        <th>Término</th>
                                        <th>Estatus</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>`;
                
                if (data.length > 0) {
                    data.forEach(sub => {
                        const estatus = sub.estatus_progreso;
                        const statusBadge = `<span class="badge ${estatus.color}" style="padding: 8px; min-width: 80px;">${estatus.texto}</span>`;
                        const desc = sub.descripcion ? (sub.descripcion.length > 30 ? sub.descripcion.substring(0,30) + '...' : sub.descripcion) : '-';
                        
                        html += `<tr>
                                    <td class="fw-bold text-primary">${sub.nomenclatura || 'N/A'}</td>
                                    <td class="fw-bold">${sub.nombre}</td>
                                    <td title="${sub.descripcion || ''}">${desc}</td>
                                    <td>${sub.instructor_responsable || '-'}</td>
                                    <td>${sub.duracion || '-'}</td>
                                    <td class="text-success fw-bold">$${sub.costo}</td>
                                    <td class="small">${sub.fecha_inicio || '-'}</td>
                                    <td class="small">${sub.fecha_termino || '-'}</td>
                                    <td>${statusBadge}</td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <a href="/cursos/${sub.id}" class="btn-action btn-info-c" style="width: 80px; height: 32px; font-size: 11px;" title="Ver">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                            @if(auth()->user()?->puesto != 'Operacion')
                                                <a href="/cursos/${sub.id}/edit" class="btn-action btn-warning-c" style="width: 80px; height: 32px; font-size: 11px;" title="Editar">
                                                    <i class="fas fa-edit"></i> Editar
                                                </a>
                                                <form action="/cursos/${sub.id}/toggle-status" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn-action ${sub.status == 1 ? 'btn-secondary-c' : 'btn-success-c'}" style="width: 80px; height: 32px; font-size: 11px;">
                                                        <i class="fas fa-power-off"></i> ${sub.status == 1 ? 'Pausar' : 'Activar'}
                                                    </button>
                                                </form>
                                                <form action="/cursos/${sub.id}" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar subcurso?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-action btn-danger-c" style="width: 80px; height: 32px; font-size: 11px;">
                                                        <i class="fas fa-trash"></i> Borrar
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                 </tr>`;
                    });
                } else {
                    html += `<tr><td colspan="9" class="text-center py-4 text-muted small">No hay subcursos registrados para este curso.</td></tr>`;
                }
                
                html += `</tbody></table></div>`;
                container.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<div class="text-danger py-3 small text-center">Error al cargar la lista de subcursos.</div>';
            });
    }
</script>
@endsection
