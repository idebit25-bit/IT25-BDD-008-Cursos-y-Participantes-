@extends('layouts.app') 

@section('content')
<!-- jQuery UI -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #dee2e6;
        border-radius: 6px !important;
        padding: 2px 5px;
        min-height: 45px;
        background-color: #fff;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #dee2e6;
        box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.05);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #333 !important;
        border: none !important;
        color: white !important;
        border-radius: 4px !important;
        padding: 4px 10px 4px 25px !important;
        margin-top: 6px !important;
        position: relative !important;
        font-size: 0.85rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff !important;
        margin-right: 0 !important;
        position: absolute !important;
        left: 5px !important;
        border: none !important;
        background: transparent !important;
        font-weight: bold;
        top: 50%;
        transform: translateY(-50%);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        background: transparent !important;
        color: #ff4d4d !important;
    }
    .select2-container--default .select2-search--inline .select2-search__field {
        margin-top: 7px !important;
    }
    .create-container {
        padding: 50px 0;
    }
    .create-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        overflow: hidden;
        background: white;
    }
    .create-header {
        background: linear-gradient(135deg, #000000 0%, #333333 100%);
        padding: 40px 30px;
        color: white;
        text-align: center;
        position: relative;
    }
    .create-header h2 {
        font-weight: 300;
        letter-spacing: 2px;
        text-transform: uppercase;
    }
    .back-arrow {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: white;
        font-size: 1.2rem;
        transition: all 0.3s ease;
        background: rgba(255,255,255,0.1);
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        text-decoration: none;
    }
    .back-arrow:hover {
        background: rgba(255,255,255,0.2);
        color: white;
        transform: translateY(-50%) translateX(-5px);
    }
    .icon-header {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.9;
    }
    .form-section-title {
        font-size: 0.85rem;
        font-weight: bold;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        display: flex;
        align-items: center;
    }
    .form-section-title i {
        margin-right: 10px;
        color: #333;
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
    }
    .input-group-text {
        background-color: #ffffff;
        color: #6c757d;
        border-right: none;
    }
    .form-control, .form-select {
        border-left: none;
        padding: 10px 15px;
    }
    .form-control:focus, .form-select:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }
    .input-group:focus-within {
        box-shadow: 0 0 0 0.25rem rgba(0, 0, 0, 0.05);
        border-radius: 0.375rem;
    }
    .botones-container {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
    }
    .btn-custom {
        border-radius: 6px !important;
        padding: 12px 35px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    .btn-success {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
    }
    .btn-success:hover {
        background-color: #218838 !important;
        border-color: #1e7e34 !important;
    }
</style>

<div class="container create-container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card create-card">
                <div class="create-header">
                    <a href="{{ route('participantes.index') }}" class="back-arrow" title="Regresar">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="icon-header">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h2 class="mb-0">Registrar Participante</h2>
                    <p class="mb-0 mt-2 opacity-75">Ingrese los datos para la inscripción al curso</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm mb-4">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('participantes.store') }}" method="POST">
                        @csrf 
                        
                        <!-- Sección 1: Datos Personales -->
                        <div class="row">
                            <div class="col-12">
                                <h5 class="form-section-title"><i class="fas fa-id-card"></i> Información Personal</h5>
                                <div class="form-section-card shadow-sm">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label for="N" class="form-label">N° Participante</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                                <input type="text" class="form-control" id="N" name="N" value="{{ old('N') }}" required placeholder="Ej: 001">
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <label for="NombredelPostulante" class="form-label">Nombre Completo</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                <input type="text" class="form-control" id="NombredelPostulante" name="NombredelPostulante" value="{{ old('NombredelPostulante') }}" required placeholder="Nombre y apellidos">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Correo" class="form-label">Correo Electrónico</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                <input type="email" class="form-control" id="Correo" name="Correo" value="{{ old('Correo') }}" required placeholder="correo@ejemplo.com">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="Telefono" class="form-label">Teléfono</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                <input type="text" class="form-control" id="Telefono" name="Telefono" value="{{ old('Telefono') }}" 
                                                       maxlength="10" pattern="\d{10}" title="El teléfono debe tener 10 dígitos numéricos" required placeholder="10 dígitos">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="Edad" class="form-label">Edad</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-birthday-cake"></i></span>
                                                <input type="number" class="form-control" id="Edad" name="Edad" value="{{ old('Edad') }}" min="18" max="90" required>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="Direccion" class="form-label">Dirección</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                                <input type="text" class="form-control" id="Direccion" name="Direccion" value="{{ old('Direccion') }}" required placeholder="Calle, número, colonia, ciudad">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="Curp" class="form-label">CURP</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-id-badge"></i></span>
                                                <input type="text" class="form-control" id="Curp" name="Curp" value="{{ old('Curp') }}" 
                                                       maxlength="18" minlength="18" style="text-transform: uppercase;" required placeholder="18 caracteres">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Escolaridad" class="form-label">Escolaridad</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-graduation-cap"></i></span>
                                                <select class="form-select" id="Escolaridad" name="Escolaridad" required>
                                                    <option value="" disabled selected>Seleccione escolaridad</option>
                                                    <option value="Primaria">Primaria</option>
                                                    <option value="Secundaria">Secundaria</option>
                                                    <option value="Preparatoria">Preparatoria</option>
                                                    <option value="Licenciatura">Licenciatura</option>
                                                    <option value="Maestría">Maestría</option>
                                                    <option value="Doctorado">Doctorado</option>
                                                    <option value="Otro">Otro</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección 2: Información Laboral -->
                            <div class="col-12">
                                <h5 class="form-section-title"><i class="fas fa-briefcase"></i> Información Laboral</h5>
                                <div class="form-section-card shadow-sm">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="Empresa" class="form-label">Empresa</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                                <input type="text" class="form-control" id="Empresa" name="Empresa" value="{{ old('Empresa') }}" required placeholder="Nombre de la empresa">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Puesto" class="form-label">Puesto</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-user-tie"></i></span>
                                                <input type="text" class="form-control" id="Puesto" name="Puesto" value="{{ old('Puesto') }}" required placeholder="Escriba el puesto...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="Ocupacion" class="form-label">Ocupación específica</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-tools"></i></span>
                                                <input type="text" class="form-control" id="Ocupacion" name="Ocupacion" value="{{ old('Ocupacion') }}" required placeholder="Escribe la ocupación...">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="RazónSocial" class="form-label">Razón Social</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-file-invoice"></i></span>
                                                <input type="text" class="form-control" id="RazónSocial" name="RazónSocial" value="{{ old('RazónSocial') }}" required placeholder="Razón social">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="RFCEmpresa" class="form-label">RFC de la Empresa</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-fingerprint"></i></span>
                                                <input type="text" class="form-control" id="RFCEmpresa" name="RFCEmpresa" value="{{ old('RFCEmpresa') }}" required placeholder="RFC">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección 3: Inscripción y Pago -->
                            <div class="col-12">
                                <h5 class="form-section-title"><i class="fas fa-money-check-alt"></i> Inscripción y Pago</h5>
                                <div class="form-section-card shadow-sm">
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label for="cursos" class="form-label">Cursos a inscribir</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-book"></i></span>
                                                <select class="form-select" id="cursos" name="cursos[]" multiple required>
                                                    @foreach ($cursos as $curso)
                                                        <option value="{{ $curso->id }}">{{ $curso->NombredelCurso }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Puedes seleccionar varios cursos de la lista.</small>
                                        </div>
                                        <div id="detallesCursos" class="col-md-12 mb-3" style="display: none;">
                                            <div class="alert alert-dark border-0 shadow-sm" style="background-color: #f1f3f5;">
                                                <h6 class="fw-bold mb-2 small text-uppercase">Cursos Seleccionados:</h6>
                                                <div id="listaDetalles" class="list-group list-group-flush bg-transparent"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="FechadelCurso" class="form-label">Fecha del Curso</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                                <input type="date" class="form-control" id="FechadelCurso" name="FechadelCurso" value="{{ old('FechadelCurso') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="Pago" class="form-label">Monto de Pago</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                                                <input type="number" step="0.01" min="0" class="form-control" id="Pago" name="Pago" value="{{ old('Pago') }}" required placeholder="0.00">
                                            </div>
                                            <small id="totalSugerido" class="text-primary fw-bold mt-1 d-block" style="display: none !important;">
                                                Total sugerido: $<span id="sumaCostos">0.00</span>
                                            </small>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="EstadoDePago" class="form-label">Estado de Pago</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                                <select class="form-select" id="EstadoDePago" name="EstadoDePago" required>
                                                    <option value="" disabled selected>Estado...</option>
                                                    <option value="Pagado">Pagado</option>
                                                    <option value="Pendiente">Pendiente</option>
                                                    <option value="Anticipo">Anticipo</option>
                                                    <option value="Cancelado">Cancelado</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="botones-container pb-3">
                            <button type="submit" class="btn btn-success btn-custom shadow-sm">
                                <i class="fas fa-save me-2"></i> Guardar Participante
                            </button>
                            <a href="{{ route('participantes.index') }}" class="btn btn-danger btn-custom shadow-sm">
                                <i class="fas fa-times me-2"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar Select2
    $('#cursos').select2({
        placeholder: "Seleccione uno o más cursos",
        allowClear: true,
        width: 'resolve'
    });

    const puestos = [
        "01 Cultivo, crianza y aprovechamiento", "01.1 Agricultura y silvicultura", "01.2 Ganadería",
        "01.3 Pesca y acuacultura", "02 Extracción y suministro", "02.1 Exploración",
        "02.2 Extracción", "02.3 Refinación y beneficio", "02.4 Provisión de energia",
        "02.5 Provisión de agua", "03 Construcción", "03.1 Planeación y dirección de obras",
        "03.2 Edificación y urbanización", "03.3 Acabado", "03.4 Instalación y mantenimiento",
        "04 Tecnologia", "04.1 Mecánica", "04.2 Electricidad", "04.3 Electrónica",
        "04.4 Informática", "04.5 Telecomunicaciones", "04.6 Procesos industriales",
        "05 Procesamiento y fabricación", "05.1 Minerales no metálicos", "05.2 Metales",
        "05.3 Alimento y bebidas", "05.4 Textiles y prendas de vestir", "05.5 Materia orgánica",
        "05.6 Productos químicos", "05.7 Productos metálicos y de hule y plástico",
        "05.8 Productos eléctricos y electrónicos", "05.9 Productos impresos",
        "06 Transporte", "06.1 Ferroviario", "06.2 Autotransporte", "06.3 Aéreo",
        "06.4 Maritimo y fluvial", "06.5 Servicios de apoyo", "07 Provisión de bienes y servicios",
        "07.1 Comercio", "07.2 Alimentación y hospedaje", "07.3 Turismo",
        "07.4 Deporte y esparcimiento.", "07.5 Servicios personales",
        "07.6 Reparación de artículos de uso doméstico y personal", "7.7 Limpieza",
        "07.8 Servicio postal y mensajeria", "08 Gestión y soporte administrativo",
        "08.1 Bolsa, banca y seguros", "08.2 Administración", "08.3 Servicios legales",
        "09 Salud y protección social", "09.1 Servicios médicos",
        "09.2 Inspección sanitaria y del medio ambiente", "09.3 Seguridad social",
        "09.4 Protección de bienes y/o personas", "10 Comunicación", "10.1 Publicación",
        "10.2 Radio, cine, televisión y teatro", "10.3 Interpretación artística",
        "10.4 Traducción e interpretación lingüística", "10.5 Publicidad, propaganda y relaciones públicas",
        "11 Desarrollo y extensión del conocimiento", "11.1 Investigación", "11.2 Enseñanza",
        "11.3 Difusión cultural"
    ];

    $("#Puesto").autocomplete({
        source: puestos,
        minLength: 0,
        select: function (event, ui) {
            $("#Puesto").val(ui.item.value);
            return false;
        }
    }).focus(function() {
        $(this).autocomplete("search", "");
    });

    $('#cursos').on('change', function() {
        var selectedIds = $(this).val();
        var listaDetalles = $('#listaDetalles');
        var detallesContainer = $('#detallesCursos');

        if (selectedIds && selectedIds.length > 0) {
            $.ajax({
                url: "{{ route('participantes.cursos-detalles') }}",
                type: "GET",
                data: { ids: selectedIds },
                success: function(data) {
                    listaDetalles.empty();
                    var totalSuma = 0;
                    data.forEach(function(curso) {
                        var fechaI = curso.fecha_inicio ? curso.fecha_inicio : 'No definida';
                        var costo = curso.costo ? parseFloat(curso.costo) : 0;
                        totalSuma += costo;
                        
                        listaDetalles.append(
                            '<div class="list-group-item bg-transparent border-0 px-0 py-1">' +
                                '<i class="fas fa-check-circle text-dark me-2"></i>' +
                                '<strong>' + curso.nombre + '</strong> ' +
                                '<span class="text-muted small ms-2">(Inicio: ' + fechaI + ' - Costo: $' + costo.toFixed(2) + ')</span>' +
                            '</div>'
                        );

                        if (selectedIds.indexOf(curso.id.toString()) === 0) {
                             $('#FechadelCurso').val(curso.fecha_inicio);
                        }
                    });
                    
                    $('#sumaCostos').text(totalSuma.toFixed(2));
                    $('#Pago').val(totalSuma.toFixed(2));
                    $('#totalSugerido').attr('style', 'display: block !important;');
                    detallesContainer.fadeIn();
                }
            });
        } else {
            detallesContainer.fadeOut();
            listaDetalles.empty();
        }
    });
});
</script>
@endsection
