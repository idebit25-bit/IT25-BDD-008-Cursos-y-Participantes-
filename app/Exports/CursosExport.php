<?php

namespace App\Exports;

use App\Models\Curso;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class CursosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $cursos;

    // Lista de campos que son tipo sí/no (excluyendo los campos específicos)
    protected $booleanFields = [
        'Virtual', 'Presencial', 'Mixto', 'Presentación',
        'Formato_DC5_Tienefirma', 'Cartapoder_tienefirma', 'UDEMY'
    ];

    public function __construct($cursos)
    {
        $this->cursos = $cursos;
    }

    public function headings(): array
    {
        return [
            'Nomenclatura',
            'parent_id',
            'Nombre del Curso',
            'Descripción del Curso',
            'Costo del Curso',
            'Instructor Responsable',
            'Fecha de Inicio',
            'Fecha de Término',
            'Virtual',
            'Presencial',
            'Mixto',
            'Sin Fecha',
            'Drive Sin Fecha',
            'Facebook',
            'Drive Facebook',
            'LinkedIn',
            'Drive LinkedIn',
            'Instagram',
            'Drive Instagram',
            'Temario',
            'Drive Temario',
            'Itinerario',
            'Drive Itinerario',
            'Planeación',
            'Drive Planeación',
            'Digital',
            'Drive Digital',
            'Impreso Presentable',
            'Presentación',
            'Evaluación Diagnóstica',
            'Evaluación de Satisfacción',
            'Evaluación Final',
            'DC3',
            'Fecha de Registro STPS',
            'Formato DC5',
            'Formato DC5 Tiene Firma',
            'Certificado de Comprobación',
            'Drive de Certificado',
            'Carta Poder Tiene Firma',
            'Drive Carta Poder',
            'UDEMY',
            'Enlace UDEMY',
        ];
    }

    public function map($curso): array
    {
        return [
            $curso->Nomenclatura,
            $curso->parent_id,
            $curso->NombredelCurso,
            $curso->DescripciondeCurso,
            '$' . number_format((float)($curso->CostodelCurso ?: 0), 2),
            $curso->InstructorResponsable,
            $curso->FechadeInicio ? \Carbon\Carbon::parse($curso->FechadeInicio)->format('d/m/Y') : '',
            $curso->FechadeTermino ? \Carbon\Carbon::parse($curso->FechadeTermino)->format('d/m/Y') : '',
            $this->formatValue('Virtual', $curso->Virtual),
            $this->formatValue('Presencial', $curso->Presencial),
            $this->formatValue('Mixto', $curso->Mixto),
            $curso->SinFecha, // Mantener el valor original para porcentajes
            $curso->DriveSinFecha,
            $curso->Facebook, // Mantener el valor original para porcentajes
            $curso->DriveFacebook,
            $curso->Linkedin, // Mantener el valor original para porcentajes
            $curso->DriveLinkedin,
            $curso->Instagram, // Mantener el valor original para porcentajes
            $curso->DriveInstagram,
            $curso->Temario, // Mantener el valor original para porcentajes
            $curso->DriveTemario,
            $curso->Itinerario, // Mantener el valor original para porcentajes
            $curso->DriveItinerario,
            $curso->Planeación, // Mantener el valor original para porcentajes
            $curso->DrivePlaneación,
            $curso->Digital, // Mantener el valor original para porcentajes
            $curso->DriveDigital,
            $curso->Impreso_Presentable, // Conservar valor original
            $curso->Presentación,
            $curso->Evaluación_diagnostica, // Conservar valor original
            $curso->EvaluaciondeSatisfacción, // Conservar valor original
            $curso->EvaluacionFinal, // Conservar valor original
            $curso->DC3, // Conservar valor original
            $curso->FechadeRegistro_STPS ? \Carbon\Carbon::parse($curso->FechadeRegistro_STPS)->format('d/m/Y') : '',
            $curso->Formato_DC5, // Conservar valor original
            $this->formatValue('Formato_DC5_Tienefirma', $curso->Formato_DC5_Tienefirma),
            $curso->Certificadodecomprobacion, // Conservar valor original
            $curso->DrivedeCertificadodecomprobacion,
            $this->formatValue('Cartapoder_tienefirma', $curso->Cartapoder_tienefirma),
            $curso->DriveCartapoder,
            $this->formatValue('UDEMY', $curso->UDEMY),
            $curso->EnlaceUDEMY,
        ];
    }

    public function styles(Worksheet $sheet)
{
    // Obtener la última columna y fila
    $lastColumn = $sheet->getHighestColumn();
    $lastRow = $sheet->getHighestRow();

    // Estilo del encabezado
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'], // Texto blanco
            'size' => 11
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '000000'] // Fondo negro
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ]
    ];

    // Aplicar estilo al encabezado
    $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray($headerStyle);
    $sheet->getRowDimension(1)->setRowHeight(30);

    // Estilo para las filas de datos
    $dataStyle = [
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'CCCCCC']
            ]
        ]
    ];
    $sheet->getStyle("A2:{$lastColumn}{$lastRow}")->applyFromArray($dataStyle);

    // Colorear filas alternas
    for ($row = 2; $row <= $lastRow; $row++) {
        if ($row % 2 == 0) {
            $sheet->getStyle("A{$row}:{$lastColumn}{$row}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('F5F5F5'));
        }
    }

    // Ajustar el ancho de las columnas
$lastColumnIndex = Coordinate::columnIndexFromString($lastColumn);

for ($col = 1; $col <= $lastColumnIndex; $col++) {
    $columnLetter = Coordinate::stringFromColumnIndex($col);
    $sheet->getColumnDimension($columnLetter)->setWidth(15);
}


    // Ancho adicional para columnas específicas
    $sheet->getColumnDimension('B')->setWidth(30); // Nombre del Curso
    $sheet->getColumnDimension('C')->setWidth(40); // Descripción

    // Altura de las filas de datos
    foreach (range(2, $lastRow) as $row) {
        $sheet->getRowDimension($row)->setRowHeight(22);
    }

    // Alinear al centro las columnas booleanas (✓/✗)
    $booleanColumns = ['H', 'I', 'J', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AG', 'AH', 'AI', 'AK', 'AM'];
    foreach ($booleanColumns as $column) {
        $sheet->getStyle("{$column}2:{$column}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    return $sheet;
}

    public function collection()
    {
        return $this->cursos;
    }

    private function formatValue($field, $value): string
    {
        // Si el campo está en la lista de campos booleanos, usar ✓/✗
        if (in_array($field, $this->booleanFields)) {
            return $this->formatBoolean($value);
        }

        // Para otros campos, devolver el valor original
        return $value;
    }

    private function formatBoolean($value): string
    {
        if (is_string($value)) {
            $value = strtolower(trim($value));
            if ($value === 'true' || $value === '1' || $value === 'si' || $value === 'sí' || $value === 'yes') {
                return '✓';
            }
            return '✗';
        }
        if (is_numeric($value)) {
            return $value == 1 ? '✓' : '✗';
        }
        if (is_bool($value)) {
            return $value === true ? '✓' : '✗';
        }
        return '✗';
    }
}
