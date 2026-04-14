<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('category')?->id;

        return [
            'name'        => ['sometimes', 'string', 'max:100', "unique:categories,name,{$id}"],
            'description' => ['sometimes', 'nullable', 'string', 'max:500'],
            'color'       => ['sometimes', 'nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sort_order'  => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Ya existe una categoría con ese nombre.',
            'color.regex' => 'El color debe ser un hexadecimal válido (ej: #3B82F6).',
        ];
    }
}
