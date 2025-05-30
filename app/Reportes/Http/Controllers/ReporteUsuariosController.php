<?php

namespace App\Reportes\Http\Controllers;

use App\Http\Controllers\Controller;
use App\reportes\Services\ReporteUsuariosService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReporteUsuariosController extends Controller
{
    protected ReporteUsuariosService $reporteUsuariosService;

    public function __construct(ReporteUsuariosService $reporteUsuariosService)
    {
        $this->reporteUsuariosService = $reporteUsuariosService;
    }

    public function index(Request $request) : JsonResponse
    {
        // Obtener reporte general de usuarios (emprendedores, asociaciones, etc.)
        $reporte = $this->reporteUsuariosService->generarReporte($request->all());
        return response()->json($reporte);
    }

    public function porMunicipalidad(Request $request) : JsonResponse
    {
        $reporte = $this->reporteUsuariosService->reportePorMunicipalidad($request->all());
        return response()->json($reporte);
    }

    public function resumenGeneral(Request $request) : JsonResponse
    {
        $resumen = $this->reporteUsuariosService->resumenGeneral($request->all());
        return response()->json($resumen);
    }
}
