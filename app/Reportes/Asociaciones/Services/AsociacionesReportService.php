<?php

namespace App\Reportes\Asociaciones\Services;

use App\Reportes\Asociaciones\Data\AsociacionesReportData;
use App\Reportes\Asociaciones\Repository\AsociacionesReportRepository;
use App\Reportes\Asociaciones\Pdf\AsociacionesReportPdfGenerator;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

readonly class AsociacionesReportService
{
    public function __construct(
        private AsociacionesReportRepository   $repository,
        private AsociacionesReportPdfGenerator $pdfGenerator
    ) {}

    /**
     * @throws Exception
     */
    public function generarReportePDF(array $filtros = [], array $opciones = [], ?string $usuarioGenerador = null): Response
    {
        try {
            Log::info('Iniciando generación de reporte de asociaciones', [
                'filtros' => $filtros,
                'opciones' => $opciones,
                'usuario' => $usuarioGenerador
            ]);

            // Obtener datos
            $asociaciones = $this->repository->obtenerAsociacionesParaReporte($filtros);

            if ($asociaciones->isEmpty()) {
                throw new Exception('No se encontraron asociaciones con los filtros especificados.');
            }

            // Crear estructura de datos
            $reportData = AsociacionesReportData::create(
                asociaciones: $asociaciones->toArray(),
                filtros: $filtros,
                usuarioGenerador: $usuarioGenerador
            );

            // Generar PDF
            $pdf = $this->pdfGenerator->generar($reportData, $opciones);

            Log::info('Reporte de asociaciones generado exitosamente', [
                'total_asociaciones' => $reportData->getTotalAsociaciones(),
                'usuario' => $usuarioGenerador
            ]);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="reporte_asociaciones_' . date('Y-m-d_H-i-s') . '.pdf"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (Exception $e) {
            Log::error('Error al generar reporte de asociaciones', [
                'error' => $e->getMessage(),
                'filtros' => $filtros,
                'usuario' => $usuarioGenerador
            ]);

            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function obtenerDatosParaFiltros(): array
    {
        try {
            return [
                'municipalidades' => $this->repository->obtenerMunicipalidades(),
                'rangos_emprendedores' => $this->repository->obtenerRangosEmprendedores(),
                'rangos_calificacion' => $this->repository->obtenerRangosCalificacion(),
            ];
        } catch (Exception $e) {
            Log::error('Error al obtener datos para filtros de asociaciones', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function previsualizarReporte(array $filtros = []): array
    {
        try {
            $asociaciones = $this->repository->obtenerAsociacionesParaReporte($filtros);

            $reportData = AsociacionesReportData::create(
                asociaciones: $asociaciones->toArray(),
                filtros: $filtros
            );

            return [
                'total_registros' => $reportData->getTotalAsociaciones(),
                'estadisticas' => $reportData->estadisticas,
                'muestra' => $asociaciones->take(5)->toArray(), // Muestra de 5 registros
                'filtros_aplicados' => $filtros,
                'insights' => [
                    'municipalidad_lider' => $reportData->getMunicipalidadConMasAsociaciones(),
                    'promedio_emprendedores' => $reportData->getPromedioEmprendedoresPorAsociacion(),
                    'calificacion_promedio' => $reportData->getCalificacionPromedio(),
                    'porcentaje_activas' => $reportData->getPorcentajeAsociacionesActivas(),
                ]
            ];

        } catch (Exception $e) {
            Log::error('Error al previsualizar reporte de asociaciones', [
                'error' => $e->getMessage(),
                'filtros' => $filtros
            ]);
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    public function generarReporteComparativo(array $asociacionesIds): array
    {
        try {
            // Implementar lógica para reporte comparativo
            $asociaciones = $this->repository->obtenerAsociacionesParaReporte([
                'ids' => $asociacionesIds
            ]);

            return [
                'asociaciones' => $asociaciones->toArray(),
                'comparacion' => $this->generarComparacion($asociaciones->toArray())
            ];

        } catch (Exception $e) {
            Log::error('Error al generar reporte comparativo', [
                'error' => $e->getMessage(),
                'asociaciones_ids' => $asociacionesIds
            ]);
            throw $e;
        }
    }

    private function generarComparacion(array $asociaciones): array
    {
        if (count($asociaciones) < 2) {
            return [];
        }

        $comparacion = [];

        // Comparar métricas clave
        $metricas = ['emprendedores_count', 'servicios_count', 'calificacion', 'reservas_mes'];

        foreach ($metricas as $metrica) {
            $valores = array_column($asociaciones, $metrica);
            $comparacion[$metrica] = [
                'max' => max($valores),
                'min' => min($valores),
                'promedio' => array_sum($valores) / count($valores),
                'mejor' => $asociaciones[array_search(max($valores), $valores)]['nombre'],
                'menor' => $asociaciones[array_search(min($valores), $valores)]['nombre']
            ];
        }

        return $comparacion;
    }
}
