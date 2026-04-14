<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:200', 'unique:projects,title'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'summary'     => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'demo_url'    => ['nullable', 'url', 'max:255'],
            'repo_url'    => ['nullable', 'url', 'max:255'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status'      => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'started_at'  => ['nullable', 'date'],
            'finished_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'skill_ids'   => ['nullable', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'         => 'El título del proyecto es obligatorio.',
            'title.unique'           => 'Ya existe un proyecto con ese título.',
            'summary.required'       => 'El resumen del proyecto es obligatorio.',
            'category_id.exists'     => 'La categoría seleccionada no existe.',
            'cover_image.image'      => 'El archivo debe ser una imagen.',
            'cover_image.mimes'      => 'La imagen debe ser jpg, jpeg, png o webp.',
            'cover_image.max'        => 'La imagen no puede superar 2MB.',
            'status.in'              => 'El estado debe ser draft, published o archived.',
            'finished_at.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la de inicio.',
            'skill_ids.*.exists'     => 'Una o más habilidades seleccionadas no existen.',
        ];
    }
}
