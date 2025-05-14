<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LugarTuristico extends Model
{
    use HasFactory;
    protected $table = 'lugares_turisticos';
    protected $fillable = [
        'nombre', 'descripcion', 'ubicacion', 'imagen'
    ];

}
