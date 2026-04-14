<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('project')?->id;

        return [
            'title'       => ['sometimes', 'string', 'max:200', "unique:projects,title,{$id}"],
            'category_id' => ['sometimes', 'nullable', 'exists:categories,id'],
            'summary'     => ['sometimes', 'string', 'max:500'],
            'description' => ['sometimes', 'nullable', 'string'],
            'demo_url'    => ['sometimes', 'nullable', 'url', 'max:255'],
            'repo_url'    => ['sometimes', 'nullable', 'url', 'max:255'],
            'cover_image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status'      => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
            'is_featured' => ['sometimes', 'boolean'],
            'sort_order'  => ['sometimes', 'integer', 'min:0'],
            'started_at'  => ['sometimes', 'nullable', 'date'],
            'finished_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:started_at'],
            'skill_ids'   => ['sometimes', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.unique'               => 'Ya existe un proyecto con ese título.',
            'cover_image.image'          => 'El archivo debe ser una imagen.',
            'cover_image.mimes'          => 'La imagen debe ser jpg, jpeg, png o webp.',
            'cover_image.max'            => 'La imagen no puede superar 2MB.',
            'status.in'                  => 'El estado debe ser draft, published o archived.',
            'finished_at.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la de inicio.',
            'skill_ids.*.exists'         => 'Una o más habilidades seleccionadas no existen.',
        ];
    }
}
