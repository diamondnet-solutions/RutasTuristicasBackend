<?php

namespace App\Reportes\Emprendedores\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Reportes\Emprendedores\Http\Requests\EmprendedoresReportRequest;
use App\Reportes\Emprendedores\Services\EmprendedoresReportService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class EmprendedoresReportController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private readonly EmprendedoresReportService $reportService
    ) {}

    /**
     * Generar reporte PDF de emprendedores
     */
    public function generarPDF(EmprendedoresReportRequest $request): Response|JsonResponse
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
    public function previsualizarReporte(EmprendedoresReportRequest $request): JsonResponse
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
}
