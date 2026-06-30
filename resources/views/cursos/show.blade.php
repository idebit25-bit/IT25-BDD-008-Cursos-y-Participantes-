@extends('home')
@section('title', '- Detalles del Curso')

@section('content')
<style>
    .details-container {
        margin-top: 50px;
        padding-bottom: 50px;
    }
    
    /* Botón de regreso minimalista (tipo flecha circular) */
    .back-arrow {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: rgba(0,0,0,0.05);
        color: #333;
        border-radius: 50%;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }
    .back-arrow:hover {
        background-color: #0d6efd;
        color: white;
        transform: translateX(-3px);
    }

    .details-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
        background: white;
        margin-bottom: 30px;
    }
    .details-header {
        background: #212529;
        padding: 30px;
        color: white;
        border-bottom: 4px solid #0d6efd;
    }
    .details-header h1 {
        margin: 0;
        font-weight: 300;
        font-size: 1.8rem;
    }
    
    .section-card {
        border: none;
        border-radius: 12px;
        background: #fcfcfc;
        padding: 25px;
        margin-bottom: 20px;
        border: 1px solid #f0f0f0;
    }
    .section-title {
        font-size: 0.85rem;
        font-weight: 800;
        color: #0d6efd;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .data-item { margin-bottom: 15px; }
    .data-label { font-weight: 700; color: #666; font-size: 0.8rem; display: block; margin-bottom: 3px; }
    .data-value { color: #212529; font-size: 1rem; }
    .data-value.highlight { color: #28a745; font-weight: 700; }
    
    .btn-download {
        border-radius: 6px;
        padding: 8px 25px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.75rem;
        transition: all 0.3s ease;
        background-color: #dc3545;
        color: white !important;
        border: none;
    }
    .btn-download:hover {
        background-color: #bb2d3b;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
    }

    .link-drive { color: #0d6efd; text-decoration: none; font-weight: 600; font-size: 0.9rem; }
    .link-drive:hover { text-decoration: underline; }
</style>

<div class="container details-container">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            
            <a href="javascript:history.back()" class="back-arrow" title="Regresar">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div class="card details-card">
                <div class="details-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <span class="badge bg-primary mb-2">{{ $curso->Nomenclatura }}</span>
                        <h1>{{ $curso->nombre ?: $curso->NombredelCurso }}</h1>
                    </div>
                    @if(auth()->user()->puesto != 'Operacion')
                        <a href="{{ route('cursos.pdf', $curso->id) }}" class="btn btn-download shadow-sm">
                            <i class="fas fa-file-pdf me-2"></i> Descargar
                        </a>
                    @endif
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="section-card">
                                <h5 class="section-title"><i class="fas fa-info-circle me-2"></i> General</h5>
                                <div class="data-item">
                                    <span class="data-label">Descripción</span>
                                    <div class="data-value text-justify">{{ $curso->descripcion ?: $curso->DescripciondeCurso }}</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="data-item">
                                            <span class="data-label">Instructor</span>
                                            <div class="data-value">{{ $curso->instructor_responsable ?: $curso->InstructorResponsable }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="data-item">
                                            <span class="data-label">Costo</span>
                                            <div class="data-value highlight">${{ number_format((float)($curso->costo ?: $curso->CostodelCurso), 2) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-card">
                                <h5 class="section-title"><i class="fas fa-calendar-alt me-2"></i> Cronograma</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="data-item">
                                            <span class="data-label">Inicio</span>
                                            <div class="data-value">{{ \Carbon\Carbon::parse($curso->fecha_inicio ?: $curso->FechadeInicio)->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="data-item">
                                            <span class="data-label">Término</span>
                                            <div class="data-value">{{ \Carbon\Carbon::parse($curso->fecha_termino ?: $curso->FechadeTermino)->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="data-item">
                                            <span class="data-label">Duración</span>
                                            <div class="data-value">{{ $curso->duracion ?: $curso->Duracioncurso }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-card">
                                <h5 class="section-title"><i class="fas fa-laptop-house me-2"></i> Modalidad</h5>
                                <div class="d-flex flex-wrap gap-3">
                                    @php $v = ($curso->virtual ?: $curso->Virtual) == 'Si' || ($curso->virtual ?: $curso->Virtual) == 'Sí'; @endphp
                                    <div class="px-3 py-2 border rounded-3 {{ $v ? 'bg-primary text-white' : 'bg-light text-muted' }} small fw-bold">VIRTUAL: {{ $v ? 'SÍ' : 'NO' }}</div>
                                    @php $p = ($curso->presencial ?: $curso->Presencial) == 'Si' || ($curso->presencial ?: $curso->Presencial) == 'Sí'; @endphp
                                    <div class="px-3 py-2 border rounded-3 {{ $p ? 'bg-primary text-white' : 'bg-light text-muted' }} small fw-bold">PRESENCIAL: {{ $p ? 'SÍ' : 'NO' }}</div>
                                    @php $m = ($curso->mixto ?: $curso->Mixto) == 'Si' || ($curso->mixto ?: $curso->Mixto) == 'Sí'; @endphp
                                    <div class="px-3 py-2 border rounded-3 {{ $m ? 'bg-primary text-white' : 'bg-light text-muted' }} small fw-bold">MIXTO: {{ $m ? 'SÍ' : 'NO' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="section-card">
                                <h5 class="section-title"><i class="fas fa-file-alt me-2"></i> Recursos</h5>
                                <div class="list-group list-group-flush border rounded mb-3">
                                    <div class="list-group-item d-flex justify-content-between py-2">
                                        <span class="small fw-bold">Temario</span>
                                        @if($curso->drive_temario ?: $curso->DriveTemario) <a href="{{ $curso->drive_temario ?: $curso->DriveTemario }}" target="_blank" class="link-drive">Ver</a> @else <span class="text-muted small">N/A</span> @endif
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between py-2">
                                        <span class="small fw-bold">Itinerario</span>
                                        @if($curso->drive_itinerario ?: $curso->DriveItinerario) <a href="{{ $curso->drive_itinerario ?: $curso->DriveItinerario }}" target="_blank" class="link-drive">Ver</a> @else <span class="text-muted small">N/A</span> @endif
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between py-2">
                                        <span class="small fw-bold">Planeación</span>
                                        @if($curso->drive_planeacion ?: $curso->DrivePlaneación) <a href="{{ $curso->drive_planeacion ?: $curso->DrivePlaneación }}" target="_blank" class="link-drive">Ver</a> @else <span class="text-muted small">N/A</span> @endif
                                    </div>
                                </div>

                                <span class="data-label mb-2">Evaluación y Certificación</span>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-dark border">Diag: {{ $curso->evaluacion_diagnostica ?: $curso->Evaluación_diagnostica }}</span>
                                    <span class="badge bg-light text-dark border">DC3: {{ $curso->dc3 ?: $curso->DC3 }}</span>
                                </div>
                            </div>
                            
                            @if(($curso->udemy ?: $curso->UDEMY) == 'Si' || ($curso->udemy ?: $curso->UDEMY) == 'Sí')
                            <div class="section-card bg-primary text-white border-0">
                                <h5 class="section-title text-white"><i class="fas fa-graduation-cap me-2"></i> UDEMY</h5>
                                <a href="{{ $curso->enlace_udemy ?: $curso->EnlaceUDEMY }}" target="_blank" class="btn btn-sm btn-light w-100 fw-bold">PLATAFORMA</a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
