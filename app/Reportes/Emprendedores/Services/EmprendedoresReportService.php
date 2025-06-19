<?php

namespace App\Reportes\Emprendedores\Services;

use App\Reportes\Emprendedores\Data\EmprendedoresReportData;
use App\Reportes\Emprendedores\Repository\EmprendedoresReportRepository;
use App\Reportes\Emprendedores\Pdf\EmprendedoresReportPdfGenerator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

readonly class EmprendedoresReportService
{
    public function __construct(
        private EmprendedoresReportRepository   $repository,
        private EmprendedoresReportPdfGenerator $pdfGenerator
    ) {}

    public function generarReportePDF(array $filtros = [], array $opciones = [], ?string $usuarioGenerador = null): Response
    {
        try {
            Log::info('Iniciando generación de reporte de emprendedores', [
                'filtros' => $filtros,
                'opciones' => $opciones,
                'usuario' => $usuarioGenerador
            ]);

            // Obtener datos
            $emprendedores = $this->repository->obtenerEmprendedoresParaReporte($filtros);

            if ($emprendedores->isEmpty()) {
                throw new \Exception('No se encontraron emprendedores con los filtros especificados.');
            }

            // Crear estructura de datos
            $reportData = EmprendedoresReportData::create(
                emprendedores: $emprendedores->toArray(),
                filtros: $filtros,
                usuarioGenerador: $usuarioGenerador
            );

            // Generar PDF
            $pdf = $this->pdfGenerator->generar($reportData, $opciones);

            Log::info('Reporte de emprendedores generado exitosamente', [
                'total_emprendedores' => $reportData->getTotalEmprendedores(),
                'usuario' => $usuarioGenerador
            ]);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="reporte_emprendedores_' . date('Y-m-d_H-i-s') . '.pdf"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            Log::error('Error al generar reporte de emprendedores', [
                'error' => $e->getMessage(),
                'filtros' => $filtros,
                'usuario' => $usuarioGenerador
            ]);

            throw $e;
        }
    }

    public function obtenerDatosParaFiltros(): array
    {
        try {
            return [
                'categorias' => $this->repository->obtenerCategorias(),
                'municipalidades' => $this->repository->obtenerMunicipalidades(),
                'comunidades' => $this->repository->obtenerComunidades(),
            ];
        } catch (\Exception $e) {
            Log::error('Error al obtener datos para filtros', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function previsualizarReporte(array $filtros = []): array
    {
        try {
            $emprendedores = $this->repository->obtenerEmprendedoresParaReporte($filtros);

            $reportData = EmprendedoresReportData::create(
                emprendedores: $emprendedores->toArray(),
                filtros: $filtros
            );

            return [
                'total_registros' => $reportData->getTotalEmprendedores(),
                'estadisticas' => $reportData->estadisticas,
                'muestra' => $emprendedores->take(5)->toArray(), // Muestra de 5 registros
                'filtros_aplicados' => $filtros
            ];

        } catch (\Exception $e) {
            Log::error('Error al previsualizar reporte', [
                'error' => $e->getMessage(),
                'filtros' => $filtros
            ]);
            throw $e;
        }
    }
}
