<?php

namespace App\Reportes\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReporteFechaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Puedes personalizar esta lógica si usas políticas de autorización.
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_inicio' => 'required|date|before_or_equal:fecha_fin',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_inicio.required'        => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date'            => 'La fecha de inicio debe tener un formato válido.',
            'fecha_inicio.before_or_equal' => 'La fecha de inicio debe ser anterior o igual a la fecha final.',
            'fecha_fin.required'           => 'La fecha final es obligatoria.',
            'fecha_fin.date'               => 'La fecha final debe tener un formato válido.',
            'fecha_fin.after_or_equal'     => 'La fecha final debe ser posterior o igual a la fecha de inicio.',
        ];
    }
}
