<?php

namespace App\Reportes\Asociaciones\Services;

use App\Reportes\Asociaciones\Data\AsociacionesReportData;

class AsociacionesChartGeneratorService
{
    /**
     * Generar datos para gráfico de distribución por municipalidad
     */
    public function generarDatosMunicipalidades(AsociacionesReportData $data): array
    {
        $municipalidades = $data->estadisticas['por_municipalidad'];

        return [
            'type' => 'bar',
            'data' => [
                'labels' => array_keys($municipalidades),
                'datasets' => [[
                    'label' => 'Asociaciones',
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
                        'beginAtZero' => true,
                        'title' => [
                            'display' => true,
                            'text' => 'Número de Asociaciones'
                        ]
                    ],
                    'x' => [
                        'title' => [
                            'display' => true,
                            'text' => 'Municipalidades'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Generar datos para gráfico de estado de asociaciones
     */
    public function generarDatosEstado(AsociacionesReportData $data): array
    {
        $estados = $data->estadisticas['por_estado'];

        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => ['Activas', 'Inactivas'],
                'datasets' => [[
                    'data' => [$estados['activas'], $estados['inactivas']],
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
     * Generar datos para gráfico de evolución temporal
     */
    public function generarDatosEvolucionTemporal(AsociacionesReportData $data): array
    {
        $distribucionAño = $data->estadisticas['distribucion_por_año'];
        ksort($distribucionAño); // Ordenar por año

        return [
            'type' => 'line',
            'data' => [
                'labels' => array_keys($distribucionAño),
                'datasets' => [[
                    'label' => 'Asociaciones Creadas',
                    'data' => array_values($distribucionAño),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => '#3B82F6',
                    'borderWidth' => 3,
                    'fill' => true,
                    'tension' => 0.4
                ]]
            ],
            'options' => [
                'responsive' => true,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'title' => [
                            'display' => true,
                            'text' => 'Número de Asociaciones'
                        ]
                    ],
                    'x' => [
                        'title' => [
                            'display' => true,
                            'text' => 'Año de Creación'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Generar datos para gráfico de distribución de emprendedores
     */
    public function generarDatosDistribucionEmprendedores(AsociacionesReportData $data): array
    {
        // Agrupar asociaciones por rangos de emprendedores
        $rangos = [
            '1-5' => 0,
            '6-10' => 0,
            '11-20' => 0,
            '21-50' => 0,
            '50+' => 0
        ];

        foreach ($data->asociaciones as $asociacion) {
            $count = $asociacion['emprendedores_count'] ?? 0;

            if ($count <= 5) {
                $rangos['1-5']++;
            } elseif ($count <= 10) {
                $rangos['6-10']++;
            } elseif ($count <= 20) {
                $rangos['11-20']++;
            } elseif ($count <= 50) {
                $rangos['21-50']++;
            } else {
                $rangos['50+']++;
            }
        }

        return [
            'type' => 'doughnut',
            'data' => [
                'labels' => array_keys($rangos),
                'datasets' => [[
                    'data' => array_values($rangos),
                    'backgroundColor' => $this->getColores(count($rangos), 0.8),
                    'borderColor' => $this->getColores(count($rangos)),
                    'borderWidth' => 2
                ]]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'position' => 'right'
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Distribución por Número de Emprendedores'
                    ]
                ]
            ]
        ];
    }

    /**
     * Generar datos para gráfico de calificaciones
     */
    public function generarDatosCalificaciones(AsociacionesReportData $data): array
    {
        // Agrupar por rangos de calificación
        $rangos = [
            '4.5-5.0' => 0,
            '4.0-4.4' => 0,
            '3.5-3.9' => 0,
            '3.0-3.4' => 0,
            '< 3.0' => 0
        ];

        foreach ($data->asociaciones as $asociacion) {
            $calificacion = $asociacion['calificacion'] ?? 0;

            if ($calificacion >= 4.5) {
                $rangos['4.5-5.0']++;
            } elseif ($calificacion >= 4.0) {
                $rangos['4.0-4.4']++;
            } elseif ($calificacion >= 3.5) {
                $rangos['3.5-3.9']++;
            } elseif ($calificacion >= 3.0) {
                $rangos['3.0-3.4']++;
            } else {
                $rangos['< 3.0']++;
            }
        }

        return [
            'type' => 'bar',
            'data' => [
                'labels' => array_keys($rangos),
                'datasets' => [[
                    'label' => 'Asociaciones',
                    'data' => array_values($rangos),
                    'backgroundColor' => [
                        '#10B981', // Verde para excelente
                        '#3B82F6', // Azul para muy bueno
                        '#F59E0B', // Amarillo para bueno
                        '#F97316', // Naranja para regular
                        '#EF4444'  // Rojo para malo
                    ],
                    'borderWidth' => 2
                ]]
            ],
            'options' => [
                'responsive' => true,
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'title' => [
                            'display' => true,
                            'text' => 'Número de Asociaciones'
                        ]
                    ],
                    'x' => [
                        'title' => [
                            'display' => true,
                            'text' => 'Rango de Calificación'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Generar todos los gráficos
     */
    public function generarTodosLosGraficos(AsociacionesReportData $data): array
    {
        return [
            'municipalidades' => $this->generarDatosMunicipalidades($data),
            'estado' => $this->generarDatosEstado($data),
            'evolucion_temporal' => $this->generarDatosEvolucionTemporal($data),
            'distribucion_emprendedores' => $this->generarDatosDistribucionEmprendedores($data),
            'calificaciones' => $this->generarDatosCalificaciones($data)
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

        $textoGrafico = "Gráfico de " . ucfirst($tipo) . " - Asociaciones";
        $x = ($width - strlen($textoGrafico) * 10) / 2;
        $y = $height / 2;

        imagestring($imagen, 5, $x, $y, $textoGrafico, $texto);

        ob_start();
        imagepng($imagen);
        $contenido = ob_get_contents();
        ob_end_clean();

        imagedestroy($imagen);

        return 'data:image/png;base64,' . base64encode($contenido);
    }
}
