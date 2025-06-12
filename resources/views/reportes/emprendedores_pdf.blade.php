<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Emprendedores - Lago Titicaca</title>
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

        .emprendedores-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .emprendedores-table th,
        .emprendedores-table td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        .emprendedores-table th {
            background-color: #f1f5f9;
            color: #374151;
            font-weight: bold;
        }

        .emprendedores-table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .categoria-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: #475569;
        }

        .categoria-alojamiento {
            background-color: #059669;
        }

        .categoria-gastronomia {
            background-color: #dc2626;
        }

        .categoria-aventura {
            background-color: #7c3aed;
        }

        .categoria-artesanias {
            background-color: #ea580c;
        }

        .categoria-default {
            background-color: #6b7280;
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
    </style>
</head>
<body>
<!-- Header -->
<div class="header">
    <h1>Reporte de Emprendedores</h1>
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
                    {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ is_array($value) ? implode(', ', $value) : $value }}
                </span>
            @endif
        @endforeach
    </div>
@endif

<!-- Estadísticas Generales -->
@if($incluir_estadisticas)
    <div class="section">
        <div class="section-title">Resumen Ejecutivo</div>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $data->estadisticas['total'] }}</div>
                <div class="stat-label">Total Emprendedores</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $data->estadisticas['capacidad_total'] }}</div>
                <div class="stat-label">Capacidad Total</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $data->estadisticas['promedio_reservas'] }}</div>
                <div class="stat-label">Promedio Reservas/Mes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $data->estadisticas['porcentaje_certificaciones'] }}%</div>
                <div class="stat-label">Con Certificaciones</div>
            </div>
        </div>

        <div class="summary-grid">
            <div class="summary-item">
                <h4>Distribución por Categoría</h4>
                <ul class="summary-list">
                    @foreach($data->estadisticas['por_categoria'] as $categoria => $cantidad)
                        <li>• {{ $categoria }}: <span class="highlight">{{ $cantidad }}</span> emprendedores</li>
                    @endforeach
                </ul>
            </div>
            <div class="summary-item">
                <h4>Distribución por Municipalidad</h4>
                <ul class="summary-list">
                    @foreach($data->estadisticas['por_municipalidad'] as $municipalidad => $cantidad)
                        <li>• {{ $municipalidad }}: <span class="highlight">{{ $cantidad }}</span> emprendedores</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<!-- Lista de Emprendedores -->
<div class="section">
    <div class="section-title"> Detalle de Emprendedores</div>

    @if(count($data->emprendedores) > 0)
        <table class="emprendedores-table">
            <thead>
            <tr>
                <th style="width: 20%">Nombre</th>
                <th style="width: 12%">Categoría</th>
                <th style="width: 15%">Ubicación</th>
                <th style="width: 12%">Contacto</th>
                <th style="width: 10%">Precio</th>
                <th style="width: 8%">Capacidad</th>
                <th style="width: 8%">Reservas</th>
                <th style="width: 15%">Precio Prom. Servicios</th>
            </tr>
            </thead>
            <tbody>
            @foreach($data->emprendedores as $emprendedor)
                <tr>
                    <td>
                        <strong>{{ $emprendedor['nombre'] }}</strong>
                        <br>
                        <small style="color: #64748b;">{{ $emprendedor['tipo_servicio'] }}</small>
                        @if($emprendedor['facilidades_discapacidad'])
                            <br><small style="color: #059669;"> Accesible</small>
                        @endif
                    </td>
                    <td>
                        <span
                            class="categoria-badge">
                            {{ $emprendedor['categoria'] ?? 'Sin categoría' }}
                        </span>
                    </td>
                    <td>
                        <strong>{{ $emprendedor['asociacion_nombre'] ?? 'Sin comunidad' }}</strong>
                        <br>
                        <small>{{ $emprendedor['ubicacion'] ?? 'Sin municipalidad' }}</small>
                    </td>
                    <td class="contact-info">
                        @if($emprendedor['telefono'])
                            Tel: {{ $emprendedor['telefono'] }}<br>
                        @endif
                        @if($emprendedor['email'])
                            Email: {{ $emprendedor['email'] }}<br>
                        @endif
                        @if($emprendedor['pagina_web'])
                            Web: {{ $emprendedor['pagina_web'] }}
                        @endif
                    </td>
                    <td>{{ $emprendedor['precio_rango'] ?? 'N/D' }}</td>
                    <td style="text-align: center;">{{ $emprendedor['capacidad_aforo'] ?? 'N/D' }}</td>
                    <td style="text-align: center;">{{ $emprendedor['servicios_count'] ?? 'N/D' }}</td>
                    <td>{{ $emprendedor['precio_promedio_servicios'] ?? 'N/D' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">No se encontraron emprendedores con los filtros aplicados.</div>
    @endif
</div>

<!-- Footer -->
<div class="footer">
    Plataforma de Turismo Comunitario - Lago Titicaca © {{ date('Y') }}
</div>
</body>
</html>
