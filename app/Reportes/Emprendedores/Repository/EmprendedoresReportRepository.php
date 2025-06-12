<?php

namespace App\Reportes\Emprendedores\Repository;

use App\reservas\Emprendedores\Models\Emprendedor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmprendedoresReportRepository
{
    /**
     * Obtener emprendedores para reporte con filtros aplicados.
     */
    public function obtenerEmprendedoresParaReporte(array $filtros = []): Collection
    {
        $query = Emprendedor::query()
            ->with([
                'asociacion:id,nombre,municipalidad_id',
                'asociacion.municipalidad:id,nombre',
                'servicios:id,emprendedor_id,nombre,precio_referencial,estado',
                'administradores:id,name,email',
            ])
            ->where('estado', true);

        $this->aplicarFiltros($query, $filtros);

        return $query->get()->map(function ($emprendedor) {
            return $this->transformarEmprendedor($emprendedor);
        });
    }

    /**
     * Aplicar filtros a la consulta.
     */
    private function aplicarFiltros(Builder $query, array $filtros): void
    {
        if (!empty($filtros['categoria'])) {
            $query->where('categoria', $filtros['categoria']);
        }

        if (!empty($filtros['asociacion_id'])) {
            $query->where('asociacion_id', $filtros['asociacion_id']);
        }

        if (!empty($filtros['fecha_desde'])) {
            $query->where('created_at', '>=', Carbon::parse($filtros['fecha_desde'])->startOfDay());
        }

        if (!empty($filtros['fecha_hasta'])) {
            $query->where('created_at', '<=', Carbon::parse($filtros['fecha_hasta'])->endOfDay());
        }

        if (array_key_exists('facilidades_discapacidad', $filtros)) {
            $query->where('facilidades_discapacidad', (bool) $filtros['facilidades_discapacidad']);
        }

        if (!empty($filtros['tipo_servicio'])) {
            $query->where('tipo_servicio', 'like', '%' . $filtros['tipo_servicio'] . '%');
        }

        if (!empty($filtros['metodo_pago'])) {
            $query->whereJsonContains('metodos_pago', $filtros['metodo_pago']);
        }

        if (!empty($filtros['idioma'])) {
            $query->whereJsonContains('idiomas_hablados', $filtros['idioma']);
        }

        if (!empty($filtros['certificacion'])) {
            $query->whereJsonContains('certificaciones', $filtros['certificacion']);
        }

        if (!empty($filtros['capacidad_min'])) {
            $query->where('capacidad_aforo', '>=', (int) $filtros['capacidad_min']);
        }

        if (!empty($filtros['capacidad_max'])) {
            $query->where('capacidad_aforo', '<=', (int) $filtros['capacidad_max']);
        }

        $this->aplicarFiltroPrecio($query, $filtros);

        if (!empty($filtros['ubicacion'])) {
            $query->where('ubicacion', 'like', '%' . $filtros['ubicacion'] . '%');
        }

        if (!empty($filtros['con_servicios'])) {
            $query->has('servicios');
        }

        if (!empty($filtros['con_administrador'])) {
            $query->whereHas('administradores', function ($q) {
                $q->wherePivot('es_principal', true);
            });
        }
    }

    /**
     * Aplicar filtro por rango de precios.
     */
    private function aplicarFiltroPrecio(Builder $query, array $filtros): void
    {
        if (empty($filtros['precio_min']) && empty($filtros['precio_max'])) {
            return;
        }

        $query->where(function ($q) use ($filtros) {
            $q->whereNotNull('precio_rango');

            if (!empty($filtros['precio_min'])) {
                $q->whereRaw("
                (SELECT (regexp_matches(precio_rango, '\\d+', 'g'))[1]::INTEGER) >= ?
            ", [(int) $filtros['precio_min']]);
            }

            if (!empty($filtros['precio_max'])) {
                $q->whereRaw("
                (SELECT (regexp_matches(precio_rango, '\\d+', 'g'))[2]::INTEGER) <= ?
            ", [(int) $filtros['precio_max']]);
            }
        });
    }


    /**
     * Transformar emprendedor para el reporte.
     */
    private function transformarEmprendedor($emprendedor): array
    {
        return [
            'id' => $emprendedor->id,
            'nombre' => $emprendedor->nombre,
            'tipo_servicio' => $emprendedor->tipo_servicio,
            'descripcion' => $emprendedor->descripcion,
            'ubicacion' => $emprendedor->ubicacion,
            'telefono' => $emprendedor->telefono,
            'email' => $emprendedor->email,
            'pagina_web' => $emprendedor->pagina_web,
            'horario_atencion' => $emprendedor->horario_atencion,
            'precio_rango' => $emprendedor->precio_rango,
            'metodos_pago' => $emprendedor->metodos_pago,
            'capacidad_aforo' => $emprendedor->capacidad_aforo,
            'numero_personas_atiende' => $emprendedor->numero_personas_atiende,
            'categoria' => $emprendedor->categoria,
            'certificaciones' => $emprendedor->certificaciones,
            'idiomas_hablados' => $emprendedor->idiomas_hablados,
            'opciones_acceso' => $emprendedor->opciones_acceso,
            'facilidades_discapacidad' => $emprendedor->facilidades_discapacidad,
            'estado' => $emprendedor->estado,
            'created_at' => $emprendedor->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $emprendedor->updated_at?->format('Y-m-d H:i:s'),
            'asociacion_id' => $emprendedor->asociacion_id,
            'asociacion_nombre' => $emprendedor->asociacion?->nombre ?? 'Sin asociación',
            'administrador_principal' => optional($emprendedor->administradorPrincipal)->only(['id', 'name', 'email']),
            'servicios_count' => $emprendedor->servicios->count(),
            'servicios_activos' => $emprendedor->servicios->where('estado', true)->count(),
            'precio_promedio_servicios' => $this->calcularPrecioPromedioServicios($emprendedor->servicios),
            'tiene_pagina_web' => !empty($emprendedor->pagina_web),
            'acepta_pagos_digitales' => $this->aceptaPagosDigitales($emprendedor->metodos_pago),
            'es_multilingue' => is_array($emprendedor->idiomas_hablados) && count($emprendedor->idiomas_hablados) > 1,
            'tiene_certificaciones' => is_array($emprendedor->certificaciones) && count($emprendedor->certificaciones) > 0,
        ];
    }

    /**
     * Calcular el precio promedio de los servicios.
     */
    private function calcularPrecioPromedioServicios($servicios): ?float
    {
        $serviciosConPrecio = $servicios->filter(fn($s) => !is_null($s->precio_referencial) && $s->precio_referencial > 0);

        return $serviciosConPrecio->isNotEmpty()
            ? round($serviciosConPrecio->avg('precio_referencial'), 2)
            : null;
    }

    /**
     * Verificar si acepta pagos digitales.
     */
    private function aceptaPagosDigitales($metodosPago): bool
    {
        if (!is_array($metodosPago)) {
            return false;
        }

        $pagosDigitales = ['tarjeta', 'transferencia', 'yape', 'plin', 'paypal', 'mercadopago'];

        return !empty(array_intersect(array_map('strtolower', $metodosPago), $pagosDigitales));
    }

    /**
     * Obtener categorías únicas.
     */
    public function obtenerCategorias(): Collection
    {
        return Emprendedor::where('estado', true)
            ->whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria')
            ->filter()
            ->sort()
            ->values();
    }

    /**
     * Obtener municipalidades distintas.
     */
    public function obtenerMunicipalidades(): Collection
    {
        return Emprendedor::where('emprendedores.estado', true)
            ->join('asociaciones', 'emprendedores.asociacion_id', '=', 'asociaciones.id')
            ->join('municipalidad', 'asociaciones.municipalidad_id', '=', 'municipalidad.id')
            ->select('municipalidad.nombre')
            ->distinct()
            ->pluck('nombre')
            ->filter()
            ->sort()
            ->values();
    }

    /**
     * Obtener comunidades desde campo texto JSON.
     */
    public function obtenerComunidades(): Collection
    {
        $comunidadesTexto = DB::table('municipalidad')
            ->whereNotNull('comunidades')
            ->pluck('comunidades');

        $comunidades = collect();

        foreach ($comunidadesTexto as $lista) {
            $elementos = json_decode($lista, true);
            if (is_array($elementos)) {
                $comunidades = $comunidades->merge($elementos);
            }
        }

        return $comunidades->unique()->sort()->values();
    }
}
