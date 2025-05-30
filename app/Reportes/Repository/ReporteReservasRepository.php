<?php

namespace App\Reportes\Repository;

use Illuminate\Support\Facades\DB;

class ReporteReservasRepository
{
    public function obtenerReservas($filtros)
    {
        return DB::table('reservas')
            ->when(isset($filtros['fecha_inicio']), function ($query) use ($filtros) {
                $query->whereDate('fecha', '>=', $filtros['fecha_inicio']);
            })
            ->when(isset($filtros['fecha_fin']), function ($query) use ($filtros) {
                $query->whereDate('fecha', '<=', $filtros['fecha_fin']);
            })
            ->get();
    }

    public function obtenerPorCategoria()
    {
        return DB::table('reservas')
            ->join('servicios', 'reservas.servicio_id', '=', 'servicios.id')
            ->join('categorias', 'servicios.categoria_id', '=', 'categorias.id')
            ->select('categorias.nombre as categoria', DB::raw('count(*) as total'))
            ->groupBy('categorias.nombre')
            ->get();
    }

    public function obtenerPorServicio()
    {
        return DB::table('reservas')
            ->join('servicios', 'reservas.servicio_id', '=', 'servicios.id')
            ->select('servicios.nombre as servicio', DB::raw('count(*) as total'))
            ->groupBy('servicios.nombre')
            ->get();
    }
}
