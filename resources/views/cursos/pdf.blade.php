<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Técnica del Curso - {{ $curso->Nomenclatura }}</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 11pt;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo {
            width: 180px;
        }
        .contact-info {
            text-align: right;
            font-size: 9pt;
            color: #555;
        }
        .contact-info b {
            color: #0d6efd;
        }
        
        h1 {
            color: #212529;
            font-size: 20pt;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .nomenclatura {
            color: #0d6efd;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 25px;
        }
        .section-title {
            background-color: #f8f9fa;
            color: #0d6efd;
            font-size: 12pt;
            font-weight: bold;
            padding: 8px 12px;
            border-left: 5px solid #0d6efd;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .info-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .info-grid td {
            padding: 8px 5px;
            vertical-align: top;
        }
        .label {
            font-weight: bold;
            color: #555;
            width: 30%;
            font-size: 10pt;
        }
        .value {
            color: #212529;
            width: 70%;
        }

        .table-custom {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table-custom th {
            background-color: #212529;
            color: white;
            padding: 10px;
            font-size: 9pt;
            text-transform: uppercase;
            border: 1px solid #212529;
        }
        .table-custom td {
            padding: 10px;
            border: 1px solid #dee2e6;
            text-align: center;
            font-size: 10pt;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: bold;
        }
        .bg-primary { background-color: #0d6efd; color: white; }
        .bg-light { background-color: #e9ecef; color: #333; }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <img src="{{ public_path('images/logo4.jpg') }}" class="logo">
            </td>
            <td class="contact-info">
                <b>Instituto I-DEB</b><br>
                instituto.ideb@idebmexico.com<br>
                3337029639 / 3320705533
            </td>
        </tr>
    </table>

    <div class="header-main">
        <h1>{{ $curso->NombredelCurso }}</h1>
        <div class="nomenclatura">FICHA TÉCNICA: {{ $curso->Nomenclatura }}</div>
    </div>

    <div class="section">
        <div class="section-title">Información General</div>
        <table class="info-grid">
            <tr>
                <td class="label">Descripción:</td>
                <td class="value" style="text-align: justify;">{{ $curso->DescripciondeCurso }}</td>
            </tr>
            <tr>
                <td class="label">Instructor Responsable:</td>
                <td class="value">{{ $curso->InstructorResponsable }}</td>
            </tr>
            <tr>
                <td class="label">Costo del Curso:</td>
                <td class="value"><b>${{ number_format((float)$curso->CostodelCurso, 2) }} MXN</b></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Cronograma y Modalidad</div>
        <table class="info-grid">
            <tr>
                <td class="label">Periodo:</td>
                <td class="value">
                    Del <b>{{ \Carbon\Carbon::parse($curso->FechadeInicio)->format('d/m/Y') }}</b> 
                    al <b>{{ \Carbon\Carbon::parse($curso->FechadeTermino)->format('d/m/Y') }}</b>
                </td>
            </tr>
            <tr>
                <td class="label">Duración Total:</td>
                <td class="value">{{ $curso->Duracioncurso }}</td>
            </tr>
        </table>

        <table class="table-custom">
            <thead>
                <tr>
                    <th>Virtual</th>
                    <th>Presencial</th>
                    <th>Mixto</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge {{ $curso->Virtual == 'Si' ? 'bg-primary' : 'bg-light' }}">{{ $curso->Virtual == 'Si' ? 'SÍ' : 'NO' }}</span></td>
                    <td><span class="badge {{ $curso->Presencial == 'Si' ? 'bg-primary' : 'bg-light' }}">{{ $curso->Presencial == 'Si' ? 'SÍ' : 'NO' }}</span></td>
                    <td><span class="badge {{ $curso->Mixto == 'Si' ? 'bg-primary' : 'bg-light' }}">{{ $curso->Mixto == 'Si' ? 'SÍ' : 'NO' }}</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Recursos y Evaluación</div>
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Material Digital</th>
                    <th>Material Impreso</th>
                    <th>UDEMY</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $curso->Digital ?: 'No disponible' }}</td>
                    <td>{{ $curso->Impreso_Presentable ?: 'No disponible' }}</td>
                    <td>{{ $curso->UDEMY == 'Si' ? 'Disponible' : 'No' }}</td>
                </tr>
            </tbody>
        </table>

        <p style="margin-top: 15px; font-weight: bold; font-size: 10pt; color: #555;">Estatus de Evaluación:</p>
        <table class="table-custom">
            <thead>
                <tr>
                    <th>Diagnóstica</th>
                    <th>Satisfacción</th>
                    <th>Final</th>
                    <th>DC3</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $curso->Evaluación_diagnostica }}</td>
                    <td>{{ $curso->EvaluaciondeSatisfacción }}</td>
                    <td>{{ $curso->EvaluacionFinal }}</td>
                    <td>{{ $curso->DC3 }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        Ficha generada automáticamente por el Sistema de Gestión de Cursos - Instituto I-DEB | Fecha de impresión: {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
