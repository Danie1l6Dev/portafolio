<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('skill')?->id;

        return [
            'name'        => ['sometimes', 'string', 'max:100', "unique:skills,name,{$id}"],
            'group'       => ['sometimes', 'nullable', 'string', 'max:80'],
            'level'       => ['sometimes', 'integer', 'min:1', 'max:5'],
            'icon'        => ['sometimes', 'nullable', 'string', 'max:255'],
            'sort_order'  => ['sometimes', 'integer', 'min:0'],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }
}
