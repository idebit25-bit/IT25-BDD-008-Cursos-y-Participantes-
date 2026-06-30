@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
    .form-control, .form-select {
        border-left: none;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: none;
        border-color: #dee2e6;
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

    .form-select.select-completo {
        border-color: #28a745 !important;
        background-color: #f0fff4 !important;
        border-left: 4px solid #28a745 !important;
    }
    .form-select.select-vacio {
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

    .stps-title {
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
                        <h2>Editar Paso 7: Documentación STPS y Certificados</h2>
                        <p class="mb-0 mt-2 opacity-75">Actualiza trámites oficiales y certificados</p>

                        <div id="estadoGeneral" class="mt-3">
                            <span id="estadoBadge">
                                <span class="estado-indicador" id="indicadorGeneral"></span>
                                <span id="textoEstado">Verificando campos...</span>
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('cursos.update.paso', [$curso->id, 7]) }}" method="POST" enctype="multipart/form-data" id="formularioCurso">
                            @csrf
                            @method('PUT')

                            <!-- ==========================================
                            FECHA REGISTRO STPS
                            ========================================== -->
                            <div class="form-section-card shadow-sm">
                                <div class="stps-title"><i class="fas fa-calendar-alt"></i> Fecha de Registro STPS</div>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="FechaRegistroSTPS" class="form-label">
                                            Fecha de Registro STPS
                                            <span class="estado-indicador" id="estado-FechaRegistroSTPS"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            <input type="date" name="FechaRegistroSTPS" id="FechaRegistroSTPS" 
                                                class="form-control" 
                                                value="{{ old('FechaRegistroSTPS', isset($fechaRegistro) ? $fechaRegistro->fecha_registro : '') }}"
                                                oninput="validarFecha(this)"
                                                onchange="validarFecha(this)">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ==========================================
                            FORMATO DC5
                            ========================================== -->
                            <div class="form-section-card shadow-sm">
                                <div class="stps-title"><i class="fas fa-file-contract"></i> Formato DC5</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="FormatoDC5" class="form-label">
                                            Nombre / Referencia
                                            <span class="estado-indicador" id="estado-FormatoDC5"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                            <input type="text" name="FormatoDC5" id="FormatoDC5" 
                                                class="form-control" 
                                                value="{{ old('FormatoDC5', isset($certificaciones['dc5']) ? $certificaciones['dc5']->nombre : '') }}" 
                                                placeholder="Ej: DC5_2024.pdf"
                                                oninput="validarCampo(this)"
                                                onchange="validarCampo(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="FormatoDC5TieneFirma" class="form-label">
                                            ¿Tiene firma?
                                            <span class="estado-indicador" id="estado-FormatoDC5TieneFirma"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                            <select name="FormatoDC5TieneFirma" id="FormatoDC5TieneFirma" 
                                                class="form-select" 
                                                onchange="validarSelect(this)">
                                                <option value="" disabled {{ old('FormatoDC5TieneFirma', isset($certificaciones['dc5']) ? $certificaciones['dc5']->tiene_firma : '') === '' ? 'selected' : '' }}>Seleccione</option>
                                                <option value="Si" {{ old('FormatoDC5TieneFirma', isset($certificaciones['dc5']) ? $certificaciones['dc5']->tiene_firma : '') == 'Si' ? 'selected' : '' }}>Sí</option>
                                                <option value="No" {{ old('FormatoDC5TieneFirma', isset($certificaciones['dc5']) ? $certificaciones['dc5']->tiene_firma : '') == 'No' ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Archivo Local</label>
                                        <input type="file" name="archivoDC5" class="form-control">
                                        @if(isset($recursos['dc5_archivo']) && $recursos['dc5_archivo'])
                                            <div class="documento-preview">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="doc-nombre">{{ basename($recursos['dc5_archivo']->url) }}</span>
                                                <div class="doc-acciones">
                                                    <a href="{{ $recursos['dc5_archivo']->archivo_publico_url }}" target="_blank" class="btn-ver-doc">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- ==========================================
                            CERTIFICADO DE COMPROBACIÓN
                            ========================================== -->
                            <div class="form-section-card shadow-sm">
                                <div class="stps-title"><i class="fas fa-stamp"></i> Certificado de Comprobación</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="CertificadoComprobacion" class="form-label">
                                            Estado
                                            <span class="estado-indicador" id="estado-CertificadoComprobacion"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                            <select name="CertificadoComprobacion" id="CertificadoComprobacion" 
                                                class="form-select" 
                                                onchange="validarSelect(this)">
                                                <option value="" disabled {{ old('CertificadoComprobacion', isset($certificaciones['certificado_comprobacion']) ? $certificaciones['certificado_comprobacion']->nombre : '') === '' ? 'selected' : '' }}>Seleccione</option>
                                                <option value="Ya obtenida" {{ old('CertificadoComprobacion', isset($certificaciones['certificado_comprobacion']) ? $certificaciones['certificado_comprobacion']->nombre : '') == 'Ya obtenida' ? 'selected' : '' }}>Ya obtenida</option>
                                                <option value="En proceso" {{ old('CertificadoComprobacion', isset($certificaciones['certificado_comprobacion']) ? $certificaciones['certificado_comprobacion']->nombre : '') == 'En proceso' ? 'selected' : '' }}>En proceso</option>
                                                <option value="No obtenida" {{ old('CertificadoComprobacion', isset($certificaciones['certificado_comprobacion']) ? $certificaciones['certificado_comprobacion']->nombre : '') == 'No obtenida' ? 'selected' : '' }}>No obtenida</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="DriveCertificadoComprobacion" class="form-label">
                                            URL Drive
                                            <span class="estado-indicador" id="estado-DriveCertificadoComprobacion"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-google-drive"></i></span>
                                            <input type="text" name="DriveCertificadoComprobacion" id="DriveCertificadoComprobacion" 
                                                class="form-control" 
                                                value="{{ old('DriveCertificadoComprobacion', isset($certificaciones['certificado_comprobacion']) ? $certificaciones['certificado_comprobacion']->drive_url : '') }}" 
                                                placeholder="https://drive.google.com/..."
                                                oninput="validarDrive(this)"
                                                onchange="validarDrive(this)">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Archivo Local</label>
                                        <input type="file" name="archivoCertificado" class="form-control">
                                        @if(isset($recursos['certificado_archivo']) && $recursos['certificado_archivo'])
                                            <div class="documento-preview">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="doc-nombre">{{ basename($recursos['certificado_archivo']->url) }}</span>
                                                <div class="doc-acciones">
                                                    <a href="{{ $recursos['certificado_archivo']->archivo_publico_url }}" target="_blank" class="btn-ver-doc">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- ==========================================
                            CARTA PODER
                            ========================================== -->
                            <div class="form-section-card shadow-sm">
                                <div class="stps-title"><i class="fas fa-file-signature"></i> Carta Poder</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="CartaPoderTieneFirma" class="form-label">
                                            ¿Tiene firma?
                                            <span class="estado-indicador" id="estado-CartaPoderTieneFirma"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-signature"></i></span>
                                            <select name="CartaPoderTieneFirma" id="CartaPoderTieneFirma" 
                                                class="form-select" 
                                                onchange="validarSelect(this)">
                                                <option value="" disabled {{ old('CartaPoderTieneFirma', isset($certificaciones['carta_poder']) ? $certificaciones['carta_poder']->tiene_firma : '') === '' ? 'selected' : '' }}>Seleccione</option>
                                                <option value="Si" {{ old('CartaPoderTieneFirma', isset($certificaciones['carta_poder']) ? $certificaciones['carta_poder']->tiene_firma : '') == 'Si' ? 'selected' : '' }}>Sí</option>
                                                <option value="No" {{ old('CartaPoderTieneFirma', isset($certificaciones['carta_poder']) ? $certificaciones['carta_poder']->tiene_firma : '') == 'No' ? 'selected' : '' }}>No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="DriveCartaPoder" class="form-label">
                                            URL Drive
                                            <span class="estado-indicador" id="estado-DriveCartaPoder"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-google-drive"></i></span>
                                            <input type="text" name="DriveCartaPoder" id="DriveCartaPoder" 
                                                class="form-control" 
                                                value="{{ old('DriveCartaPoder', isset($certificaciones['carta_poder']) ? $certificaciones['carta_poder']->drive_url : '') }}" 
                                                placeholder="https://drive.google.com/..."
                                                oninput="validarDrive(this)"
                                                onchange="validarDrive(this)">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Archivo Local</label>
                                        <input type="file" name="archivoCartaPoder" class="form-control">
                                        @if(isset($recursos['carta_poder_archivo']) && $recursos['carta_poder_archivo'])
                                            <div class="documento-preview">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="doc-nombre">{{ basename($recursos['carta_poder_archivo']->url) }}</span>
                                                <div class="doc-acciones">
                                                    <a href="{{ $recursos['carta_poder_archivo']->archivo_publico_url }}" target="_blank" class="btn-ver-doc">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- ==========================================
                            UDEMY
                            ========================================== -->
                            <div class="form-section-card shadow-sm">
                                <div class="stps-title"><i class="fas fa-graduation-cap"></i> UDEMY</div>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="UDEMY" class="form-label">
                                            Estado UDEMY
                                            <span class="estado-indicador" id="estado-UDEMY"></span>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                            <select name="UDEMY" id="UDEMY" 
                                                class="form-select" 
                                                onchange="validarSelect(this)">
                                                <option value="" disabled {{ old('UDEMY', isset($recursos['udemy']) ? $recursos['udemy']->url : '') === '' ? 'selected' : '' }}>Seleccione</option>
                                                <option value="Prellenado" {{ old('UDEMY', isset($recursos['udemy']) ? $recursos['udemy']->url : '') == 'Prellenado' ? 'selected' : '' }}>Prellenado</option>
                                                <option value="No se ha prellenado" {{ old('UDEMY', isset($recursos['udemy']) ? $recursos['udemy']->url : '') == 'No se ha prellenado' ? 'selected' : '' }}>No se ha prellenado</option>
                                            </select>
                                        </div>
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
    document.querySelectorAll('#FechaRegistroSTPS').forEach(campo => {
        validarFecha(campo);
    });
    document.querySelectorAll('#FormatoDC5, #CertificadoComprobacion, #CartaPoderTieneFirma, #UDEMY').forEach(campo => {
        validarSelect(campo);
    });
    document.querySelectorAll('#DriveCertificadoComprobacion, #DriveCartaPoder').forEach(campo => {
        validarDrive(campo);
    });
    document.querySelectorAll('#FormatoDC5').forEach(campo => {
        validarCampo(campo);
    });
    actualizarContadores();
    actualizarEstadoGeneral();
});

function validarFecha(campo) {
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

function validarCampo(campo) {
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

function validarSelect(select) {
    const valor = select.value;
    const indicador = document.getElementById('estado-' + select.id);
    
    select.classList.remove('select-completo', 'select-vacio');
    if (indicador) {
        indicador.classList.remove('estado-verde', 'estado-rojo');
    }
    
    if (valor === '' || valor === null) {
        select.classList.add('select-vacio');
        if (indicador) indicador.classList.add('estado-rojo');
    } else {
        select.classList.add('select-completo');
        if (indicador) indicador.classList.add('estado-verde');
    }
    
    actualizarContadores();
    actualizarEstadoGeneral();
}

function actualizarContadores() {
    const campos = document.querySelectorAll('#formularioCurso .form-control, #formularioCurso .form-select');
    let completos = 0, incompletos = 0, vacios = 0;
    
    campos.forEach(campo => {
        if (campo.classList.contains('drive-completo') || campo.classList.contains('select-completo')) {
            completos++;
        } else if (campo.classList.contains('drive-vacio') || campo.classList.contains('select-vacio')) {
            vacios++;
        }
    });
    
    document.getElementById('totalCompletos').textContent = completos;
    document.getElementById('totalIncompletos').textContent = incompletos;
    document.getElementById('totalVacios').textContent = vacios;
}

function actualizarEstadoGeneral() {
    const campos = document.querySelectorAll('#formularioCurso .form-control, #formularioCurso .form-select');
    let completos = 0, incompletos = 0, vacios = 0;
    const total = campos.length;
    
    campos.forEach(campo => {
        if (campo.classList.contains('drive-completo') || campo.classList.contains('select-completo')) {
            completos++;
        } else if (campo.classList.contains('drive-vacio') || campo.classList.contains('select-vacio')) {
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
        textoEstado.textContent = `🟡 ${incompletos} campo(s) incompleto(s) - Revisar`;
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