<?php

namespace App\Reportes\Emprendedores\Data;

class EmprendedoresReportData
{
    public function __construct(
        public array  $emprendedores,
        public array  $filtros,
        public array  $estadisticas,
        public string $fechaGeneracion,
        public string $usuarioGenerador
    ) {}

    public static function create(
        array $emprendedores,
        array $filtros = [],
        ?string $usuarioGenerador = null
    ): self {
        return new self(
            emprendedores: $emprendedores,
            filtros: $filtros,
            estadisticas: self::calcularEstadisticas($emprendedores),
            fechaGeneracion: now()->format('d/m/Y H:i:s'),
            usuarioGenerador: $usuarioGenerador ?? 'Sistema'
        );
    }

    private static function calcularEstadisticas(array $emprendedores): array
    {
        $total = count($emprendedores);

        if ($total === 0) {
            return [
                'total' => 0,
                'por_categoria' => [],
                'por_municipalidad' => [],
                'promedio_reservas' => 0,
                'capacidad_total' => 0,
                'con_certificaciones' => 0,
                'con_facilidades_discapacidad' => 0
            ];
        }

        $categorias = [];
        $municipalidades = [];
        $totalReservas = 0;
        $capacidadTotal = 0;
        $conCertificaciones = 0;
        $conFacilidades = 0;

        foreach ($emprendedores as $emprendedor) {
            // Contar por categoría
            $categoria = $emprendedor['categoria'] ?? 'Sin categoría';
            $categorias[$categoria] = ($categorias[$categoria] ?? 0) + 1;

            // Contar por municipalidad
            $municipalidad = $emprendedor['municipalidad'] ?? 'Sin municipalidad';
            $municipalidades[$municipalidad] = ($municipalidades[$municipalidad] ?? 0) + 1;

            // Sumar estadísticas
            $totalReservas += $emprendedor['reservas_mes'] ?? 0;
            $capacidadTotal += $emprendedor['capacidad_aforo'] ?? 0;

            if (!empty($emprendedor['certificaciones'])) {
                $conCertificaciones++;
            }

            if ($emprendedor['facilidades_discapacidad'] ?? false) {
                $conFacilidades++;
            }
        }

        return [
            'total' => $total,
            'por_categoria' => $categorias,
            'por_municipalidad' => $municipalidades,
            'promedio_reservas' => round($totalReservas / $total, 2),
            'capacidad_total' => $capacidadTotal,
            'con_certificaciones' => $conCertificaciones,
            'con_facilidades_discapacidad' => $conFacilidades,
            'porcentaje_certificaciones' => round(($conCertificaciones / $total) * 100, 2),
            'porcentaje_facilidades' => round(($conFacilidades / $total) * 100, 2)
        ];
    }

    public function getTotalEmprendedores(): int
    {
        return $this->estadisticas['total'];
    }

    public function getCategoriaMasPopular(): ?string
    {
        if (empty($this->estadisticas['por_categoria'])) {
            return null;
        }

        return array_key_first(
            array_slice(
                arsort($this->estadisticas['por_categoria']) ? $this->estadisticas['por_categoria'] : [],
                0,
                1,
                true
            )
        );
    }

    public function getMunicipalidadConMasEmprendedores(): ?string
    {
        if (empty($this->estadisticas['por_municipalidad'])) {
            return null;
        }

        $municipalidades = $this->estadisticas['por_municipalidad'];
        arsort($municipalidades);

        return array_key_first($municipalidades);
    }
}
