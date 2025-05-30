<?php

namespace App\Reportes\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $table = 'reportes'; // Puedes cambiar esto si no usas una tabla específica
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'datos',
    ];

    protected $casts = [
        'datos' => 'array',
    ];
}
