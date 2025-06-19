<?php

namespace App\Reportes\Asociaciones\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Reportes\Asociaciones\Http\Requests\AsociacionesReportRequest;
use App\Reportes\Asociaciones\Services\AsociacionesReportService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AsociacionesReportController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly AsociacionesReportService $reportService
    ) {}

    /**
     * Generar reporte PDF de asociaciones
     */
    public function generarPDF(AsociacionesReportRequest $request): Response|JsonResponse
    {
        try {
            $filtros = $request->getFiltros();
            $opciones = $request->getOpciones();
            $usuario = $request->user()?->name ?? 'Usuario Anónimo';

            $pdf = $this->reportService->generarReportePDF($filtros, $opciones, $usuario);

            return $pdf;

        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Error al generar el reporte: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Obtener datos para filtros del reporte
     */
    public function obtenerDatosFiltros(): JsonResponse
    {
        try {
            $datos = $this->reportService->obtenerDatosParaFiltros();

            return $this->successResponse(
                data: $datos,
                message: 'Datos para filtros obtenidos correctamente'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Error al obtener datos para filtros: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Previsualizar reporte antes de generar
     */
    public function previsualizarReporte(AsociacionesReportRequest $request): JsonResponse
    {
        try {
            $filtros = $request->getFiltros();
            $preview = $this->reportService->previsualizarReporte($filtros);

            return $this->successResponse(
                data: $preview,
                message: 'Previsualización del reporte generada correctamente'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Error al previsualizar el reporte: ' . $e->getMessage(),
                code: 500
            );
        }
    }

    /**
     * Generar reporte comparativo entre asociaciones
     */
    public function generarReporteComparativo(AsociacionesReportRequest $request): JsonResponse
    {
        try {
            $asociacionesIds = $request->input('asociaciones_ids', []);

            if (count($asociacionesIds) < 2) {
                return $this->errorResponse(
                    message: 'Se requieren al menos 2 asociaciones para generar un reporte comparativo',
                    code: 400
                );
            }

            $reporte = $this->reportService->generarReporteComparativo($asociacionesIds);

            return $this->successResponse(
                data: $reporte,
                message: 'Reporte comparativo generado correctamente'
            );

        } catch (\Exception $e) {
            return $this->errorResponse(
                message: 'Error al generar el reporte comparativo: ' . $e->getMessage(),
                code: 500
            );
        }
    }
}
