<?php

namespace App\Reportes\Services;

use App\reportes\Repository\ReporteUsuariosRepository;

class ReporteUsuariosService
{
    protected $repository;

    public function __construct(ReporteUsuariosRepository $repository)
    {
        $this->repository = $repository;
    }

    public function generarReporte(array $filtros)
    {
        return $this->repository->obtenerUsuarios($filtros);
    }

    public function reportePorMunicipalidad(array $filtros)
    {
        return $this->repository->usuariosPorMunicipalidad();
    }

    public function resumenGeneral(array $filtros)
    {
        return $this->repository->obtenerResumenGeneral();
    }
}
