@extends('home')
@section('title', '- Detalles del Participante')

@section('content')
<style>
    .details-container {
        margin-top: 50px;
        padding-bottom: 50px;
    }
    
    /* Botón de regreso minimalista */
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
</style>

<div class="container details-container">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            
            <a href="{{ route('participantes.index') }}" class="back-arrow" title="Regresar">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div class="card details-card">
                <div class="details-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <span class="badge bg-primary mb-2">ID: {{ $participante->N }}</span>
                        <h1>{{ $participante->NombredelPostulante }}</h1>
                    </div>
                    @if(auth()->user()->puesto != 'Operacion')
                        <a href="{{ route('participantes.descargar-pdf', ['id' => $participante->id]) }}" class="btn btn-download shadow-sm">
                            <i class="fas fa-file-pdf me-2"></i> Descargar
                        </a>
                    @endif
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="row">
                        <!-- Columna Principal -->
                        <div class="col-lg-7">
                            <div class="section-card">
                                <h5 class="section-title"><i class="fas fa-user me-2"></i> Información Personal</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="data-item">
                                            <span class="data-label">Correo Electrónico</span>
                                            <div class="data-value">{{ $participante->Correo }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="data-item">
                                            <span class="data-label">Teléfono</span>
                                            <div class="data-value">{{ $participante->Telefono }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="data-item">
                                            <span class="data-label">Edad</span>
                                            <div class="data-value">{{ $participante->Edad }} años</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="data-item">
                                            <span class="data-label">CURP</span>
                                            <div class="data-value">{{ $participante->Curp }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="data-item">
                                            <span class="data-label">Dirección</span>
                                            <div class="data-value">{{ $participante->Direccion }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="section-card">
                                <h5 class="section-title"><i class="fas fa-building me-2"></i> Información Laboral y Académica</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="data-item">
                                            <span class="data-label">Escolaridad</span>
                                            <div class="data-value">{{ $participante->Escolaridad }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="data-item">
                                            <span class="data-label">Ocupación</span>
                                            <div class="data-value">{{ $participante->Ocupacion }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="data-item">
                                            <span class="data-label">Empresa</span>
                                            <div class="data-value">{{ $participante->Empresa ?: 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="data-item">
                                            <span class="data-label">RFC Empresa</span>
                                            <div class="data-value">{{ $participante->RFCEmpresa ?: 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Columna Secundaria -->
                        <div class="col-lg-5">
                            <div class="section-card">
                                <h5 class="section-title"><i class="fas fa-credit-card me-2"></i> Resumen de Cuenta</h5>
                                
                                <div class="data-item border-bottom pb-2 mb-3">
                                    <span class="data-label">Costo Total de Cursos</span>
                                    <div class="data-value fw-bold text-dark" style="font-size: 1.2rem;">
                                        ${{ number_format($participante->total_a_cobrar, 2) }}
                                    </div>
                                </div>

                                <div class="data-item border-bottom pb-2 mb-3">
                                    <span class="data-label">Monto Pagado</span>
                                    @php
                                        $pago = !empty($participante->pago) && is_numeric($participante->pago) ? floatval($participante->pago) : 0;
                                    @endphp
                                    <div class="data-value highlight" style="font-size: 1.2rem;">
                                        ${{ number_format($pago, 2) }}
                                    </div>
                                </div>

                                <div class="data-item mb-3">
                                    <span class="data-label">Saldo Pendiente</span>
                                    @php
                                        $saldo = $participante->total_a_cobrar - $pago;
                                        $saldoColor = $saldo > 0 ? 'text-danger' : 'text-success';
                                    @endphp
                                    <div class="data-value fw-bold {{ $saldoColor }}" style="font-size: 1.5rem;">
                                        ${{ number_format(max(0, $saldo), 2) }}
                                    </div>
                                </div>

                                <div class="data-item">
                                    <span class="data-label">Estatus de Pago</span>
                                    @php
                                        $badgeClass = match($participante->EstadoDePago) {
                                            'Pagado' => 'bg-success',
                                            'Pendiente' => 'bg-warning text-dark',
                                            'Anticipo' => 'bg-info',
                                            'Cancelado' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} p-2 w-100" style="font-size: 0.9rem;">{{ $participante->EstadoDePago }}</span>
                                </div>
                            </div>

                            <div class="section-card">
                                <h5 class="section-title"><i class="fas fa-graduation-cap me-2"></i> Cursos Inscritos</h5>
                                @if($participante->cursos->isEmpty())
                                    <p class="text-muted small">No hay cursos registrados.</p>
                                @else
                                    <ul class="list-group list-group-flush">
                                        @foreach($participante->cursos as $curso)
                                            <li class="list-group-item px-0 bg-transparent py-2">
                                                <div class="fw-bold small text-dark">{{ $curso->NombredelCurso }}</div>
                                                <div class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="fas fa-calendar-alt me-1"></i> {{ $curso->FechadeInicio ?: ($participante->FechadelCurso ?: 'Fecha no especificada') }}
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
