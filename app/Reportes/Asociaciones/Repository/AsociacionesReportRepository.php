<?php

namespace App\Reportes\Asociaciones\Repository;

use App\pagegeneral\models\Municipalidad;
use App\Reportes\Asociaciones\Models\Asociacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AsociacionesReportRepository
{
    public function obtenerAsociacionesParaReporte(array $filtros = []): Collection
    {
        $query = Asociacion::query()
            ->with('municipalidad')
            ->withCount('emprendedores');

        $this->aplicarFiltros($query, $filtros);

        return $query->get()->map(fn($asociacion) => $this->transformarAsociacion($asociacion));
    }

    private function aplicarFiltros(Builder $query, array $filtros): void
    {
        if (!empty($filtros['municipalidad'])) {
            $query->whereHas('municipalidad', fn($q) => $q->where('nombre', $filtros['municipalidad']));
        }

        if (isset($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        if (!empty($filtros['fecha_desde'])) {
            $query->whereDate('created_at', '>=', $filtros['fecha_desde']);
        }

        if (!empty($filtros['fecha_hasta'])) {
            $query->whereDate('created_at', '<=', $filtros['fecha_hasta']);
        }

        if (!empty($filtros['emprendedores_min'])) {
            $query->has('emprendedores', '>=', $filtros['emprendedores_min']);
        }

        if (!empty($filtros['emprendedores_max'])) {
            $query->has('emprendedores', '<=', $filtros['emprendedores_max']);
        }

        if (!empty($filtros['nombre'])) {
            $query->where('nombre', 'like', '%' . $filtros['nombre'] . '%');
        }

        // Ordenamiento
        $ordenPor = $filtros['orden_por'] ?? 'nombre';
        $direccion = $filtros['direccion'] ?? 'asc';

        switch ($ordenPor) {
            case 'emprendedores':
                $query->orderBy('emprendedores_count', $direccion);
                break;
            case 'calificacion':
                $query->orderBy('emprendedores_avg_calificacion', $direccion);
                break;
            case 'fecha_creacion':
                $query->orderBy('created_at', $direccion);
                break;
            default:
                $query->orderBy('nombre', $direccion);
        }
    }

    private function transformarAsociacion($asociacion): array
    {
        return [
            'id' => $asociacion->id,
            'nombre' => $asociacion->nombre,
            'descripcion' => $asociacion->descripcion,
            'ubicacion' => $asociacion->ubicacion,
            'telefono' => $asociacion->telefono,
            'email' => $asociacion->email,
            'estado' => $asociacion->estado,
            'municipalidad' => $asociacion->municipalidad?->nombre ?? 'Sin municipalidad',
            'municipalidad_id' => $asociacion->municipalidad_id,
            'emprendedores_count' => $asociacion->emprendedores_count ?? 0,
            'servicios_count' => $this->contarServicios($asociacion->id),
            'calificacion' => 2.0,
            'reservas_mes' => $this->calcularReservasMensuales($asociacion->id),
            'ingresos_mes' => $this->calcularIngresosMensuales($asociacion->id),
            'servicios_por_categoria' => $this->obtenerServiciosPorCategoria($asociacion->id),
            'fecha_creacion' => $asociacion->created_at?->format('Y-m-d'),
            'años_operacion' => $asociacion->created_at ? $asociacion->created_at->diffInYears(now()) : 0,
            'created_at' => $asociacion->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $asociacion->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    private function contarServicios(int $asociacionId): int
    {
        return DB::table('servicios')
            ->join('emprendedores', 'servicios.emprendedor_id', '=', 'emprendedores.id')
            ->where('emprendedores.asociacion_id', $asociacionId)
            ->count();
    }

    private function calcularReservasMensuales(int $asociacionId): int
    {
        return DB::table('reserva_servicios')
            ->join('emprendedores', 'reserva_servicios.emprendedor_id', '=', 'emprendedores.id')
            ->where('emprendedores.asociacion_id', $asociacionId)
            ->whereMonth('reserva_servicios.created_at', now()->month)
            ->whereYear('reserva_servicios.created_at', now()->year)
            ->count();
    }

    private function calcularIngresosMensuales(int $asociacionId): float
    {
        return (float) DB::table('reserva_servicios')
            ->join('emprendedores', 'reserva_servicios.emprendedor_id', '=', 'emprendedores.id')
            ->where('emprendedores.asociacion_id', $asociacionId)
            ->whereMonth('reserva_servicios.created_at', now()->month)
            ->whereYear('reserva_servicios.created_at', now()->year)
            ->sum(DB::raw('precio * cantidad'));
    }

    private function obtenerServiciosPorCategoria(int $asociacionId): array
    {
        return DB::table('servicios')
            ->join('emprendedores', 'servicios.emprendedor_id', '=', 'emprendedores.id')
            ->select('emprendedores.categoria', DB::raw('COUNT(servicios.id) as total'))
            ->where('emprendedores.asociacion_id', $asociacionId)
            ->groupBy('emprendedores.categoria')
            ->pluck('total', 'emprendedores.categoria')
            ->toArray();
    }

    public function obtenerMunicipalidades(): Collection
    {
        return Municipalidad::has('asociaciones')
            ->pluck('nombre')
            ->filter()
            ->sort()
            ->values();
    }

    public function obtenerRangosEmprendedores(): array
    {
        $stats = Asociacion::withCount('emprendedores')
            ->get()
            ->map(fn($a) => $a->emprendedores_count)
            ->filter();

        return [
            'min' => $stats->min() ?? 0,
            'max' => $stats->max() ?? 0,
        ];
    }

    public function obtenerRangosCalificacion(): array
    {
        return [
            'min' => 1.0,
            'max' => 5.0
        ];
    }
}
