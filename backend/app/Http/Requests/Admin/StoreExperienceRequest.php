<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company'      => ['required', 'string', 'max:150'],
            'position'     => ['required', 'string', 'max:150'],
            'location'     => ['nullable', 'string', 'max:150'],
            'description'  => ['nullable', 'string'],
            'company_url'  => ['nullable', 'url', 'max:255'],
            'company_logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:1024'],
            'started_at'   => ['required', 'date'],
            'finished_at'  => ['nullable', 'date', 'after_or_equal:started_at'],
            'is_current'   => ['nullable', 'boolean'],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'company.required'           => 'El nombre de la empresa es obligatorio.',
            'position.required'          => 'El cargo es obligatorio.',
            'started_at.required'        => 'La fecha de inicio es obligatoria.',
            'company_logo.image'         => 'El logo debe ser una imagen.',
            'company_logo.mimes'         => 'El logo debe ser jpg, jpeg, png, webp o svg.',
            'company_logo.max'           => 'El logo no puede superar 1MB.',
            'finished_at.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la de inicio.',
        ];
    }
}
