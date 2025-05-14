<?php
namespace App\reservas\reserva\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Reservas\Reserva\Models\ReservaServicio;

class Reserva extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'usuario_id',
        'codigo_reserva',
        'estado',
        'notas',
    ];
    
    // Estados de la reserva
    const ESTADO_PENDIENTE = 'pendiente';
    const ESTADO_CONFIRMADA = 'confirmada';
    const ESTADO_CANCELADA = 'cancelada';
    const ESTADO_COMPLETADA = 'completada';
    
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    public function servicios(): HasMany
    {
        return $this->hasMany(ReservaServicio::class);
    }
    
    /**
     * Genera un código único para la reserva
     */
    public static function generarCodigoReserva(): string
    {
        $codigo = strtoupper(substr(uniqid(), -6)) . date('ymd');
        
        // Verificar que no exista ya una reserva con este código
        while (self::where('codigo_reserva', $codigo)->exists()) {
            $codigo = strtoupper(substr(uniqid(), -6)) . date('ymd');
        }
        
        return $codigo;
    }
    
    /**
     * Calcula el total de servicios en la reserva
     */
    public function getTotalServiciosAttribute(): int
    {
        return $this->servicios()->count();
    }
    
    /**
     * Calcula la fecha de inicio (el primer servicio)
     */
    public function getFechaInicioAttribute(): ?string
    {
        $primerServicio = $this->servicios()->orderBy('fecha_inicio')->first();
        return $primerServicio ? $primerServicio->fecha_inicio : null;
    }
    
    /**
     * Calcula la fecha de fin (el último servicio)
     */
    public function getFechaFinAttribute(): ?string
    {
        $ultimoServicio = $this->servicios()->orderBy('fecha_fin', 'desc')->first();
        return $ultimoServicio ? $ultimoServicio->fecha_fin ?? $ultimoServicio->fecha_inicio : null;
    }
}