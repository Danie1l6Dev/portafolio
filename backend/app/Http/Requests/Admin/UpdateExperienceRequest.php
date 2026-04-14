<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company'      => ['sometimes', 'string', 'max:150'],
            'position'     => ['sometimes', 'string', 'max:150'],
            'location'     => ['sometimes', 'nullable', 'string', 'max:150'],
            'description'  => ['sometimes', 'nullable', 'string'],
            'company_url'  => ['sometimes', 'nullable', 'url', 'max:255'],
            'company_logo' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:1024'],
            'started_at'   => ['sometimes', 'date'],
            'finished_at'  => ['sometimes', 'nullable', 'date', 'after_or_equal:started_at'],
            'is_current'   => ['sometimes', 'boolean'],
            'sort_order'   => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_logo.image'         => 'El logo debe ser una imagen.',
            'company_logo.mimes'         => 'El logo debe ser jpg, jpeg, png, webp o svg.',
            'company_logo.max'           => 'El logo no puede superar 1MB.',
            'finished_at.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la de inicio.',
        ];
    }
}
