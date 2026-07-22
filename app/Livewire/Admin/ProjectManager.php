<?php

namespace App\Livewire\Admin;

use App\Livewire\Admin\Concerns\AuthorizesContentEditors;
use App\Models\Category;
use App\Models\Media;
use App\Models\Project;
use App\Models\Skill;
use App\Services\ImageService;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Throwable;

class ProjectManager extends Component
{
    use AuthorizesContentEditors;
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $categoryFilter = '';

    public bool $showForm = false;

    public ?int $editingProjectId = null;

    public string $title = '';

    public string $categoryId = '';

    public string $summary = '';

    public string $description = '';

    public string $demoUrl = '';

    public string $repoUrl = '';

    public string $status = 'draft';

    public bool $isFeatured = false;

    public int $sortOrder = 0;

    public string $startedAt = '';

    public string $finishedAt = '';

    /** @var list<int|string> */
    public array $skillIds = [];

    public mixed $coverImage = null;

    /** @var list<mixed> */
    public array $galleryImages = [];

    public bool $confirmingDelete = false;

    public ?int $deletingProjectId = null;

    public string $deletingProjectTitle = '';

    public bool $confirmingMediaDelete = false;

    public ?int $deletingMediaId = null;

    public string $deletingMediaName = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $projectId): void
    {
        $project = Project::query()->with('skills')->findOrFail($projectId);

        $this->editingProjectId = $project->id;
        $this->title = $project->title;
        $this->categoryId = $project->category_id ? (string) $project->category_id : '';
        $this->summary = $project->summary;
        $this->description = $project->description ?? '';
        $this->demoUrl = $project->demo_url ?? '';
        $this->repoUrl = $project->repo_url ?? '';
        $this->status = $project->status;
        $this->isFeatured = $project->is_featured;
        $this->sortOrder = $project->sort_order;
        $this->startedAt = $project->started_at?->toDateString() ?? '';
        $this->finishedAt = $project->finished_at?->toDateString() ?? '';
        $this->skillIds = array_values($project->skills->modelKeys());
        $this->showForm = true;
        $this->resetErrorBag();
    }

    public function save(ImageService $imageService): void
    {
        $this->authorizeContentEditor();

        $existingGalleryCount = $this->editingProjectId
            ? Project::query()
                ->findOrFail($this->editingProjectId)
                ->media()
                ->where('collection', 'gallery')
                ->count()
            : 0;

        if ($existingGalleryCount + count($this->galleryImages) > 8) {
            $this->addError('galleryImages', 'La galería admite un máximo de 8 imágenes en total.');

            return;
        }

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:200', Rule::unique('projects', 'title')->ignore($this->editingProjectId)],
            'categoryId' => ['nullable', 'integer', 'exists:categories,id'],
            'summary' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'demoUrl' => ['nullable', 'url', 'max:255'],
            'repoUrl' => ['nullable', 'url', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'isFeatured' => ['boolean'],
            'sortOrder' => ['required', 'integer', 'min:0'],
            'startedAt' => ['nullable', 'date'],
            'finishedAt' => ['nullable', 'date', 'after_or_equal:startedAt'],
            'skillIds' => ['array'],
            'skillIds.*' => ['integer', 'exists:skills,id'],
            'coverImage' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'galleryImages' => ['array', 'max:8'],
            'galleryImages.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $project = $this->editingProjectId ? Project::findOrFail($this->editingProjectId) : new Project;
        $oldCover = $project->cover_image;
        $newCover = null;
        $newPaths = [];

        try {
            if ($this->coverImage) {
                $newCover = $imageService->store($this->coverImage, 'projects');
                $newPaths[] = $newCover;
            }

            DB::transaction(function () use ($project, $validated, $newCover, $imageService, &$newPaths): void {
                $project->fill([
                    'category_id' => filled($validated['categoryId']) ? (int) $validated['categoryId'] : null,
                    'title' => $validated['title'],
                    'summary' => $validated['summary'],
                    'description' => filled($validated['description']) ? $validated['description'] : null,
                    'demo_url' => filled($validated['demoUrl']) ? $validated['demoUrl'] : null,
                    'repo_url' => filled($validated['repoUrl']) ? $validated['repoUrl'] : null,
                    'status' => $validated['status'],
                    'is_featured' => $validated['isFeatured'],
                    'sort_order' => $validated['sortOrder'],
                    'started_at' => filled($validated['startedAt']) ? $validated['startedAt'] : null,
                    'finished_at' => filled($validated['finishedAt']) ? $validated['finishedAt'] : null,
                ]);

                if (! $project->exists || $project->isDirty('title')) {
                    $project->slug = $project->generateSlug($project->title, $project->exists ? $project->id : null);
                }

                if ($newCover) {
                    $project->cover_image = $newCover;
                }

                $project->save();
                $project->skills()->sync(array_map('intval', $validated['skillIds']));

                $sortOrder = (int) ($project->media()
                    ->where('collection', 'gallery')
                    ->max('sort_order') ?? 0);

                foreach ($this->galleryImages as $image) {
                    $path = $imageService->store($image, 'projects/gallery');
                    $newPaths[] = $path;
                    $project->media()->create([
                        'collection' => 'gallery',
                        'disk' => 'public',
                        'path' => $path,
                        'filename' => $image->getClientOriginalName(),
                        'mime_type' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'sort_order' => ++$sortOrder,
                    ]);
                }
            });
        } catch (Throwable $exception) {
            foreach (array_reverse($newPaths) as $path) {
                $imageService->delete($path);
            }

            throw $exception;
        }

        if ($newCover && $oldCover && $oldCover !== $newCover) {
            $imageService->delete($oldCover);
        }

        $this->resetForm();
        Flux::toast(variant: 'success', text: 'Proyecto guardado correctamente.');
    }

    public function confirmDelete(int $projectId): void
    {
        $project = Project::findOrFail($projectId);

        $this->deletingProjectId = $project->id;
        $this->deletingProjectTitle = $project->title;
        $this->confirmingDelete = true;
    }

    public function delete(ImageService $imageService): void
    {
        $this->authorizeContentEditor();

        $project = Project::query()->with('media')->findOrFail($this->deletingProjectId);
        $paths = $project->media->pluck('path')->push($project->cover_image)->filter()->all();

        DB::transaction(function () use ($project): void {
            $project->skills()->detach();
            $project->media()->delete();
            $project->delete();
        });

        foreach ($paths as $path) {
            $imageService->delete($path);
        }

        if ($this->editingProjectId === $this->deletingProjectId) {
            $this->resetForm();
        }

        $this->cancelDelete();
        $this->resetPage();
        Flux::toast(variant: 'success', text: 'Proyecto eliminado.');
    }

    public function confirmMediaDelete(int $mediaId): void
    {
        $media = Media::query()
            ->where('mediable_type', Project::class)
            ->where('mediable_id', $this->editingProjectId)
            ->where('collection', 'gallery')
            ->findOrFail($mediaId);

        $this->deletingMediaId = $media->id;
        $this->deletingMediaName = $media->alt ?: $media->filename;
        $this->confirmingMediaDelete = true;
    }

    public function deleteMedia(ImageService $imageService): void
    {
        $this->authorizeContentEditor();

        $media = Media::query()
            ->where('mediable_type', Project::class)
            ->where('mediable_id', $this->editingProjectId)
            ->where('collection', 'gallery')
            ->findOrFail($this->deletingMediaId);

        $path = $media->path;
        $media->delete();
        $imageService->delete($path);

        $this->cancelMediaDelete();
        Flux::toast(variant: 'success', text: 'Imagen eliminada de la galería.');
    }

    public function cancelDelete(): void
    {
        $this->reset('confirmingDelete', 'deletingProjectId', 'deletingProjectTitle');
    }

    public function cancelMediaDelete(): void
    {
        $this->reset('confirmingMediaDelete', 'deletingMediaId', 'deletingMediaName');
    }

    public function cancelForm(): void
    {
        $this->resetForm();
    }

    public function render(): View
    {
        return view('livewire.admin.project-manager', [
            'projects' => $this->projects(),
            'categories' => Category::ordered()->get(['id', 'name']),
            'skills' => Skill::ordered()->get(['id', 'name', 'group']),
            'editingProject' => $this->editingProjectId
                ? Project::query()
                    ->with(['media' => fn ($query) => $query->where('collection', 'gallery')])
                    ->find($this->editingProjectId)
                : null,
        ]);
    }

    /** @return LengthAwarePaginator<int, Project> */
    private function projects(): LengthAwarePaginator
    {
        return Project::query()
            ->with(['category', 'skills'])
            ->when($this->search !== '', fn ($query) => $query->where(function ($query): void {
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('summary', 'like', "%{$this->search}%");
            }))
            ->when($this->statusFilter !== '', fn ($query) => $query->withStatus($this->statusFilter))
            ->when($this->categoryFilter !== '', fn ($query) => $query->where('category_id', $this->categoryFilter))
            ->ordered()
            ->paginate(10);
    }

    private function resetForm(): void
    {
        $this->reset(
            'showForm',
            'editingProjectId',
            'title',
            'categoryId',
            'summary',
            'description',
            'demoUrl',
            'repoUrl',
            'isFeatured',
            'sortOrder',
            'startedAt',
            'finishedAt',
            'skillIds',
            'coverImage',
            'galleryImages',
        );
        $this->status = 'draft';
        $this->resetErrorBag();
    }
}
