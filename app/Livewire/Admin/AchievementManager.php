<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Enums\AchievementType;
use App\Livewire\Admin\Concerns\AuthorizesContentEditors;
use App\Models\Achievement;
use App\Services\DocumentService;
use App\Services\ImageService;
use App\Services\MediaGalleryService;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Throwable;

final class AchievementManager extends Component
{
    use AuthorizesContentEditors;
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public string $typeFilter = '';

    public string $visibilityFilter = 'all';

    public bool $showForm = false;

    public ?int $editingAchievementId = null;

    public string $title = '';

    public string $type = 'hackathon';

    public string $organization = '';

    public string $result = '';

    public string $role = '';

    public string $description = '';

    public string $achievedAt = '';

    public string $externalUrl = '';

    public mixed $image = null;

    public mixed $certificate = null;

    /** @var list<mixed> */
    public array $galleryImages = [];

    /** @var array<int, string> */
    public array $mediaAlt = [];

    public bool $isFeatured = false;

    public bool $isVisible = true;

    public int $sortOrder = 0;

    public bool $removeCurrentImage = false;

    public bool $removeCurrentCertificate = false;

    public bool $confirmingDelete = false;

    public ?int $deletingAchievementId = null;

    public string $deletingAchievementTitle = '';

    public bool $confirmingMediaDelete = false;

    public ?int $deletingMediaId = null;

    public string $deletingMediaName = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedVisibilityFilter(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $achievementId): void
    {
        $achievement = Achievement::query()
            ->with(['media' => fn ($query) => $query->where('collection', MediaGalleryService::COLLECTION)])
            ->findOrFail($achievementId);

        $this->editingAchievementId = $achievement->id;
        $this->title = $achievement->title;
        $this->type = $achievement->type->value;
        $this->organization = $achievement->organization;
        $this->result = $achievement->result ?? '';
        $this->role = $achievement->role ?? '';
        $this->description = $achievement->description ?? '';
        $this->achievedAt = $achievement->achieved_at->toDateString();
        $this->externalUrl = $achievement->external_url ?? '';
        $this->isFeatured = $achievement->is_featured;
        $this->isVisible = $achievement->is_visible;
        $this->sortOrder = $achievement->sort_order;
        $this->mediaAlt = $achievement->media
            ->mapWithKeys(static fn ($media): array => [$media->id => (string) ($media->alt ?? '')])
            ->all();
        $this->showForm = true;
        $this->resetErrorBag();
    }

    public function markImageForRemoval(): void
    {
        $this->image = null;
        $this->removeCurrentImage = true;
        $this->resetValidation('image');
    }

    public function markCertificateForRemoval(): void
    {
        $this->certificate = null;
        $this->removeCurrentCertificate = true;
        $this->resetValidation('certificate');
    }

    public function save(
        ImageService $imageService,
        DocumentService $documentService,
        MediaGalleryService $galleryService,
    ): void {
        $this->authorizeContentEditor();

        $galleryLimit = (int) config('admin.galleries.achievements.max_items', 12);
        $galleryFileLimit = (int) config('admin.galleries.achievements.max_file_kilobytes', 3072);
        $existingGalleryCount = $this->editingAchievementId
            ? Achievement::query()
                ->findOrFail($this->editingAchievementId)
                ->media()
                ->where('collection', MediaGalleryService::COLLECTION)
                ->count()
            : 0;

        if ($existingGalleryCount + count($this->galleryImages) > $galleryLimit) {
            $this->addError('galleryImages', "La galería admite un máximo de {$galleryLimit} imágenes en total.");

            return;
        }

        $validated = $this->validate([
            'title' => ['required', 'string', 'max:180'],
            'type' => ['required', Rule::enum(AchievementType::class)],
            'organization' => ['required', 'string', 'max:180'],
            'result' => ['nullable', 'string', 'max:150'],
            'role' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string', 'max:3000'],
            'achievedAt' => ['required', 'date', 'before_or_equal:today'],
            'externalUrl' => ['nullable', 'url:http,https', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'galleryImages' => ['array', "max:{$galleryLimit}"],
            'galleryImages.*' => ['image', 'mimes:jpg,jpeg,png,webp', "max:{$galleryFileLimit}"],
            'certificate' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'isFeatured' => ['boolean'],
            'isVisible' => ['boolean'],
            'sortOrder' => ['required', 'integer', 'min:0', 'max:65535'],
        ], [
            'achievedAt.before_or_equal' => 'La fecha del logro no puede estar en el futuro.',
            'image.image' => 'La portada debe ser una imagen válida.',
            'image.mimes' => 'La portada debe ser JPG, PNG o WebP.',
            'image.max' => 'La portada no puede superar 3 MB.',
            'galleryImages.*.image' => 'Cada archivo de la galería debe ser una imagen válida.',
            'galleryImages.*.mimes' => 'Las fotos deben ser JPG, PNG o WebP.',
            'galleryImages.*.max' => 'Cada foto no puede superar 3 MB.',
            'certificate.mimes' => 'El certificado debe ser un archivo PDF.',
            'certificate.max' => 'El certificado no puede superar 5 MB.',
        ]);

        $achievement = $this->editingAchievementId
            ? Achievement::query()->findOrFail($this->editingAchievementId)
            : new Achievement;
        $oldImagePath = $achievement->image_path;
        $oldCertificatePath = $achievement->certificate_path;
        $newImagePath = null;
        $newCertificatePath = null;
        $newGalleryPaths = [];

        $data = [
            'title' => $validated['title'],
            'type' => $validated['type'],
            'organization' => $validated['organization'],
            'result' => filled($validated['result']) ? $validated['result'] : null,
            'role' => filled($validated['role']) ? $validated['role'] : null,
            'description' => filled($validated['description']) ? $validated['description'] : null,
            'achieved_at' => $validated['achievedAt'],
            'external_url' => filled($validated['externalUrl']) ? $validated['externalUrl'] : null,
            'is_featured' => $validated['isFeatured'],
            'is_visible' => $validated['isVisible'],
            'sort_order' => $validated['sortOrder'],
        ];

        if ($this->removeCurrentImage) {
            $data['image_path'] = null;
        }

        if ($this->removeCurrentCertificate) {
            $data['certificate_path'] = null;
        }

        try {
            if ($this->image) {
                $newImagePath = $imageService->store($this->image, 'achievements');
                $data['image_path'] = $newImagePath;
            }

            if ($this->certificate) {
                $newCertificatePath = $documentService->storePdf($this->certificate, 'achievements');
                $data['certificate_path'] = $newCertificatePath;
            }

            DB::transaction(function () use ($achievement, $data, $galleryService, &$newGalleryPaths): void {
                $achievement->fill($data)->save();

                if ($this->galleryImages !== []) {
                    $gallery = $galleryService->append(
                        $achievement,
                        $this->galleryImages,
                        'achievements/gallery',
                        "Evidencia de {$achievement->title}",
                    );
                    $newGalleryPaths = $gallery['paths'];
                }
            });
        } catch (Throwable $exception) {
            $imageService->delete($newImagePath);
            $documentService->delete($newCertificatePath);
            $galleryService->deletePathsIfUnreferenced($newGalleryPaths);

            throw $exception;
        }

        if ($oldImagePath && $oldImagePath !== $achievement->image_path) {
            $galleryService->deletePathsIfUnreferenced([$oldImagePath]);
        }

        if ($oldCertificatePath && $oldCertificatePath !== $achievement->certificate_path) {
            $documentService->delete($oldCertificatePath);
        }

        $this->resetForm();
        Flux::toast(variant: 'success', text: 'Logro guardado correctamente.');
    }

    public function confirmDelete(int $achievementId): void
    {
        $achievement = Achievement::query()->findOrFail($achievementId);

        $this->deletingAchievementId = $achievement->id;
        $this->deletingAchievementTitle = $achievement->title;
        $this->confirmingDelete = true;
    }

    public function delete(DocumentService $documentService, MediaGalleryService $galleryService): void
    {
        $this->authorizeContentEditor();

        $achievement = Achievement::query()->with('media')->findOrFail($this->deletingAchievementId);
        $imagePath = $achievement->image_path;
        $certificatePath = $achievement->certificate_path;
        $galleryPaths = $achievement->media
            ->pluck('path')
            ->map(static fn (mixed $path): string => (string) $path)
            ->values()
            ->all();

        DB::transaction(function () use ($achievement): void {
            $achievement->media()->delete();
            $achievement->delete();
        });

        $galleryService->deletePathsIfUnreferenced([$imagePath, ...$galleryPaths]);
        $documentService->delete($certificatePath);

        $this->cancelDelete();
        $this->resetPage();
        Flux::toast(variant: 'success', text: 'Logro eliminado.');
    }

    public function confirmMediaDelete(int $mediaId): void
    {
        $media = $this->editingAchievement()
            ->media()
            ->where('collection', MediaGalleryService::COLLECTION)
            ->findOrFail($mediaId);

        $this->deletingMediaId = $media->id;
        $this->deletingMediaName = $media->alt ?: $media->filename;
        $this->confirmingMediaDelete = true;
    }

    public function deleteMedia(MediaGalleryService $galleryService): void
    {
        $this->authorizeContentEditor();
        $galleryService->delete($this->editingAchievement(), (int) $this->deletingMediaId);

        $this->cancelMediaDelete();
        Flux::toast(variant: 'success', text: 'Foto eliminada de la galería.');
    }

    public function sortGalleryImage(int $mediaId, int $position, MediaGalleryService $galleryService): void
    {
        $this->authorizeContentEditor();
        $galleryService->move($this->editingAchievement(), $mediaId, $position);
    }

    public function useMediaAsCover(int $mediaId, MediaGalleryService $galleryService): void
    {
        $this->authorizeContentEditor();
        $achievement = $this->editingAchievement();

        $galleryService->promoteToCover(
            $achievement,
            $mediaId,
            'image_path',
            "Portada anterior de {$achievement->title}",
        );

        Flux::toast(variant: 'success', text: 'La foto ahora es la portada del logro.');
    }

    public function saveMediaAlt(int $mediaId, MediaGalleryService $galleryService): void
    {
        $this->authorizeContentEditor();

        $validated = Validator::make(
            ['alt' => $this->mediaAlt[$mediaId] ?? null],
            ['alt' => ['nullable', 'string', 'max:255']],
        )->validate();

        $galleryService->updateAlt($this->editingAchievement(), $mediaId, $validated['alt'] ?? null);
        Flux::toast(variant: 'success', text: 'Texto alternativo actualizado.');
    }

    public function removePendingGalleryImage(int $index): void
    {
        if (! array_key_exists($index, $this->galleryImages)) {
            return;
        }

        array_splice($this->galleryImages, $index, 1);
        $this->resetValidation('galleryImages');
    }

    public function cancelDelete(): void
    {
        $this->reset('confirmingDelete', 'deletingAchievementId', 'deletingAchievementTitle');
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
        return view('livewire.admin.achievement-manager', [
            'achievements' => $this->achievements(),
            'types' => AchievementType::cases(),
            'editingAchievement' => $this->editingAchievementId
                ? Achievement::query()
                    ->with(['media' => fn ($query) => $query->where('collection', MediaGalleryService::COLLECTION)])
                    ->find($this->editingAchievementId)
                : null,
            'galleryLimit' => (int) config('admin.galleries.achievements.max_items', 12),
        ]);
    }

    /** @return LengthAwarePaginator<int, Achievement> */
    private function achievements(): LengthAwarePaginator
    {
        return Achievement::query()
            ->with(['media' => fn ($query) => $query->where('collection', MediaGalleryService::COLLECTION)])
            ->when($this->search !== '', fn (Builder $query): Builder => $query->where(function (Builder $query): void {
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhere('organization', 'like', "%{$this->search}%")
                    ->orWhere('result', 'like', "%{$this->search}%");
            }))
            ->when($this->typeFilter !== '', fn (Builder $query): Builder => $query->where('type', $this->typeFilter))
            ->when($this->visibilityFilter === 'visible', fn (Builder $query): Builder => $query->visible())
            ->when($this->visibilityFilter === 'hidden', fn (Builder $query): Builder => $query->where('is_visible', false))
            ->ordered()
            ->paginate(10);
    }

    private function resetForm(): void
    {
        $this->reset(
            'showForm',
            'editingAchievementId',
            'title',
            'organization',
            'result',
            'role',
            'description',
            'achievedAt',
            'externalUrl',
            'image',
            'certificate',
            'galleryImages',
            'mediaAlt',
            'isFeatured',
            'sortOrder',
            'removeCurrentImage',
            'removeCurrentCertificate',
        );
        $this->type = AchievementType::Hackathon->value;
        $this->isVisible = true;
        $this->resetErrorBag();
    }

    private function editingAchievement(): Achievement
    {
        return Achievement::query()->findOrFail($this->editingAchievementId);
    }
}
