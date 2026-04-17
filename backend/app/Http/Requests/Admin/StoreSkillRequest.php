<?php

namespace App\Http\Requests\Admin;

use App\Enums\SkillGroup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:100', 'unique:skills,name'],
            'group'       => ['nullable', Rule::enum(SkillGroup::class)],
            'level'       => ['nullable', 'integer', 'min:1', 'max:5'],
            'icon'        => ['nullable', 'string', 'max:255'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la habilidad es obligatorio.',
            'name.unique'   => 'Ya existe una habilidad con ese nombre.',
            'group.enum'    => 'El grupo seleccionado no es valido.',
            'level.min'     => 'El nivel minimo es 1.',
            'level.max'     => 'El nivel maximo es 5.',
        ];
    }
}
