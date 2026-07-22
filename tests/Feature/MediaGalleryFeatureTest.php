<?php

use App\Enums\AchievementType;
use App\Livewire\Admin\AchievementManager;
use App\Livewire\Admin\ProjectManager;
use App\Models\Achievement;
use App\Models\Media;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function mediaGalleryFeatureEditor(string $role = 'editor'): User
{
    return User::factory()->create([
        'role' => $role,
        'email_verified_at' => now(),
    ]);
}

/** @param array<string, mixed> $attributes */
function mediaGalleryFeatureAchievement(array $attributes = []): Achievement
{
    return Achievement::query()->create(array_merge([
        'title' => 'Hackathon del Caribe',
        'type' => AchievementType::Hackathon,
        'organization' => 'Comunidad Tech Caribe',
        'result' => 'Primer lugar',
        'description' => 'Construcción colaborativa de una solución web.',
        'achieved_at' => '2026-06-14',
        'is_visible' => true,
        'sort_order' => 1,
    ], $attributes));
}

/** @param array<string, mixed> $attributes */
function mediaGalleryFeatureProject(array $attributes = []): Project
{
    return Project::query()->create(array_merge([
        'title' => 'Sistema de evidencias',
        'slug' => 'sistema-de-evidencias',
        'summary' => 'Proyecto utilizado para verificar la gestión de su galería.',
        'status' => 'published',
        'sort_order' => 1,
    ], $attributes));
}

function mediaGalleryFeatureMedia(
    Project|Achievement $owner,
    string $path,
    int $sortOrder,
    ?string $alt = null,
): Media {
    return $owner->media()->create([
        'collection' => 'gallery',
        'disk' => 'public',
        'path' => $path,
        'filename' => basename($path),
        'mime_type' => 'image/jpeg',
        'size' => 1024,
        'alt' => $alt,
        'sort_order' => $sortOrder,
    ]);
}

it('uploads several achievement gallery images with complete media metadata', function (): void {
    Storage::fake('public');

    $uploads = [
        UploadedFile::fake()->image('equipo.jpg', 1200, 800),
        UploadedFile::fake()->image('presentacion.png', 1200, 800),
        UploadedFile::fake()->image('premiacion.jpg', 1200, 800),
    ];

    Livewire::actingAs(mediaGalleryFeatureEditor('admin'))
        ->test(AchievementManager::class)
        ->set('title', 'Ganadores Hackathon Guajira')
        ->set('organization', 'Universidad de La Guajira')
        ->set('result', 'Primer lugar')
        ->set('achievedAt', '2026-06-14')
        ->set('galleryImages', $uploads)
        ->call('save')
        ->assertHasNoErrors();

    $achievement = Achievement::query()->with('media')->sole();
    $gallery = $achievement->media;

    expect($gallery)->toHaveCount(3)
        ->and($gallery->pluck('collection')->unique()->all())->toBe(['gallery'])
        ->and($gallery->pluck('disk')->unique()->all())->toBe(['public'])
        ->and($gallery->pluck('filename')->all())->toBe([
            'equipo.jpg',
            'presentacion.png',
            'premiacion.jpg',
        ])
        ->and($gallery->pluck('sort_order')->all())->toBe([1, 2, 3])
        ->and($gallery->pluck('alt')->all())->toBe([
            'Evidencia de Ganadores Hackathon Guajira 1',
            'Evidencia de Ganadores Hackathon Guajira 2',
            'Evidencia de Ganadores Hackathon Guajira 3',
        ]);

    foreach ($gallery as $media) {
        expect($media->mediable_type)->toBe(Achievement::class)
            ->and($media->mediable_id)->toBe($achievement->id)
            ->and($media->mime_type)->toStartWith('image/')
            ->and($media->size)->toBeInt()->toBeGreaterThan(0)
            ->and($media->path)->toStartWith('images/achievements/gallery/');

        Storage::disk('public')->assertExists($media->path);
    }
});

it('enforces the twelve image achievement limit across existing and pending media', function (): void {
    Storage::fake('public');
    config()->set('admin.galleries.achievements.max_items', 12);

    $achievement = mediaGalleryFeatureAchievement();

    foreach (range(1, 11) as $index) {
        mediaGalleryFeatureMedia(
            $achievement,
            "images/achievements/gallery/existing-{$index}.jpg",
            $index,
        );
    }

    Livewire::actingAs(mediaGalleryFeatureEditor())
        ->test(AchievementManager::class)
        ->call('edit', $achievement->id)
        ->set('galleryImages', [
            UploadedFile::fake()->image('pending-one.jpg', 800, 600),
            UploadedFile::fake()->image('pending-two.jpg', 800, 600),
        ])
        ->call('save')
        ->assertHasErrors(['galleryImages']);

    expect($achievement->media()->where('collection', 'gallery')->count())->toBe(11)
        ->and(Storage::disk('public')->allFiles())->toBe([]);
});

it('persists contiguous project gallery order and rejects media owned by another project', function (): void {
    $project = mediaGalleryFeatureProject();
    $otherProject = mediaGalleryFeatureProject([
        'title' => 'Proyecto ajeno',
        'slug' => 'proyecto-ajeno',
    ]);

    $first = mediaGalleryFeatureMedia($project, 'images/projects/gallery/first.jpg', 40);
    $second = mediaGalleryFeatureMedia($project, 'images/projects/gallery/second.jpg', 10);
    $third = mediaGalleryFeatureMedia($project, 'images/projects/gallery/third.jpg', 25);
    $foreign = mediaGalleryFeatureMedia($otherProject, 'images/projects/gallery/foreign.jpg', 1);

    $component = Livewire::actingAs(mediaGalleryFeatureEditor('admin'))
        ->test(ProjectManager::class)
        ->call('edit', $project->id)
        ->call('sortGalleryImage', $first->id, 0)
        ->assertHasNoErrors();

    $ordered = $project->media()
        ->where('collection', 'gallery')
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    expect($ordered->modelKeys())->toBe([$first->id, $second->id, $third->id])
        ->and($ordered->pluck('sort_order')->all())->toBe([1, 2, 3]);

    expect(fn () => $component->call('sortGalleryImage', $foreign->id, 0))
        ->toThrow(ModelNotFoundException::class);
});

it('swaps a project cover with gallery media while preserving both files', function (): void {
    Storage::fake('public');

    $coverPath = 'images/projects/cover-old.jpg';
    $galleryPath = 'images/projects/gallery/cover-new.jpg';
    Storage::disk('public')->put($coverPath, 'old project cover');
    Storage::disk('public')->put($galleryPath, 'new project cover');

    $project = mediaGalleryFeatureProject(['cover_image' => $coverPath]);
    $promoted = mediaGalleryFeatureMedia($project, $galleryPath, 1, 'Vista principal');

    Livewire::actingAs(mediaGalleryFeatureEditor())
        ->test(ProjectManager::class)
        ->call('edit', $project->id)
        ->call('useMediaAsCover', $promoted->id)
        ->assertHasNoErrors();

    expect($project->refresh()->cover_image)->toBe($galleryPath)
        ->and($project->media()->where('collection', 'gallery')->pluck('path')->all())->toBe([$coverPath]);

    $this->assertDatabaseMissing('media', ['id' => $promoted->id]);
    $this->assertDatabaseHas('media', [
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'collection' => 'gallery',
        'path' => $coverPath,
    ]);
    Storage::disk('public')->assertExists($coverPath);
    Storage::disk('public')->assertExists($galleryPath);
});

it('swaps an achievement cover with gallery media while preserving both files', function (): void {
    Storage::fake('public');

    $coverPath = 'images/achievements/cover-old.jpg';
    $galleryPath = 'images/achievements/gallery/cover-new.jpg';
    Storage::disk('public')->put($coverPath, 'old achievement cover');
    Storage::disk('public')->put($galleryPath, 'new achievement cover');

    $achievement = mediaGalleryFeatureAchievement(['image_path' => $coverPath]);
    $promoted = mediaGalleryFeatureMedia($achievement, $galleryPath, 1, 'Foto de premiación');

    Livewire::actingAs(mediaGalleryFeatureEditor('admin'))
        ->test(AchievementManager::class)
        ->call('edit', $achievement->id)
        ->call('useMediaAsCover', $promoted->id)
        ->assertHasNoErrors();

    expect($achievement->refresh()->image_path)->toBe($galleryPath)
        ->and($achievement->media()->where('collection', 'gallery')->pluck('path')->all())->toBe([$coverPath]);

    $this->assertDatabaseMissing('media', ['id' => $promoted->id]);
    $this->assertDatabaseHas('media', [
        'mediable_type' => Achievement::class,
        'mediable_id' => $achievement->id,
        'collection' => 'gallery',
        'path' => $coverPath,
    ]);
    Storage::disk('public')->assertExists($coverPath);
    Storage::disk('public')->assertExists($galleryPath);
});

it('saves trimmed alternative text for project gallery media', function (): void {
    $project = mediaGalleryFeatureProject();
    $media = mediaGalleryFeatureMedia(
        $project,
        'images/projects/gallery/dashboard.jpg',
        1,
        'Texto anterior',
    );

    Livewire::actingAs(mediaGalleryFeatureEditor())
        ->test(ProjectManager::class)
        ->call('edit', $project->id)
        ->set("mediaAlt.{$media->id}", '  Panel principal del sistema  ')
        ->call('saveMediaAlt', $media->id)
        ->assertHasNoErrors();

    expect($media->refresh()->alt)->toBe('Panel principal del sistema');
});

it('deletes an achievement gallery media record and its file only', function (): void {
    Storage::fake('public');

    $achievement = mediaGalleryFeatureAchievement();
    $deletedPath = 'images/achievements/gallery/delete-me.jpg';
    $keptPath = 'images/achievements/gallery/keep-me.jpg';
    Storage::disk('public')->put($deletedPath, 'deleted photo');
    Storage::disk('public')->put($keptPath, 'kept photo');
    $deleted = mediaGalleryFeatureMedia($achievement, $deletedPath, 1, 'Foto descartada');
    $kept = mediaGalleryFeatureMedia($achievement, $keptPath, 2, 'Foto conservada');

    Livewire::actingAs(mediaGalleryFeatureEditor())
        ->test(AchievementManager::class)
        ->call('edit', $achievement->id)
        ->call('confirmMediaDelete', $deleted->id)
        ->call('deleteMedia')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('media', ['id' => $deleted->id]);
    $this->assertDatabaseHas('media', ['id' => $kept->id]);
    Storage::disk('public')->assertMissing($deletedPath);
    Storage::disk('public')->assertExists($keptPath);
});

it('renders gallery evidence for visible achievements and excludes hidden ones from home', function (): void {
    Storage::fake('public');

    $visible = mediaGalleryFeatureAchievement([
        'title' => 'Hackathon visible',
        'sort_order' => 1,
    ]);
    mediaGalleryFeatureMedia(
        $visible,
        'images/achievements/gallery/visible.jpg',
        1,
        'Equipo celebrando el resultado visible',
    );

    $hidden = mediaGalleryFeatureAchievement([
        'title' => 'Reconocimiento oculto',
        'is_visible' => false,
        'sort_order' => 2,
    ]);
    mediaGalleryFeatureMedia(
        $hidden,
        'images/achievements/gallery/hidden.jpg',
        1,
        'Evidencia que no debe publicarse',
    );

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Hackathon visible')
        ->assertSee('Equipo celebrando el resultado visible')
        ->assertDontSee('Reconocimiento oculto')
        ->assertDontSee('Evidencia que no debe publicarse');
});
