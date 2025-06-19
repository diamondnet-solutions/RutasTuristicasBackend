<?php

namespace App\Reportes\Asociaciones\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AsociacionesReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajustar según tus necesidades de autorización
    }

    public function rules(): array
    {
        return [
            'municipalidad' => 'nullable|string|max:100',
            'estado' => 'nullable|boolean',
            'fecha_desde' => 'nullable|date|before_or_equal:fecha_hasta',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde',
            'emprendedores_min' => 'nullable|integer|min:0',
            'emprendedores_max' => 'nullable|integer|min:0|gte:emprendedores_min',
            'calificacion_min' => 'nullable|numeric|min:1|max:5',
            'calificacion_max' => 'nullable|numeric|min:1|max:5|gte:calificacion_min',
            'nombre' => 'nullable|string|max:255',
            'orden_por' => 'nullable|string|in:nombre,emprendedores,servicios,calificacion,fecha_creacion',
            'direccion' => 'nullable|string|in:asc,desc',
            'formato' => 'nullable|string|in:pdf,excel',
            'incluir_estadisticas' => 'nullable|boolean',
            'incluir_graficos' => 'nullable|boolean',
            'orientacion' => 'nullable|string|in:portrait,landscape',
            'tipo_reporte' => 'nullable|string|in:completo,resumen,comparativo',
        ];
    }

    public function messages(): array
    {
        return [
            'municipalidad.string' => 'La municipalidad debe ser un texto válido.',
            'estado.boolean' => 'El estado debe ser verdadero o falso.',
            'fecha_desde.date' => 'La fecha desde debe ser una fecha válida.',
            'fecha_hasta.date' => 'La fecha hasta debe ser una fecha válida.',
            'fecha_desde.before_or_equal' => 'La fecha desde debe ser anterior o igual a la fecha hasta.',
            'fecha_hasta.after_or_equal' => 'La fecha hasta debe ser posterior o igual a la fecha desde.',
            'emprendedores_min.integer' => 'El número mínimo de emprendedores debe ser un entero.',
            'emprendedores_max.integer' => 'El número máximo de emprendedores debe ser un entero.',
            'emprendedores_max.gte' => 'El número máximo debe ser mayor o igual al mínimo.',
            'calificacion_min.numeric' => 'La calificación mínima debe ser un número.',
            'calificacion_max.numeric' => 'La calificación máxima debe ser un número.',
            'calificacion_min.min' => 'La calificación mínima debe ser al menos 1.',
            'calificacion_max.max' => 'La calificación máxima debe ser máximo 5.',
            'orden_por.in' => 'El campo de ordenamiento no es válido.',
            'direccion.in' => 'La dirección de ordenamiento debe ser asc o desc.',
            'formato.in' => 'El formato debe ser pdf o excel.',
            'orientacion.in' => 'La orientación debe ser portrait o landscape.',
            'tipo_reporte.in' => 'El tipo de reporte debe ser completo, resumen o comparativo.',
        ];
    }

    public function getFiltros(): array
    {
        return $this->only([
            'municipalidad',
            'estado',
            'fecha_desde',
            'fecha_hasta',
            'emprendedores_min',
            'emprendedores_max',
            'calificacion_min',
            'calificacion_max',
            'nombre',
            'orden_por',
            'direccion'
        ]);
    }

    public function getOpciones(): array
    {
        return [
            'formato' => $this->input('formato', 'pdf'),
            'incluir_estadisticas' => $this->boolean('incluir_estadisticas', true),
            'incluir_graficos' => $this->boolean('incluir_graficos', true),
            'orientacion' => $this->input('orientacion', 'portrait'),
            'tipo_reporte' => $this->input('tipo_reporte', 'completo'),
        ];
    }
}
