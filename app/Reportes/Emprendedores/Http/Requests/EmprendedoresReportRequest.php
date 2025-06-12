<?php

namespace App\Reportes\Emprendedores\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmprendedoresReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajustar según tus necesidades de autorización
    }

    public function rules(): array
    {
        return [
            'categoria' => 'nullable|string|max:100',
            'municipalidad' => 'nullable|string|max:100',
            'comunidad' => 'nullable|string|max:100',
            'asociacion_id' => 'nullable|integer|exists:asociaciones,id',
            'fecha_desde' => 'nullable|date|before_or_equal:fecha_hasta',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde',
            'facilidades_discapacidad' => 'nullable|boolean',
            'tipo_servicio' => 'nullable|string|max:100',
            'precio_min' => 'nullable|numeric|min:0',
            'precio_max' => 'nullable|numeric|min:0|gte:precio_min',
            'formato' => 'nullable|string|in:pdf,excel',
            'incluir_estadisticas' => 'nullable|boolean',
            'incluir_graficos' => 'nullable|boolean',
            'orientacion' => 'nullable|string|in:portrait,landscape',
        ];
    }

    public function messages(): array
    {
        return [
            'categoria.string' => 'La categoría debe ser un texto válido.',
            'municipalidad.string' => 'La municipalidad debe ser un texto válido.',
            'comunidad.string' => 'La comunidad debe ser un texto válido.',
            'asociacion_id.exists' => 'La asociación seleccionada no existe.',
            'fecha_desde.date' => 'La fecha desde debe ser una fecha válida.',
            'fecha_hasta.date' => 'La fecha hasta debe ser una fecha válida.',
            'fecha_desde.before_or_equal' => 'La fecha desde debe ser anterior o igual a la fecha hasta.',
            'fecha_hasta.after_or_equal' => 'La fecha hasta debe ser posterior o igual a la fecha desde.',
            'precio_min.numeric' => 'El precio mínimo debe ser un número.',
            'precio_max.numeric' => 'El precio máximo debe ser un número.',
            'precio_max.gte' => 'El precio máximo debe ser mayor o igual al precio mínimo.',
            'formato.in' => 'El formato debe ser pdf o excel.',
            'orientacion.in' => 'La orientación debe ser portrait o landscape.',
        ];
    }

    public function getFiltros(): array
    {
        return $this->only([
            'categoria',
            'municipalidad',
            'comunidad',
            'asociacion_id',
            'fecha_desde',
            'fecha_hasta',
            'facilidades_discapacidad',
            'tipo_servicio',
            'precio_min',
            'precio_max'
        ]);
    }

    public function getOpciones(): array
    {
        return [
            'formato' => $this->input('formato', 'pdf'),
            'incluir_estadisticas' => $this->boolean('incluir_estadisticas', true),
            'incluir_graficos' => $this->boolean('incluir_graficos', false),
            'orientacion' => $this->input('orientacion', 'portrait'),
        ];
    }
}
