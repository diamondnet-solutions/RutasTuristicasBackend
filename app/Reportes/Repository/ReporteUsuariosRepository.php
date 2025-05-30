<?php

namespace App\Reportes\Repository;

use Illuminate\Support\Facades\DB;

class ReporteUsuariosRepository
{
    public function obtenerUsuarios($filtros)
    {
        return DB::table('users')
            ->when(isset($filtros['tipo']), function ($query) use ($filtros) {
                $query->where('tipo', $filtros['tipo']);
            })
            ->get();
    }

    public function usuariosPorMunicipalidad()
    {
        return DB::table('users')
            ->join('municipalidades', 'users.municipalidad_id', '=', 'municipalidades.id')
            ->select('municipalidades.nombre as municipalidad', DB::raw('count(*) as total'))
            ->groupBy('municipalidades.nombre')
            ->get();
    }

    public function obtenerResumenGeneral()
    {
        return [
            'usuarios' => DB::table('users')->count(),
            'asociaciones' => DB::table('asociaciones')->count(),
            'emprendedores' => DB::table('users')->where('tipo', 'emprendedor')->count(),
            'lugares_turisticos' => DB::table('lugares_turisticos')->count(),
            'servicios' => DB::table('servicios')->count(),
        ];
    }
}
