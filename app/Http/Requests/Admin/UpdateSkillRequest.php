<?php

namespace App\Http\Requests\Admin;

use App\Enums\SkillGroup;
use App\Models\Skill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string|object>> */
    public function rules(): array
    {
        $skill = $this->route('skill');
        $id = $skill instanceof Skill ? $skill->getKey() : null;

        return [
            'name' => ['sometimes', 'string', 'max:100', "unique:skills,name,{$id}"],
            'group' => ['sometimes', 'nullable', Rule::enum(SkillGroup::class)],
            'level' => ['sometimes', 'integer', 'min:1', 'max:5'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:255'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }
}
