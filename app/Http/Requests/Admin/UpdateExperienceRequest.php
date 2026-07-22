<?php

namespace App\Http\Requests\Admin;

use App\Models\Experience;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

class UpdateExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string|object>> */
    public function rules(): array
    {
        return [
            'company' => ['sometimes', 'string', 'max:150'],
            'position' => ['sometimes', 'string', 'max:150'],
            'location' => ['sometimes', 'nullable', 'string', 'max:150'],
            'description' => ['sometimes', 'nullable', 'string'],
            'company_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'company_logo' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'started_at' => ['sometimes', 'date'],
            'finished_at' => ['sometimes', 'nullable', 'date'],
            'is_current' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $experience = $this->route('experience');

            if (! $experience instanceof Experience) {
                return;
            }

            $isCurrent = $this->has('is_current')
                ? $this->boolean('is_current')
                : $experience->is_current;
            $startedAt = $this->input('started_at', $experience->started_at->toDateString());
            $finishedAt = $this->input('finished_at', $experience->finished_at?->toDateString());

            if (! $isCurrent && blank($finishedAt)) {
                $validator->errors()->add('finished_at', 'La fecha de fin es obligatoria si la experiencia no es actual.');

                return;
            }

            if (! $validator->errors()->has('started_at')
                && ! $validator->errors()->has('finished_at')
                && $startedAt
                && $finishedAt
                && Carbon::parse($finishedAt)->lt(Carbon::parse($startedAt))) {
                $validator->errors()->add('finished_at', 'La fecha de fin debe ser igual o posterior a la de inicio.');
            }
        }];
    }

    public function messages(): array
    {
        return [
            'company_logo.image' => 'El logo debe ser una imagen.',
            'company_logo.mimes' => 'El logo debe ser jpg, jpeg, png o webp.',
            'company_logo.max' => 'El logo no puede superar 1MB.',
            'finished_at.required' => 'La fecha de fin es obligatoria si la experiencia no es actual.',
            'finished_at.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la de inicio.',
        ];
    }
}
