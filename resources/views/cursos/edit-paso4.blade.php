@extends('layouts.app')

@section('content')
<style>
    .step-container {
        padding: 50px 0;
        background-color: #f4f7f6;
        min-height: calc(100vh - 80px);
    }
    .step-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        background: white;
    }
    .step-header {
        background: linear-gradient(135deg, #000000 0%, #333333 100%);
        padding: 30px;
        color: white;
        text-align: center;
    }
    .step-header h2 {
        font-weight: 300;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin: 0;
    }
    .form-section-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
        border: 1px solid #e9ecef;
    }
    .form-label {
        font-weight: 700;
        color: #495057;
        font-size: 0.75rem;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .input-group-text {
        background-color: #ffffff;
        color: #6c757d;
        border-right: none;
    }
    .form-control {
        border-left: none;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }

    .form-control.porcentaje-completo {
        border-color: #28a745 !important;
        background-color: #f0fff4 !important;
        border-left: 4px solid #28a745 !important;
    }
    .form-control.porcentaje-incompleto {
        border-color: #ffc107 !important;
        background-color: #fffef0 !important;
        border-left: 4px solid #ffc107 !important;
    }
    .form-control.porcentaje-vacio {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
        border-left: 4px solid #dc3545 !important;
        animation: pulse-red 2s ease-in-out infinite;
    }

    .form-control.drive-completo {
        border-color: #28a745 !important;
        background-color: #f0fff4 !important;
        border-left: 4px solid #28a745 !important;
    }
    .form-control.drive-vacio {
        border-color: #dc3545 !important;
        background-color: #fff5f5 !important;
        border-left: 4px solid #dc3545 !important;
        animation: pulse-red 2s ease-in-out infinite;
    }

    @keyframes pulse-red {
        0% { box-shadow: 0 0 12px rgba(220, 53, 69, 0.4); }
        50% { box-shadow: 0 0 25px rgba(220, 53, 69, 0.8); }
        100% { box-shadow: 0 0 12px rgba(220, 53, 69, 0.4); }
    }

    .estado-indicador {
        display: inline-block;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        transition: all 0.4s ease;
        flex-shrink: 0;
        border: 2px solid #e9ecef;
    }
    .estado-indicador.estado-verde {
        background-color: #28a745;
        border-color: #28a745;
        box-shadow: 0 0 12px rgba(40, 167, 69, 0.4);
    }
    .estado-indicador.estado-amarillo {
        background-color: #ffc107;
        border-color: #ffc107;
        box-shadow: 0 0 12px rgba(255, 193, 7, 0.4);
    }
    .estado-indicador.estado-rojo {
        background-color: #dc3545;
        border-color: #dc3545;
        box-shadow: 0 0 12px rgba(220, 53, 69, 0.4);
        animation: pulse-red 2s ease-in-out infinite;
    }

    .documento-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 15px;
        background: #e9ecef;
        border-radius: 6px;
        margin-top: 8px;
        font-size: 0.9rem;
    }
    .documento-preview i {
        font-size: 1.3rem;
        color: #0d6efd;
    }
    .documento-preview .doc-nombre {
        flex: 1;
        font-weight: 500;
        color: #212529;
        word-break: break-all;
    }
    .documento-preview .doc-acciones {
        display: flex;
        gap: 8px;
    }
    .documento-preview .doc-acciones a {
        text-decoration: none;
        font-size: 0.85rem;
        padding: 4px 12px;
        border-radius: 4px;
    }
    .btn-ver-doc {
        background: #0d6efd;
        color: white !important;
    }
    .btn-ver-doc:hover {
        background: #0b5ed7;
    }

    .botones-container {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    .btn-custom {
        border-radius: 6px;
        padding: 12px 35px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    .btn-guardar-verde {
        background-color: #28a745 !important;
        border: 2px solid #28a745 !important;
        color: #ffffff !important;
    }
    .btn-guardar-verde:hover {
        background-color: #218838 !important;
        border-color: #1e7e34 !important;
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3) !important;
    }
    .btn-guardar-verde i {
        color: #ffffff !important;
    }

    .doc-title {
        font-weight: 700;
        text-transform: uppercase;
        color: #333;
        border-bottom: 2px solid #333;
        padding-bottom: 5px;
        margin-bottom: 20px;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .contador-campos {
        text-align: center;
        margin-top: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        font-size: 0.9rem;
        color: #495057;
    }
    .contador-campos span {
        font-weight: 700;
    }
    .contador-campos .completos { color: #28a745; }
    .contador-campos .incompletos { color: #ffc107; }
    .contador-campos .vacios { color: #dc3545; }

    #estadoBadge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.5s ease;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
    }
    #estadoBadge .estado-indicador {
        width: 16px;
        height: 16px;
        border: none;
    }
</style>

<div class="step-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card step-card">
                    <div class="step-header">
                        <h2>Editar Paso 4: Documentos del Curso</h2>
                        <p class="mb-0 mt-2 opacity-75">Actualiza Temario, Itinerario y Planeación</p>

                        <div id="estadoGeneral" class="mt-3">
                            <span id="estadoBadge">
                                <span class="estado-indicador" id="indicadorGeneral"></span>
                                <span id="textoEstado">Verificando campos...</span>
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('cursos.update.paso', [$curso->id, 4]) }}" method="POST" enctype="multipart/form-data" id="formularioCurso">
                            @csrf
                            @method('PUT')

                            <!-- ==========================================
                            TEMARIO
                            ========================================== -->
                            <div class="form-section-card shadow-sm">
                                <div class="doc-title"><i class="fas fa-list-ul"></i> Temario</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="Temario" class="form-label">
                                            Porcentaje
                                            <span class="estado-indicador" id="estado-Temario"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                            <input type="text" name="Temario" id="Temario" 
                                                class="form-control" 
                                                value="{{ old('Temario', $recursos['temario']->url ?? '') }}" 
                                                placeholder="Ej: 100%"
                                                oninput="validarPorcentaje(this)"
                                                onchange="validarPorcentaje(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="DriveTemario" class="form-label">
                                            URL Drive
                                            <span class="estado-indicador" id="estado-DriveTemario"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-google-drive"></i></span>
                                            <input type="text" name="DriveTemario" id="DriveTemario" 
                                                class="form-control" 
                                                value="{{ old('DriveTemario', $recursos['temario']->drive_url ?? '') }}" 
                                                placeholder="https://drive.google.com/..."
                                                oninput="validarDrive(this)"
                                                onchange="validarDrive(this)">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Archivo Local</label>
                                        <input type="file" name="archivoTemario" class="form-control">
                                        @if(isset($recursos['temario_archivo']) && $recursos['temario_archivo'])
                                            <div class="documento-preview">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="doc-nombre">{{ basename($recursos['temario_archivo']->url) }}</span>
                                                <div class="doc-acciones">
                                                    <a href="{{ $recursos['temario_archivo']->archivo_publico_url }}" target="_blank" class="btn-ver-doc">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- ==========================================
                            ITINERARIO
                            ========================================== -->
                            <div class="form-section-card shadow-sm">
                                <div class="doc-title"><i class="fas fa-route"></i> Itinerario</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="Itinerario" class="form-label">
                                            Porcentaje
                                            <span class="estado-indicador" id="estado-Itinerario"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                            <input type="text" name="Itinerario" id="Itinerario" 
                                                class="form-control" 
                                                value="{{ old('Itinerario', $recursos['itinerario']->url ?? '') }}" 
                                                placeholder="Ej: 100%"
                                                oninput="validarPorcentaje(this)"
                                                onchange="validarPorcentaje(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="DriveItinerario" class="form-label">
                                            URL Drive
                                            <span class="estado-indicador" id="estado-DriveItinerario"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-google-drive"></i></span>
                                            <input type="text" name="DriveItinerario" id="DriveItinerario" 
                                                class="form-control" 
                                                value="{{ old('DriveItinerario', $recursos['itinerario']->drive_url ?? '') }}" 
                                                placeholder="https://drive.google.com/..."
                                                oninput="validarDrive(this)"
                                                onchange="validarDrive(this)">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Archivo Local</label>
                                        <input type="file" name="archivoItinerario" class="form-control">
                                        @if(isset($recursos['itinerario_archivo']) && $recursos['itinerario_archivo'])
                                            <div class="documento-preview">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="doc-nombre">{{ basename($recursos['itinerario_archivo']->url) }}</span>
                                                <div class="doc-acciones">
                                                    <a href="{{ $recursos['itinerario_archivo']->archivo_publico_url }}" target="_blank" class="btn-ver-doc">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- ==========================================
                            PLANEACIÓN
                            ========================================== -->
                            <div class="form-section-card shadow-sm">
                                <div class="doc-title"><i class="fas fa-tasks"></i> Planeación</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="Planeacion" class="form-label">
                                            Porcentaje
                                            <span class="estado-indicador" id="estado-Planeacion"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                            <input type="text" name="Planeacion" id="Planeacion" 
                                                class="form-control" 
                                                value="{{ old('Planeacion', $recursos['planeacion']->url ?? '') }}" 
                                                placeholder="Ej: 100%"
                                                oninput="validarPorcentaje(this)"
                                                onchange="validarPorcentaje(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="DrivePlaneacion" class="form-label">
                                            URL Drive
                                            <span class="estado-indicador" id="estado-DrivePlaneacion"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-google-drive"></i></span>
                                            <input type="text" name="DrivePlaneacion" id="DrivePlaneacion" 
                                                class="form-control" 
                                                value="{{ old('DrivePlaneacion', $recursos['planeacion']->drive_url ?? '') }}" 
                                                placeholder="https://drive.google.com/..."
                                                oninput="validarDrive(this)"
                                                onchange="validarDrive(this)">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Archivo Local</label>
                                        <input type="file" name="archivoPlaneacion" class="form-control">
                                        @if(isset($recursos['planeacion_archivo']) && $recursos['planeacion_archivo'])
                                            <div class="documento-preview">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="doc-nombre">{{ basename($recursos['planeacion_archivo']->url) }}</span>
                                                <div class="doc-acciones">
                                                    <a href="{{ $recursos['planeacion_archivo']->archivo_publico_url }}" target="_blank" class="btn-ver-doc">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="contador-campos">
                                <span class="completos" id="totalCompletos">0</span> completos | 
                                <span class="incompletos" id="totalIncompletos">0</span> incompletos | 
                                <span class="vacios" id="totalVacios">0</span> vacíos
                            </div>

                            <div class="botones-container">
                                <button type="submit" class="btn btn-guardar-verde btn-custom shadow-sm">
                                    <i class="fas fa-save me-2"></i> Guardar Cambios
                                </button>
                                <a href="{{ route('cursos.edit', $curso->id) }}" class="btn btn-secondary btn-custom shadow-sm">
                                    <i class="fas fa-arrow-left me-2"></i> Regresar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#Temario, #Itinerario, #Planeacion').forEach(campo => {
        validarPorcentaje(campo);
    });
    document.querySelectorAll('#DriveTemario, #DriveItinerario, #DrivePlaneacion').forEach(campo => {
        validarDrive(campo);
    });
    actualizarContadores();
    actualizarEstadoGeneral();
});

function validarPorcentaje(campo) {
    const valor = campo.value.trim();
    const indicador = document.getElementById('estado-' + campo.id);
    
    campo.classList.remove('porcentaje-completo', 'porcentaje-incompleto', 'porcentaje-vacio');
    if (indicador) {
        indicador.classList.remove('estado-verde', 'estado-amarillo', 'estado-rojo');
    }
    
    if (valor === '') {
        campo.classList.add('porcentaje-vacio');
        if (indicador) indicador.classList.add('estado-rojo');
    } else if (valor === '100%' || valor === '100 %' || valor === '100') {
        campo.classList.add('porcentaje-completo');
        if (indicador) indicador.classList.add('estado-verde');
    } else {
        campo.classList.add('porcentaje-incompleto');
        if (indicador) indicador.classList.add('estado-amarillo');
    }
    
    actualizarContadores();
    actualizarEstadoGeneral();
}

function validarDrive(campo) {
    const valor = campo.value.trim();
    const indicador = document.getElementById('estado-' + campo.id);
    
    campo.classList.remove('drive-completo', 'drive-vacio');
    if (indicador) {
        indicador.classList.remove('estado-verde', 'estado-rojo');
    }
    
    if (valor === '') {
        campo.classList.add('drive-vacio');
        if (indicador) indicador.classList.add('estado-rojo');
    } else {
        campo.classList.add('drive-completo');
        if (indicador) indicador.classList.add('estado-verde');
    }
    
    actualizarContadores();
    actualizarEstadoGeneral();
}

function actualizarContadores() {
    const campos = document.querySelectorAll('#formularioCurso .form-control');
    let completos = 0, incompletos = 0, vacios = 0;
    
    campos.forEach(campo => {
        if (campo.classList.contains('porcentaje-completo') || campo.classList.contains('drive-completo')) {
            completos++;
        } else if (campo.classList.contains('porcentaje-incompleto')) {
            incompletos++;
        } else if (campo.classList.contains('porcentaje-vacio') || campo.classList.contains('drive-vacio')) {
            vacios++;
        }
    });
    
    document.getElementById('totalCompletos').textContent = completos;
    document.getElementById('totalIncompletos').textContent = incompletos;
    document.getElementById('totalVacios').textContent = vacios;
}

function actualizarEstadoGeneral() {
    const campos = document.querySelectorAll('#formularioCurso .form-control');
    let completos = 0, incompletos = 0, vacios = 0;
    const total = campos.length;
    
    campos.forEach(campo => {
        if (campo.classList.contains('porcentaje-completo') || campo.classList.contains('drive-completo')) {
            completos++;
        } else if (campo.classList.contains('porcentaje-incompleto')) {
            incompletos++;
        } else if (campo.classList.contains('porcentaje-vacio') || campo.classList.contains('drive-vacio')) {
            vacios++;
        }
    });
    
    const indicadorGeneral = document.getElementById('indicadorGeneral');
    const textoEstado = document.getElementById('textoEstado');
    
    if (vacios > 0) {
        indicadorGeneral.className = 'estado-indicador estado-rojo';
        textoEstado.textContent = `⚠️ ${vacios} campo(s) vacío(s) - Requiere atención`;
    } else if (incompletos > 0) {
        indicadorGeneral.className = 'estado-indicador estado-amarillo';
        textoEstado.textContent = `🟡 ${incompletos} campo(s) incompleto(s) - Deben ser 100%`;
    } else if (completos === total) {
        indicadorGeneral.className = 'estado-indicador estado-verde';
        textoEstado.textContent = '✅ Todos los campos completos - Listo para guardar';
    } else {
        indicadorGeneral.className = 'estado-indicador';
        textoEstado.textContent = 'Verificando campos...';
    }
}
</script>
@endsection