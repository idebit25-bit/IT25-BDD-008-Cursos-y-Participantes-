@extends('home')
@section('title', '- Papelera de Cursos')

@section('content')
<style>
    .table-container { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin: 20px; }
    .table thead th { 
        background-color: #212529 !important; 
        color: white !important; 
        padding: 15px;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }
    .table td { vertical-align: middle; }
    
    .btn-action-c {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
        height: 34px;
        min-width: 120px;
        margin: 2px;
        border: none;
        color: white !important;
        text-decoration: none;
    }
    .btn-action-c:hover { transform: translateY(-1px); opacity: 0.9; color: white !important; }
    .btn-success-c { background-color: #28a745; }
    .btn-dark-c { background-color: #212529; }
    .btn-secondary-c { background-color: #6c757d; }

    .section-divider {
        border-bottom: 2px solid #f1f1f1;
        margin: 40px 0 30px 0;
        padding-bottom: 10px;
        display: flex;
        align-items: center;
    }
    .section-divider i { margin-right: 10px; color: #6c757d; }
    .section-divider h4 { margin-bottom: 0; font-weight: 300; color: #333; }
</style>

<div class="container-fluid">
    <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h1 class="h3 mb-0" style="font-weight: 300; color: #070606;">Papelera de Cursos y Subcursos</h1>
            <a href="{{ route('cursos.index') }}" class="btn-action-c btn-secondary-c shadow-sm" style="min-width: 180px; height: 40px;">
                <i class="fas fa-arrow-left me-2"></i> Volver a Cursos
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- SECCIÓN DE CURSOS -->
        <div class="section-divider">
            <i class="fas fa-book fa-lg"></i>
            <h4>Cursos Principales</h4>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center">Nomenclatura</th>
                        <th>Nombre del Curso</th>
                        <th>Instructor</th>
                        <th class="text-center">Costo</th>
                        <th>Fecha Baja</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cursos as $curso)
                        <tr>
                            <td class="text-center fw-bold text-primary">{{ $curso->Nomenclatura ?: $curso->nomenclatura }}</td>
                            <td>{{ $curso->NombredelCurso ?: $curso->nombre }}</td>
                            <td>{{ $curso->InstructorResponsable ?: $curso->instructor_responsable }}</td>
                            <td class="text-center text-success fw-bold">${{ number_format((float)($curso->CostodelCurso ?: $curso->costo), 2) }}</td>
                            <td class="small text-muted">{{ $curso->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="d-flex justify-content-center flex-wrap">
                                    <form action="{{ route('cursos.activar', $curso->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-action-c btn-success-c" title="Reactivar">
                                            <i class="fas fa-undo me-1"></i> Reactivar
                                        </button>
                                    </form>

                                    <button class="btn-action-c btn-dark-c" data-bs-toggle="modal" data-bs-target="#modalDeleteCurso{{ $curso->id }}">
                                        <i class="fas fa-trash-alt me-1"></i> Borrar Def.
                                    </button>
                                </div>

                                <!-- Modal Confirmación Curso -->
                                <div class="modal fade" id="modalDeleteCurso{{ $curso->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('cursos.eliminar-definitivo', $curso->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-dark text-white border-0">
                                                    <h5 class="modal-title" style="font-weight: 300;">Seguridad de Eliminación</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 text-dark text-start">
                                                    <p>¿Estás seguro de eliminar el curso <strong>{{ $curso->NombredelCurso ?: $curso->nombre }}</strong>?</p>
                                                    <p class="text-danger small fw-bold">Esta acción eliminará también sus subcursos asociados de forma permanente.</p>
                                                    <div class="mb-3 mt-4">
                                                        <label class="form-label small fw-bold text-muted">Contraseña de Administrador:</label>
                                                        <input type="password" name="password" class="form-control" required style="border-radius: 6px;">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0">
                                                    <button type="button" class="btn btn-light btn-action-c text-dark" data-bs-dismiss="modal" style="min-width: 100px;">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger btn-action-c" style="min-width: 150px;">Eliminar para siempre</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No hay cursos principales en la papelera.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- SECCIÓN DE SUBCURSOS -->
        <div class="section-divider">
            <i class="fas fa-level-down-alt fa-lg"></i>
            <h4>Subcursos</h4>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center">Nomenclatura</th>
                        <th>Nombre del Subcurso</th>
                        <th>Curso Padre</th>
                        <th class="text-center">Costo</th>
                        <th>Fecha Baja</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($subcursos as $sub)
                        <tr>
                            <td class="text-center fw-bold text-primary">{{ $sub->Nomenclatura ?: $sub->nomenclatura }}</td>
                            <td>{{ $sub->NombredelCurso ?: $sub->nombre }}</td>
                            <td class="small">
                                @php
                                    $padre = \App\Models\Cursos::find($sub->parent_id);
                                @endphp
                                <span class="text-muted">{{ $padre ? $padre->nombre : 'Desconocido' }}</span>
                            </td>
                            <td class="text-center text-success fw-bold">${{ number_format((float)($sub->CostodelCurso ?: $sub->costo), 2) }}</td>
                            <td class="small text-muted">{{ $sub->updated_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="d-flex justify-content-center flex-wrap">
                                    <form action="{{ route('cursos.activar', $sub->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-action-c btn-success-c" title="Reactivar">
                                            <i class="fas fa-undo me-1"></i> Reactivar
                                        </button>
                                    </form>

                                    <button class="btn-action-c btn-dark-c" data-bs-toggle="modal" data-bs-target="#modalDeleteSub{{ $sub->id }}">
                                        <i class="fas fa-trash-alt me-1"></i> Borrar Def.
                                    </button>
                                </div>

                                <!-- Modal Confirmación Subcurso -->
                                <div class="modal fade" id="modalDeleteSub{{ $sub->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('cursos.eliminar-definitivo', $sub->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-dark text-white border-0">
                                                    <h5 class="modal-title" style="font-weight: 300;">Eliminar Subcurso</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4 text-dark text-start">
                                                    <p>¿Estás seguro de eliminar el subcurso <strong>{{ $sub->NombredelCurso ?: $sub->nombre }}</strong>?</p>
                                                    <p class="text-danger small fw-bold">Esta acción no se puede deshacer.</p>
                                                    <div class="mb-3 mt-4">
                                                        <label class="form-label small fw-bold text-muted">Contraseña de Administrador:</label>
                                                        <input type="password" name="password" class="form-control" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0">
                                                    <button type="button" class="btn btn-light btn-action-c text-dark" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger btn-action-c">Eliminar permanentemente</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No hay subcursos en la papelera.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
