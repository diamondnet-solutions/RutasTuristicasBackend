<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Asociaciones - Lago Titicaca</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 3px solid #2563eb;
        }

        .header h1 {
            color: #1e40af;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header .subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .header .date {
            color: #64748b;
            font-size: 11px;
        }

        .section {
            margin-bottom: 25px;
        }

        .section-title {
            background-color: #f1f5f9;
            color: #1e40af;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: bold;
            border-left: 4px solid #2563eb;
            margin-bottom: 15px;
        }

        .filters-info {
            background-color: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #e2e8f0;
        }

        .filters-info h3 {
            color: #475569;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .filter-item {
            display: inline-block;
            background-color: #e0e7ff;
            color: #3730a3;
            padding: 4px 8px;
            border-radius: 3px;
            margin: 2px;
            font-size: 11px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
        }

        .stat-number {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 11px;
            color: #64748b;
        }

        /* Estilos para gráficos */
        .charts-section {
            margin-bottom: 30px;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 20px;
        }

        .chart-container {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .chart-title {
            font-size: 14px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 15px;
        }

        .chart-image {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .chart-full-width {
            grid-column: 1 / -1;
        }

        .insights-section {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .insights-title {
            color: #1e40af;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .insights-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .insight-item {
            background-color: #ffffff;
            border-radius: 6px;
            padding: 12px;
            border-left: 4px solid #3b82f6;
        }

        .insight-label {
            font-weight: bold;
            color: #374151;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .insight-value {
            color: #6b7280;
            font-size: 11px;
        }

        .asociaciones-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .asociaciones-table th,
        .asociaciones-table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        .asociaciones-table th {
            background-color: #f1f5f9;
            color: #374151;
            font-weight: bold;
        }

        .asociaciones-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .estado-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: white;
        }

        .estado-activo { background-color: #059669; }
        .estado-inactivo { background-color: #dc2626; }

        .calificacion-stars {
            color: #f59e0b;
            font-size: 10px;
        }

        .contact-info {
            font-size: 9px;
            color: #64748b;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #64748b;
            padding: 10px;
            border-top: 1px solid #e2e8f0;
        }

        .page-break {
            page-break-before: always;
        }

        .no-data {
            text-align: center;
            color: #64748b;
            font-style: italic;
            padding: 40px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .summary-item {
            background-color: #f8fafc;
            padding: 10px;
            border-radius: 5px;
            border-left: 3px solid #2563eb;
        }

        .summary-item h4 {
            color: #1e40af;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .summary-list {
            list-style: none;
            font-size: 10px;
        }

        .summary-list li {
            padding: 2px 0;
            color: #475569;
        }

        .highlight {
            background-color: #fef3c7;
            padding: 2px 4px;
            border-radius: 2px;
        }

        .performance-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .performance-card {
            background-color: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
        }

        .performance-title {
            font-size: 11px;
            color: #0369a1;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .performance-value {
            font-size: 16px;
            color: #1e40af;
            font-weight: bold;
        }
    </style>
</head>
<body>
<!-- Header -->
<div class="header">
    <h1>Reporte de Asociaciones</h1>
    <div class="subtitle">Turismo Comunitario - Lago Titicaca</div>
    <div class="date">
        Generado el {{ $data->fechaGeneracion }} por {{ $data->usuarioGenerador }}
    </div>
</div>

<!-- Filtros Aplicados -->
@if(!empty($data->filtros))
    <div class="filters-info">
        <h3>Filtros Aplicados:</h3>
        @foreach($data->filtros as $key => $value)
            @if($value !== null && $value !== '')
                <span class="filter-item">
                    {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ is_array($value) ? implode(', ', $value) : ($key === 'estado' ? ($value ? 'Activo' : 'Inactivo') : $value) }}
                </span>
            @endif
        @endforeach
    </div>
@endif

<!-- Estadísticas Generales -->
@if($incluir_estadisticas)
    <div class="section">
        <div class="section-title">📊 Resumen Ejecutivo</div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $data->estadisticas['total'] }}</div>
                <div class="stat-label">Total Asociaciones</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $data->estadisticas['total_emprendedores'] }}</div>
                <div class="stat-label">Total Emprendedores</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $data->estadisticas['total_servicios'] }}</div>
                <div class="stat-label">Total Servicios</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $data->estadisticas['promedio_calificacion'] }}</div>
                <div class="stat-label">Calificación Promedio</div>
            </div>
        </div>

        <!-- Métricas de Rendimiento -->
        <div class="performance-grid">
            <div class="performance-card">
                <div class="performance-title">Promedio Emprendedores</div>
                <div class="performance-value">{{ $data->estadisticas['promedio_emprendedores'] }}</div>
            </div>
            <div class="performance-card">
                <div class="performance-title">Promedio Servicios</div>
                <div class="performance-value">{{ $data->estadisticas['promedio_servicios'] }}</div>
            </div>
            <div class="performance-card">
                <div class="performance-title">Reservas Mensuales</div>
                <div class="performance-value">{{ $data->estadisticas['total_reservas_mes'] }}</div>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-item">
                <h4>Distribución por Municipalidad</h4>
                <ul class="summary-list">
                    @foreach($data->estadisticas['por_municipalidad'] as $municipalidad => $cantidad)
                        <li>• {{ $municipalidad }}: <span class="highlight">{{ $cantidad }}</span> asociaciones</li>
                    @endforeach
                </ul>
            </div>
            <div class="summary-item">
                <h4>Estado de Asociaciones</h4>
                <ul class="summary-list">
                    <li>• Activas: <span class="highlight">{{ $data->estadisticas['asociaciones_activas'] }}</span> ({{ $data->estadisticas['porcentaje_activas'] }}%)</li>
                    <li>• Inactivas: <span class="highlight">{{ $data->estadisticas['asociaciones_inactivas'] }}</span></li>
                </ul>
            </div>
        </div>
    </div>
@endif

<!-- Gráficos Estadísticos -->
@if($incluir_graficos && !empty($graficos))
    <div class="page-break"></div>
    <div class="section charts-section">
        <div class="section-title">📈 Análisis Gráfico</div>

        <div class="charts-grid">
            @if(isset($graficos['municipalidades']))
                <div class="chart-container">
                    <div class="chart-title">Distribución por Municipalidad</div>
                    <img src="{{ $graficos['municipalidades']['imagen'] }}" alt="Gráfico de Municipalidades" class="chart-image">
                </div>
            @endif

            @if(isset($graficos['estado']))
                <div class="chart-container">
                    <div class="chart-title">Estado de Asociaciones</div>
                    <img src="{{ $graficos['estado']['imagen'] }}" alt="Gráfico de Estado" class="chart-image">
                </div>
            @endif

            @if(isset($graficos['distribucion_emprendedores']))
                <div class="chart-container">
                    <div class="chart-title">Distribución por Emprendedores</div>
                    <img src="{{ $graficos['distribucion_emprendedores']['imagen'] }}" alt="Gráfico de Distribución" class="chart-image">
                </div>
            @endif

            @if(isset($graficos['calificaciones']))
                <div class="chart-container">
                    <div class="chart-title">Distribución de Calificaciones</div>
                    <img src="{{ $graficos['calificaciones']['imagen'] }}" alt="Gráfico de Calificaciones" class="chart-image">
                </div>
            @endif

            @if(isset($graficos['evolucion_temporal']))
                <div class="chart-container chart-full-width">
                    <div class="chart-title">Evolución Temporal de Creación</div>
                    <img src="{{ $graficos['evolucion_temporal']['imagen'] }}" alt="Gráfico de Evolución" class="chart-image">
                </div>
            @endif
        </div>

        <!-- Insights Estadísticos -->
        <div class="insights-section">
            <div class="insights-title">📈 Insights Estadísticos</div>
            <div class="insights-grid">
                <div class="insight-item">
                    <div class="insight-label">Municipalidad Líder</div>
                    <div class="insight-value">
                        @php
                            $municipalidadLider = collect($data->estadisticas['por_municipalidad'])->sortDesc()->keys()->first();
                            $cantidadLider = $data->estadisticas['por_municipalidad'][$municipalidadLider] ?? 0;
                        @endphp
                        <strong>{{ $municipalidadLider }}</strong> con <strong>{{ $cantidadLider }}</strong> asociaciones
                    </div>
                </div>
                <div class="insight-item">
                    <div class="insight-label">Mejor Calificada</div>
                    <div class="insight-value">
                        <strong>{{ $data->estadisticas['mejor_calificada'] ?? 'N/A' }}</strong> con la mejor calificación promedio
                    </div>
                </div>
                <div class="insight-item">
                    <div class="insight-label">Más Emprendedores</div>
                    <div class="insight-value">
                        <strong>{{ $data->estadisticas['mas_emprendedores'] ?? 'N/A' }}</strong> lidera en número de emprendedores
                    </div>
                </div>
                <div class="insight-item">
                    <div class="insight-label">Más Servicios</div>
                    <div class="insight-value">
                        <strong>{{ $data->estadisticas['mas_servicios'] ?? 'N/A' }}</strong> ofrece la mayor variedad de servicios
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Lista de Asociaciones -->
<div class="section">
    <div class="section-title">📋 Detalle de Asociaciones</div>

    @if(count($data->asociaciones) > 0)
        <table class="asociaciones-table">
            <thead>
            <tr>
                <th style="width: 20%">Nombre</th>
                <th style="width: 15%">Ubicación</th>
                <th style="width: 12%">Contacto</th>
                <th style="width: 8%">Estado</th>
                <th style="width: 10%">Emprendedores</th>
                <th style="width: 8%">Servicios</th>
                <th style="width: 10%">Calificación</th>
                <th style="width: 8%">Reservas/Mes</th>
                <th style="width: 9%">Años Operación</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data->asociaciones as $asociacion)
                <tr>
                    <td>
                        <strong>{{ $asociacion['nombre'] }}</strong>
                        <br>
                        <small style="color: #64748b;">{{ Str::limit($asociacion['descripcion'], 50) }}</small>
                    </td>
                    <td>
                        <strong>{{ $asociacion['ubicacion'] }}</strong>
                        <br>
                        <small>{{ $asociacion['municipalidad'] }}</small>
                    </td>
                    <td class="contact-info">
                        @if($asociacion['telefono'])
                            📞 {{ $asociacion['telefono'] }}<br>
                        @endif
                        @if($asociacion['email'])
                            ✉️ {{ $asociacion['email'] }}
                        @endif
                    </td>
                    <td>
                                <span class="estado-badge {{ $asociacion['estado'] ? 'estado-activo' : 'estado-inactivo' }}">
                                    {{ $asociacion['estado'] ? 'Activo' : 'Inactivo' }}
                                </span>
                    </td>
                    <td style="text-align: center;">
                        {{ $asociacion['emprendedores_count'] }}
                    </td>
                    <td style="text-align: center;">{{ $asociacion['servicios_count'] }}</td>
                    <td style="text-align: center;">
                                <span class="calificacion-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($asociacion['calificacion']))
                                            ★
                                        @elseif($i - 0.5 <= $asociacion['calificacion'])
                                            ☆
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </span>
                        <br>
                        <small>({{ $asociacion['calificacion'] }})</small>
                    </td>
                    <td style="text-align: center;">{{ $asociacion['reservas_mes'] }}</td>
                    <td style="text-align: center;">{{ $asociacion['años_operacion'] }} años</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            No se encontraron asociaciones con los filtros especificados.
        </div>
    @endif
</div>

<!-- Footer -->
<div class="footer">
    Reporte generado por el Sistema de Gestión Turística - Lago Titicaca |
    Página <span class="pagenum"></span> de <span class="pagecount"></span>
</div>
</body>
</html>
