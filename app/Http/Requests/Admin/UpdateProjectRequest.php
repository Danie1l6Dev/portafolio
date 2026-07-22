<?php

namespace App\Http\Requests\Admin;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string|object>> */
    public function rules(): array
    {
        $project = $this->route('project');
        $id = $project instanceof Project ? $project->getKey() : null;

        return [
            'title' => ['sometimes', 'string', 'max:200', "unique:projects,title,{$id}"],
            'category_id' => ['sometimes', 'nullable', 'exists:categories,id'],
            'summary' => ['sometimes', 'string', 'max:500'],
            'description' => ['sometimes', 'nullable', 'string'],
            'demo_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'repo_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'cover_image' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gallery_images' => ['sometimes', 'nullable', 'array', 'max:8'],
            'gallery_images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
            'is_featured' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'started_at' => ['sometimes', 'nullable', 'date'],
            'finished_at' => ['sometimes', 'nullable', 'date', 'after_or_equal:started_at'],
            'skill_ids' => ['sometimes', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $project = $this->route('project');

            if (! $project instanceof Project) {
                return;
            }

            $startedAt = $this->input('started_at', $project->started_at?->toDateString());
            $finishedAt = $this->input('finished_at', $project->finished_at?->toDateString());

            if (! $validator->errors()->has('started_at')
                && ! $validator->errors()->has('finished_at')
                && $startedAt
                && $finishedAt
                && Carbon::parse($finishedAt)->lt(Carbon::parse($startedAt))) {
                $validator->errors()->add('finished_at', 'La fecha de fin debe ser igual o posterior a la de inicio.');
            }

            $images = $this->file('gallery_images', []);
            $newImageCount = is_array($images) ? count($images) : 1;
            $existingImageCount = $project->media()->inCollection('gallery')->count();

            if ($existingImageCount + $newImageCount > 8) {
                $validator->errors()->add('gallery_images', 'La galería admite un máximo total de 8 imágenes.');
            }
        }];
    }

    public function messages(): array
    {
        return [
            'title.unique' => 'Ya existe un proyecto con ese título.',
            'cover_image.image' => 'El archivo debe ser una imagen.',
            'cover_image.mimes' => 'La imagen debe ser jpg, jpeg, png o webp.',
            'cover_image.max' => 'La imagen no puede superar 2MB.',
            'gallery_images.max' => 'La galería admite un máximo de 8 imágenes por envío.',
            'gallery_images.*.image' => 'Cada archivo de la galería debe ser una imagen.',
            'gallery_images.*.mimes' => 'Las imágenes de galería deben ser jpg, jpeg, png o webp.',
            'gallery_images.*.max' => 'Cada imagen de galería puede pesar hasta 2MB.',
            'status.in' => 'El estado debe ser draft, published o archived.',
            'finished_at.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la de inicio.',
            'skill_ids.*.exists' => 'Una o más habilidades seleccionadas no existen.',
        ];
    }
}
