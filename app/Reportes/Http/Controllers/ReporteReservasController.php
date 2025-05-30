<?php

namespace App\Reportes\Http\Controllers;

use App\Http\Controllers\Controller;
use App\reportes\Services\ReporteReservasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReporteReservasController extends Controller
{
    protected ReporteReservasService $reporteReservasService;

    public function __construct(ReporteReservasService $reporteReservasService)
    {
        $this->reporteReservasService = $reporteReservasService;
    }

    public function index(Request $request): JsonResponse
    {
        // Obtener reporte de reservas según filtros (fechas, estado, etc.)
        $reportes = $this->reporteReservasService->generarReporte($request->all());
        return response()->json($reportes);
    }

    public function porCategoria(Request $request): JsonResponse
    {
        $categorias = $this->reporteReservasService->reportePorCategoria($request->all());
        return response()->json($categorias);
    }

    public function porServicio(Request $request): JsonResponse
    {
        $servicios = $this->reporteReservasService->reportePorServicio($request->all());
        return response()->json($servicios);
    }
}
