<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\AuthorizesContentEditors;
use App\Models\Experience;
use App\Services\ImageService;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Throwable;

class ExperienceManager extends Component
{
    use AuthorizesContentEditors;
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public bool $currentOnly = false;

    public bool $showForm = false;

    public ?int $editingExperienceId = null;

    public string $company = '';

    public string $position = '';

    public string $location = '';

    public string $description = '';

    public string $companyUrl = '';

    public mixed $companyLogo = null;

    public string $startedAt = '';

    public string $finishedAt = '';

    public bool $isCurrent = false;

    public int $sortOrder = 0;

    public bool $confirmingDelete = false;

    public ?int $deletingExperienceId = null;

    public string $deletingExperienceName = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCurrentOnly(): void
    {
        $this->resetPage();
    }

    public function updatedIsCurrent(bool $value): void
    {
        if ($value) {
            $this->finishedAt = '';
            $this->resetValidation('finishedAt');
        }
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $experienceId): void
    {
        $experience = Experience::findOrFail($experienceId);

        $this->editingExperienceId = $experience->id;
        $this->company = $experience->company;
        $this->position = $experience->position;
        $this->location = $experience->location ?? '';
        $this->description = $experience->description ?? '';
        $this->companyUrl = $experience->company_url ?? '';
        $this->startedAt = $experience->started_at->toDateString();
        $this->finishedAt = $experience->finished_at?->toDateString() ?? '';
        $this->isCurrent = $experience->is_current;
        $this->sortOrder = $experience->sort_order;
        $this->showForm = true;
        $this->resetErrorBag();
    }

    public function save(ImageService $imageService): void
    {
        $this->authorizeContentEditor();

        $validated = $this->validate([
            'company' => ['required', 'string', 'max:150'],
            'position' => ['required', 'string', 'max:150'],
            'location' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'companyUrl' => ['nullable', 'url', 'max:255'],
            'companyLogo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
            'startedAt' => ['required', 'date'],
            'finishedAt' => [Rule::requiredIf(! $this->isCurrent), 'nullable', 'date', 'after_or_equal:startedAt'],
            'isCurrent' => ['boolean'],
            'sortOrder' => ['required', 'integer', 'min:0'],
        ]);

        $experience = $this->editingExperienceId
            ? Experience::findOrFail($this->editingExperienceId)
            : new Experience;
        $oldLogo = $experience->company_logo;

        $data = [
            'company' => $validated['company'],
            'position' => $validated['position'],
            'location' => filled($validated['location']) ? $validated['location'] : null,
            'description' => filled($validated['description']) ? $validated['description'] : null,
            'company_url' => filled($validated['companyUrl']) ? $validated['companyUrl'] : null,
            'started_at' => $validated['startedAt'],
            'finished_at' => $validated['isCurrent'] ? null : $validated['finishedAt'],
            'is_current' => $validated['isCurrent'],
            'sort_order' => $validated['sortOrder'],
        ];

        $newLogo = null;

        try {
            if ($this->companyLogo) {
                $newLogo = $imageService->store($this->companyLogo, 'experiences');
                $data['company_logo'] = $newLogo;
            }

            $experience->fill($data)->save();
        } catch (Throwable $exception) {
            $imageService->delete($newLogo);

            throw $exception;
        }

        if ($this->companyLogo && $oldLogo && $oldLogo !== $experience->company_logo) {
            $imageService->delete($oldLogo);
        }

        $this->resetForm();
        Flux::toast(variant: 'success', text: 'Experiencia guardada correctamente.');
    }

    public function confirmDelete(int $experienceId): void
    {
        $experience = Experience::findOrFail($experienceId);

        $this->deletingExperienceId = $experience->id;
        $this->deletingExperienceName = "{$experience->position} · {$experience->company}";
        $this->confirmingDelete = true;
    }

    public function delete(ImageService $imageService): void
    {
        $this->authorizeContentEditor();

        $experience = Experience::query()->with('media')->findOrFail($this->deletingExperienceId);
        $paths = $experience->media->pluck('path')->push($experience->company_logo)->filter()->all();

        $experience->media()->delete();
        $experience->delete();

        foreach ($paths as $path) {
            $imageService->delete($path);
        }

        $this->cancelDelete();
        $this->resetPage();
        Flux::toast(variant: 'success', text: 'Experiencia eliminada.');
    }

    public function cancelDelete(): void
    {
        $this->reset('confirmingDelete', 'deletingExperienceId', 'deletingExperienceName');
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        return view('livewire.admin.experience-manager', [
            'experiences' => $this->experiences(),
            'editingExperience' => $this->editingExperienceId
                ? Experience::find($this->editingExperienceId)
                : null,
        ]);
    }

    /** @return LengthAwarePaginator<int, Experience> */
    private function experiences(): LengthAwarePaginator
    {
        return Experience::query()
            ->when($this->search !== '', fn ($query) => $query->where(function ($query): void {
                $query->where('company', 'like', "%{$this->search}%")
                    ->orWhere('position', 'like', "%{$this->search}%")
                    ->orWhere('location', 'like', "%{$this->search}%");
            }))
            ->when($this->currentOnly, fn ($query) => $query->current())
            ->ordered()
            ->paginate(10);
    }

    private function resetForm(): void
    {
        $this->reset(
            'showForm',
            'editingExperienceId',
            'company',
            'position',
            'location',
            'description',
            'companyUrl',
            'companyLogo',
            'startedAt',
            'finishedAt',
            'isCurrent',
            'sortOrder',
        );
        $this->resetErrorBag();
    }
}
