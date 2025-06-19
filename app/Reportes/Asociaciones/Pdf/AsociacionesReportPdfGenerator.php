<?php

namespace App\Reportes\Asociaciones\Pdf;

use App\Reportes\Asociaciones\Data\AsociacionesReportData;
use App\Reportes\Asociaciones\Services\AsociacionesChartGeneratorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDF;

readonly class AsociacionesReportPdfGenerator
{
    public function __construct(
        private AsociacionesChartGeneratorService $chartService
    ) {}

    public function generar(AsociacionesReportData $data, array $opciones = []): DomPDF
    {
        $orientacion = $opciones['orientacion'] ?? 'portrait';
        $incluirEstadisticas = $opciones['incluir_estadisticas'] ?? true;
        $incluirGraficos = $opciones['incluir_graficos'] ?? true;
        $tipoReporte = $opciones['tipo_reporte'] ?? 'completo';

        // Generar gráficos si están habilitados
        $graficos = [];
        if ($incluirGraficos) {
            $graficos = $this->chartService->generarTodosLosGraficos($data);

            // Generar imágenes de gráficos
            foreach ($graficos as $tipo => $config) {
                $graficos[$tipo]['imagen'] = $this->chartService->generarImagenGrafico($config);
            }
        }

        $pdf = Pdf::loadView('reportes.asociaciones_pdf', [
            'data' => $data,
            'incluir_estadisticas' => $incluirEstadisticas,
            'incluir_graficos' => $incluirGraficos,
            'graficos' => $graficos,
            'tipo_reporte' => $tipoReporte,
            'opciones' => $opciones
        ]);

        $pdf->setPaper('A4', $orientacion);

        // Configuraciones adicionales del PDF
        $pdf->setOptions([
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'chroot' => public_path(),
            'dpi' => 150, // Mayor calidad para gráficos
        ]);

        return $pdf;
    }
}
