@extends('home')
@section('title', '- Editar Curso')
@section('nav')

<style>
    .container {
        margin-top: 150px !important;
        max-width: 1000px !important;
    }
    h2 {
        text-align: center;
        margin-bottom: 40px;
        font-weight: bold;
        color: #333;
    }
    .step-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        padding: 15px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid #eee;
    }
    .step-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        border-color: #0d6efd;
    }
    .step-number-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
        margin-right: 20px;
        flex-shrink: 0;
    }
    .step-content {
        flex-grow: 1;
    }
    .step-title {
        display: block;
        font-weight: bold;
        color: #444;
        font-size: 1.1rem;
    }
    .step-subtitle {
        display: block;
        color: #777;
        font-size: 0.9rem;
    }
    
    /* Colores basados en el estado */
    .btn-success-custom { background-color: #28a745; }
    .btn-warning-custom { background-color: #ffc107; color: #333 !important; }
    .btn-danger-custom { background-color: #dc3545; }
    .btn-secondary-custom { background-color: #6c757d; }

    .exit-btn {
        margin-top: 30px;
        padding: 12px 40px;
        font-size: 1.1rem;
        border-radius: 6px;
    }
</style>

<div class="container">
    <h2 class="mb-4">Editar Curso: <span class="text-primary">{{ $curso->NombredelCurso }}</span></h2>

    <div class="row">
        @for ($paso = 1; $paso <= 7; $paso++)
            <div class="col-md-6">
                @php
                    $progresoInfo = $coloresPorPaso[$paso] ?? ['class' => 'btn-secondary', 'texto' => 'Pendiente'];
                    $colorClass = str_replace('btn-', 'btn-', $progresoInfo['class']) . '-custom';
                    $textoEstado = $progresoInfo['texto'];
                    $titles = [
                        1 => 'Datos del Curso',
                        2 => 'Modalidad del Curso',
                        3 => 'Formato de Flyer / Imagen',
                        4 => 'Documentos del Curso',
                        5 => 'Material de Apoyo',
                        6 => 'Documentos de Evaluación',
                        7 => 'Documentación STPS y Certificados'
                    ];
                    $subtitles = [
                        1 => 'Nomenclatura, nombre, costo, fechas...',
                        2 => 'Virtual, presencial o mixto',
                        3 => 'Links de redes sociales y flyers',
                        4 => 'Temario, itinerario y planeación',
                        5 => 'Digital o impreso presentable',
                        6 => 'Evaluaciones y DC3',
                        7 => 'DC5, UDEMY y comprobaciones'
                    ];
                @endphp
                <a href="{{ route('cursos.edit.paso', [$curso->id, $paso]) }}" class="step-card">
                    <div class="step-number-circle {{ $colorClass }}">
                        {{ $paso }}
                    </div>
                    <div class="step-content">
                        <span class="step-title">{{ $titles[$paso] }}</span>
                        <span class="step-subtitle">{{ $subtitles[$paso] }} ({{ $textoEstado }})</span>
                    </div>
                    <div class="step-arrow">
                        <i class="fas fa-chevron-right text-muted"></i>
                    </div>
                </a>
            </div>
        @endfor
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('cursos.index') }}" class="btn btn-danger exit-btn">
            <i class="fas fa-sign-out-alt me-2"></i> Salir de Edición
        </a>
    </div>
</div>
@endsection
