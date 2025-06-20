<?php

namespace App\reservas\Emprendedores\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\reservas\Asociaciones\Models\Asociacion;
use App\Servicios\Models\Servicio;
use App\reservas\reserva\Models\Reserva;
use App\Pagegeneral\Models\Slider;
use App\Pagegeneral\Models\SliderDescripcion;
use App\Models\User;

class Emprendedor extends Model
{
    use HasFactory;

    protected $table = 'emprendedores';

    protected $fillable = [
        'nombre',
        'tipo_servicio',
        'descripcion',
        'ubicacion',
        'telefono',
        'email',
        'pagina_web',
        'horario_atencion',
        'precio_rango',
        'metodos_pago',
        'capacidad_aforo',
        'numero_personas_atiende',
        'comentarios_resenas',
        'imagenes',
        'categoria',
        'certificaciones',
        'idiomas_hablados',
        'opciones_acceso',
        'facilidades_discapacidad',
        'asociacion_id',
        'estado'
    ];

    protected $casts = [
        'metodos_pago' => 'array',
        'imagenes' => 'array',
        'certificaciones' => 'array',
        'idiomas_hablados' => 'array',
        'opciones_acceso' => 'array',
        'facilidades_discapacidad' => 'boolean',
        'estado' => 'boolean'
    ];

    /**
     * Obtener la asociación a la que pertenece el emprendedor
     */
    public function asociacion(): BelongsTo
    {
        return $this->belongsTo(Asociacion::class);
    }
    /**
     * Obtener los usuarios administradores del emprendimiento
     */
    public function administradores()
    {
        return $this->belongsToMany(User::class, 'user_emprendedor')
                    ->withPivot('es_principal', 'rol')
                    ->withTimestamps();
    }
     /**
     * Obtener el administrador principal del emprendimiento
     */
    public function administradorPrincipal()
    {
        return $this->belongsToMany(User::class, 'user_emprendedor')
            ->withPivot('es_principal', 'rol')
            ->wherePivot('es_principal', true)
            ->withTimestamps();
    }

    public function getAdministradorPrincipalAttribute()
    {
        return $this->administradores->firstWhere('pivot.es_principal', true);
    }
    public function servicios(): HasMany
    {
        return $this->hasMany(Servicio::class);
    }
    public function reservas(): BelongsToMany
    {
        return $this->belongsToMany(Reserva::class, 'reserva_detalle')
                    ->withPivot('descripcion', 'cantidad')
                    ->withTimestamps();
    }

    // Nuevas relaciones para sliders
    public function sliders(): HasMany
    {
        return $this->hasMany(Slider::class, 'entidad_id')
                    ->where('tipo_entidad', 'emprendedor')
                    ->orderBy('orden');
    }

    public function slidersPrincipales()
    {
        return $this->hasMany(Slider::class, 'entidad_id')
                    ->where('tipo_entidad', 'emprendedor')
                    ->where('es_principal', true)
                    ->orderBy('orden');
    }

    public function slidersSecundarios()
    {
        return $this->hasMany(Slider::class, 'entidad_id')
                    ->where('tipo_entidad', 'emprendedor')
                    ->where('es_principal', false)
                    ->with('descripcion')
                    ->orderBy('orden');
    }
}
