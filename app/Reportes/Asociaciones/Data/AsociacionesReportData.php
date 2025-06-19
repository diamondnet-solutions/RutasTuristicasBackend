<?php

namespace App\Reportes\Asociaciones\Data;

readonly class AsociacionesReportData
{
    public function __construct(
        public array  $asociaciones,
        public array  $filtros,
        public array  $estadisticas,
        public string $fechaGeneracion,
        public string $usuarioGenerador
    ) {}

    public static function create(
        array $asociaciones,
        array $filtros = [],
        ?string $usuarioGenerador = null
    ): self {
        return new self(
            asociaciones: $asociaciones,
            filtros: $filtros,
            estadisticas: self::calcularEstadisticas($asociaciones),
            fechaGeneracion: now()->format('d/m/Y H:i:s'),
            usuarioGenerador: $usuarioGenerador ?? 'Sistema'
        );
    }

    private static function calcularEstadisticas(array $asociaciones): array
    {
        $total = count($asociaciones);

        if ($total === 0) {
            return [
                'total' => 0,
                'por_municipalidad' => [],
                'por_estado' => [],
                'total_emprendedores' => 0,
                'total_servicios' => 0,
                'total_reservas_mes' => 0,
                'promedio_emprendedores' => 0,
                'promedio_servicios' => 0,
                'promedio_reservas' => 0,
                'promedio_calificacion' => 0,
                'asociaciones_activas' => 0,
                'asociaciones_inactivas' => 0,
                'mejor_calificada' => null,
                'mas_emprendedores' => null,
                'mas_servicios' => null,
                'distribucion_por_año' => []
            ];
        }

        $municipalidades = [];
        $estados = ['activas' => 0, 'inactivas' => 0];
        $totalEmprendedores = 0;
        $totalServicios = 0;
        $totalReservas = 0;
        $totalCalificacion = 0;
        $distribucionAño = [];

        $mejorCalificada = null;
        $masEmprendedores = null;
        $masServicios = null;
        $mejorCalificacion = 0;
        $maxEmprendedores = 0;
        $maxServicios = 0;

        foreach ($asociaciones as $asociacion) {
            // Contar por municipalidad
            $municipalidad = $asociacion['municipalidad'] ?? 'Sin municipalidad';
            $municipalidades[$municipalidad] = ($municipalidades[$municipalidad] ?? 0) + 1;

            // Contar por estado
            if ($asociacion['estado'] ?? true) {
                $estados['activas']++;
            } else {
                $estados['inactivas']++;
            }

            // Sumar totales
            $emprendedores = $asociacion['emprendedores_count'] ?? 0;
            $servicios = $asociacion['servicios_count'] ?? 0;
            $reservas = $asociacion['reservas_mes'] ?? 0;
            $calificacion = $asociacion['calificacion'] ?? 0;

            $totalEmprendedores += $emprendedores;
            $totalServicios += $servicios;
            $totalReservas += $reservas;
            $totalCalificacion += $calificacion;

            // Encontrar mejores asociaciones
            if ($calificacion > $mejorCalificacion) {
                $mejorCalificacion = $calificacion;
                $mejorCalificada = $asociacion['nombre'];
            }

            if ($emprendedores > $maxEmprendedores) {
                $maxEmprendedores = $emprendedores;
                $masEmprendedores = $asociacion['nombre'];
            }

            if ($servicios > $maxServicios) {
                $maxServicios = $servicios;
                $masServicios = $asociacion['nombre'];
            }

            // Distribución por año de creación
            if (!empty($asociacion['fecha_creacion'])) {
                $año = date('Y', strtotime($asociacion['fecha_creacion']));
                $distribucionAño[$año] = ($distribucionAño[$año] ?? 0) + 1;
            }
        }

        return [
            'total' => $total,
            'por_municipalidad' => $municipalidades,
            'por_estado' => $estados,
            'total_emprendedores' => $totalEmprendedores,
            'total_servicios' => $totalServicios,
            'total_reservas_mes' => $totalReservas,
            'promedio_emprendedores' => round($totalEmprendedores / $total, 2),
            'promedio_servicios' => round($totalServicios / $total, 2),
            'promedio_reservas' => round($totalReservas / $total, 2),
            'promedio_calificacion' => round($totalCalificacion / $total, 2),
            'asociaciones_activas' => $estados['activas'],
            'asociaciones_inactivas' => $estados['inactivas'],
            'porcentaje_activas' => round(($estados['activas'] / $total) * 100, 2),
            'mejor_calificada' => $mejorCalificada,
            'mas_emprendedores' => $masEmprendedores,
            'mas_servicios' => $masServicios,
            'distribucion_por_año' => $distribucionAño
        ];
    }

    public function getTotalAsociaciones(): int
    {
        return $this->estadisticas['total'];
    }

    public function getMunicipalidadConMasAsociaciones(): ?string
    {
        if (empty($this->estadisticas['por_municipalidad'])) {
            return null;
        }

        $municipalidades = $this->estadisticas['por_municipalidad'];
        arsort($municipalidades);

        return array_key_first($municipalidades);
    }

    public function getPromedioEmprendedoresPorAsociacion(): float
    {
        return $this->estadisticas['promedio_emprendedores'];
    }

    public function getCalificacionPromedio(): float
    {
        return $this->estadisticas['promedio_calificacion'];
    }

    public function getPorcentajeAsociacionesActivas(): float
    {
        return $this->estadisticas['porcentaje_activas'];
    }
}
