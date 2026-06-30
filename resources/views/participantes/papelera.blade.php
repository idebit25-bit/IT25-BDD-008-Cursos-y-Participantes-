@extends('home')
@section('title', '- Papelera de Participantes')

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
    
    .btn-action-p {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s ease;
        height: 34px;
        min-width: 130px;
        margin: 2px;
        border: none;
        color: white !important;
    }
    .btn-action-p:hover { transform: translateY(-1px); opacity: 0.9; }
    .btn-success-p { background-color: #28a745; }
    .btn-dark-p { background-color: #212529; }
    .btn-secondary-p { background-color: #6c757d; }

    tr.inactive-row { background-color: #fdfdfd; }
</style>

<div class="container-fluid">
    <div class="table-container">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h1 class="h3 mb-0" style="font-weight: 300; color: #0b0b0b;">Papelera de Participantes</h1>
            <a href="{{ route('participantes.index') }}" class="btn-action-p btn-secondary-p shadow-sm" style="min-width: 180px; height: 40px;">
                <i class="fas fa-arrow-left me-2"></i> Volver a Lista
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center">N°</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Empresa</th>
                        <th>Fecha Baja</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($participantes as $participante)
                    <tr class="inactive-row">
                        <td class="text-center text-muted">#{{ $participante->N }}</td>
                        <td class="fw-bold">{{ $participante->NombredelPostulante }}</td>
                        <td>{{ $participante->Correo }}</td>
                        <td>{{ $participante->Telefono }}</td>
                        <td>{{ $participante->Empresa ?: 'N/A' }}</td>
                        <td class="small text-muted">{{ $participante->updated_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="d-flex justify-content-center flex-wrap">
                                <form action="{{ route('participantes.activar', $participante->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn-action-p btn-success-p" title="Reactivar">
                                        <i class="fas fa-undo me-1"></i> Reactivar
                                    </button>
                                </form>

                                <button class="btn-action-p btn-dark-p" data-bs-toggle="modal" data-bs-target="#modalEliminar{{ $participante->id }}">
                                    <i class="fas fa-trash-alt me-1"></i> Borrar Def.
                                </button>
                            </div>

                            <!-- Modal Confirmación Password -->
                            <div class="modal fade" id="modalEliminar{{ $participante->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('participantes.eliminar-definitivo', $participante->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-content border-0 shadow-lg">
                                            <div class="modal-header bg-dark text-white border-0">
                                                <h5 class="modal-title" style="font-weight: 300;">Seguridad de Eliminación</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4 text-dark text-start">
                                                <p>¿Estás seguro de eliminar a <strong>{{ $participante->NombredelPostulante }}</strong>?</p>
                                                <p class="text-danger small fw-bold">Esta acción es irreversible.</p>
                                                <div class="mb-3 mt-4">
                                                    <label class="form-label small fw-bold text-muted">Contraseña de Administrador:</label>
                                                    <input type="password" name="password" class="form-control" required style="border-radius: 8px;">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 p-4 pt-0">
                                                <button type="button" class="btn btn-light btn-action-p text-dark" data-bs-dismiss="modal" style="min-width: 100px;">Cancelar</button>
                                                <button type="submit" class="btn btn-danger btn-action-p" style="min-width: 150px;">Eliminar para siempre</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">La papelera se encuentra vacía.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $participantes->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
