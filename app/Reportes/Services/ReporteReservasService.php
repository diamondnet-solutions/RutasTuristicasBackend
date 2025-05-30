<?php

namespace App\Reportes\Services;

use App\reportes\Repository\ReporteReservasRepository;

class ReporteReservasService
{
    protected ReporteReservasRepository $repository;

    public function __construct(ReporteReservasRepository $repository)
    {
        $this->repository = $repository;
    }

    public function generarReporte(array $filtros)
    {
        return $this->repository->obtenerReservas($filtros);
    }

    public function reportePorCategoria(array $filtros)
    {
        return $this->repository->obtenerPorCategoria();
    }

    public function reportePorServicio(array $filtros)
    {
        return $this->repository->obtenerPorServicio();
    }
}
