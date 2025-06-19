<?php

namespace App\Reportes\Asociaciones\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\pagegeneral\models\Municipalidad;
use App\reservas\emprendedores\models\Emprendedor;

class Asociacion extends Model
{
    use HasFactory;

    protected $table = 'asociaciones';

    protected $fillable = [
        'nombre',
        'descripcion',
        'ubicacion',
        'telefono',
        'email',
        'municipalidad_id',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relación con Municipalidad
     */
    public function municipalidad(): BelongsTo
    {
        return $this->belongsTo(Municipalidad::class, 'municipalidad_id');
    }

    /**
     * Relación con Emprendedores
     */
    public function emprendedores(): HasMany
    {
        return $this->hasMany(Emprendedor::class, 'asociacion_id');
    }

    /**
     * Relación con Emprendedores activos
     */
    public function emprendedoresActivos(): HasMany
    {
        return $this->hasMany(Emprendedor::class, 'asociacion_id')->where('estado', true);
    }

    /**
     * Accessor para obtener el total de servicios
     */
    public function getTotalServiciosAttribute(): int
    {
        return $this->emprendedores->sum(function ($emprendedor) {
            return $emprendedor->servicios->count();
        });
    }

    /**
     * Accessor para obtener la calificación promedio
     */
    public function getCalificacionPromedioAttribute(): float
    {
        $servicios = $this->emprendedores->flatMap->servicios;
        return round($servicios->avg('calificacion') ?? 0, 2);
    }

    /**
     * Accessor para obtener el total de reservas mensuales
     */
    public function getTotalReservasMesAttribute(): int
    {
        $servicios = $this->emprendedores->flatMap->servicios;
        return $servicios->sum('reservas_mes') ?? 0;
    }

    /**
     * Accessor para obtener el total de ingresos mensuales
     */
    public function getTotalIngresosMesAttribute(): float
    {
        $servicios = $this->emprendedores->flatMap->servicios;
        return $servicios->sum('ingresos_mes') ?? 0;
    }

    /**
     * Scope para asociaciones activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Scope para asociaciones inactivas
     */
    public function scopeInactivas($query)
    {
        return $query->where('estado', false);
    }

    /**
     * Scope para filtrar por municipalidad
     */
    public function scopePorMunicipalidad($query, $municipalidadId)
    {
        return $query->where('municipalidad_id', $municipalidadId);
    }

    /**
     * Scope para asociaciones con emprendedores
     */
    public function scopeConEmprendedores($query)
    {
        return $query->has('emprendedores');
    }

    /**
     * Scope para asociaciones con servicios
     */
    public function scopeConServicios($query)
    {
        return $query->whereHas('emprendedores.servicios');
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('created_at', [$fechaInicio, $fechaFin]);
    }

    /**
     * Scope para asociaciones con calificación mínima
     */
    public function scopeConCalificacionMinima($query, $calificacionMinima)
    {
        return $query->whereHas('emprendedores.servicios', function ($subQuery) use ($calificacionMinima) {
            $subQuery->havingRaw('AVG(calificacion) >= ?', [$calificacionMinima]);
        });
    }
}
