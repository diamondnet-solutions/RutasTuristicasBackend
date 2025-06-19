<?php

namespace App\Reportes\Emprendedores\Services;

use App\Reportes\Emprendedores\Data\EmprendedoresReportData;

class ChartGeneratorService
{
    /**
     * Generar datos para gráfico de categorías
     */
    public function generarDatosCategorias(EmprendedoresReportData $data): array
    {
        $categorias = $data->estadisticas['por_categoria'];

        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => array_keys($categorias),
                'datasets' => [[
                    'data' => array_values($categorias),
                    'backgroundColor' => $this->getColores(count($categorias)),
                    'borderWidth' => 2
                ]]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'position' => 'right'
                    ]
                ]
            ]
        ];
    }

    /**
     * Generar datos para gráfico de municipalidades
     */
    public function generarDatosMunicipalidades(EmprendedoresReportData $data): array
    {
        $municipalidades = $data->estadisticas['por_municipalidad'];

        return [
            'type' => 'bar',
            'data' => [
                'labels' => array_keys($municipalidades),
                'datasets' => [[
                    'label' => 'Emprendedores',
                    'data' => array_values($municipalidades),
                    'backgroundColor' => $this->getColores(count($municipalidades), 0.8),
                    'borderColor' => $this->getColores(count($municipalidades)),
                    'borderWidth' => 2
                ]]
            ],
            'options' => [
                'responsive' => true,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true
                    ]
                ]
            ]
        ];
    }

    /**
     * Generar datos para gráfico de certificaciones
     */
    public function generarDatosCertificaciones(EmprendedoresReportData $data): array
    {
        $conCertificaciones = $data->estadisticas['con_certificaciones'];
        $sinCertificaciones = $data->estadisticas['total'] - $conCertificaciones;

        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['Con Certificaciones', 'Sin Certificaciones'],
                'datasets' => [[
                    'data' => [$conCertificaciones, $sinCertificaciones],
                    'backgroundColor' => ['#10B981', '#EF4444'],
                    'borderColor' => ['#059669', '#DC2626'],
                    'borderWidth' => 2
                ]]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom'
                    ]
                ]
            ]
        ];
    }

    /**
     * Generar datos para gráfico de facilidades
     */
    public function generarDatosFacilidades(EmprendedoresReportData $data): array
    {
        $conFacilidades = $data->estadisticas['con_facilidades_discapacidad'];
        $sinFacilidades = $data->estadisticas['total'] - $conFacilidades;

        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['Con Facilidades', 'Sin Facilidades'],
                'datasets' => [[
                    'data' => [$conFacilidades, $sinFacilidades],
                    'backgroundColor' => ['#8B5CF6', '#6B7280'],
                    'borderColor' => ['#7C3AED', '#4B5563'],
                    'borderWidth' => 2
                ]]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom'
                    ]
                ]
            ]
        ];
    }

    /**
     * Generar todos los gráficos
     */
    public function generarTodosLosGraficos(EmprendedoresReportData $data): array
    {
        return [
            'categorias' => $this->generarDatosCategorias($data),
            'municipalidades' => $this->generarDatosMunicipalidades($data),
            'certificaciones' => $this->generarDatosCertificaciones($data),
            'facilidades' => $this->generarDatosFacilidades($data)
        ];
    }

    /**
     * Obtener colores para gráficos
     */
    private function getColores(int $cantidad, float $alpha = 1.0): array
    {
        $colores = [
            '#3B82F6', // Blue
            '#10B981', // Green
            '#F59E0B', // Yellow
            '#EF4444', // Red
            '#8B5CF6', // Purple
            '#06B6D4', // Cyan
            '#F97316', // Orange
            '#84CC16', // Lime
            '#EC4899', // Pink
            '#6B7280'  // Gray
        ];

        $resultado = [];
        for ($i = 0; $i < $cantidad; $i++) {
            $color = $colores[$i % count($colores)];

            if ($alpha < 1.0) {
                // Convertir hex a rgba
                $r = hexdec(substr($color, 1, 2));
                $g = hexdec(substr($color, 3, 2));
                $b = hexdec(substr($color, 5, 2));
                $resultado[] = "rgba($r, $g, $b, $alpha)";
            } else {
                $resultado[] = $color;
            }
        }

        return $resultado;
    }

    /**
     * Generar imagen de gráfico usando Chart.js (para PDF)
     */
    public function generarImagenGrafico(array $configGrafico, int $width = 800, int $height = 400): string
    {
        // Esta función requeriría una implementación con Node.js o similar
        // Por ahora, retornamos un placeholder
        return $this->generarPlaceholderGrafico($configGrafico['type'], $width, $height);
    }

    /**
     * Generar placeholder de gráfico (temporal)
     */
    private function generarPlaceholderGrafico(string $tipo, int $width, int $height): string
    {
        // Crear una imagen simple con GD
        $imagen = imagecreate($width, $height);
        $fondo = imagecolorallocate($imagen, 248, 250, 252); // bg-gray-50
        $texto = imagecolorallocate($imagen, 75, 85, 99); // text-gray-600

        imagefill($imagen, 0, 0, $fondo);

        $textoGrafico = "Gráfico de " . ucfirst($tipo);
        $x = ($width - strlen($textoGrafico) * 10) / 2;
        $y = $height / 2;

        imagestring($imagen, 5, $x, $y, $textoGrafico, $texto);

        ob_start();
        imagepng($imagen);
        $contenido = ob_get_contents();
        ob_end_clean();

        imagedestroy($imagen);

        return 'data:image/png;base64,' . base64_encode($contenido);
    }
}
