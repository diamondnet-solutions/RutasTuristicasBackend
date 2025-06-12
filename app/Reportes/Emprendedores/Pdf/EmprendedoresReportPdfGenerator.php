<?php

namespace App\Reportes\Emprendedores\Pdf;

use App\Reportes\Emprendedores\Data\EmprendedoresReportData;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDF;

class EmprendedoresReportPdfGenerator
{
    public function generar(EmprendedoresReportData $data, array $opciones = []): DomPDF
    {
        $orientacion = $opciones['orientacion'] ?? 'portrait';
        $incluirEstadisticas = $opciones['incluir_estadisticas'] ?? true;
        $incluirGraficos = $opciones['incluir_graficos'] ?? false;

        $pdf = Pdf::loadView('reportes.emprendedores_pdf', [
            'data' => $data,
            'incluir_estadisticas' => $incluirEstadisticas,
            'incluir_graficos' => $incluirGraficos,
            'opciones' => $opciones
        ]);

        $pdf->setPaper('A4', $orientacion);

        // Configuraciones adicionales del PDF
        $pdf->setOptions([
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'chroot' => public_path(),
        ]);

        return $pdf;
    }

    private function generarGraficos(EmprendedoresReportData $data): array
    {
        // Aquí podrías integrar con una librería de gráficos como Chart.js
        // Por ahora retornamos datos estructurados para los gráficos
        return [
            'categorias' => [
                'labels' => array_keys($data->estadisticas['por_categoria']),
                'data' => array_values($data->estadisticas['por_categoria'])
            ],
            'municipalidades' => [
                'labels' => array_keys($data->estadisticas['por_municipalidad']),
                'data' => array_values($data->estadisticas['por_municipalidad'])
            ]
        ];
    }
}
