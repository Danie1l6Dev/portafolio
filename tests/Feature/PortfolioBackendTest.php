<?php

use App\Models\Category;
use App\Models\Experience;
use App\Models\Project;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('returns only published projects from the Laravel application', function (): void {
    $category = Category::create([
        'name' => 'Web',
        'slug' => 'web',
        'sort_order' => 1,
    ]);

    Project::create([
        'category_id' => $category->id,
        'title' => 'Published project',
        'slug' => 'published-project',
        'summary' => 'Visible summary',
        'status' => 'published',
        'sort_order' => 1,
    ]);

    Project::create([
        'category_id' => $category->id,
        'title' => 'Draft project',
        'slug' => 'draft-project',
        'summary' => 'Hidden summary',
        'status' => 'draft',
        'sort_order' => 2,
    ]);

    $this->getJson('/projects')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Published project')
        ->assertJsonMissing(['title' => 'Draft project']);
});

it('returns the full description from the compatible project detail endpoint', function (): void {
    $project = Project::create([
        'title' => 'Detailed project',
        'slug' => 'detailed-project',
        'summary' => 'Visible summary',
        'description' => 'Full implementation context.',
        'status' => 'published',
    ]);

    $this->getJson(route('backend.projects.show', ['project' => $project]))
        ->assertOk()
        ->assertJsonPath('data.slug', 'detailed-project')
        ->assertJsonPath('data.description', 'Full implementation context.');
});

it('stores contact messages in the Laravel application', function (): void {
    $this->postJson('/contact', [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'subject' => 'Portfolio inquiry',
        'body' => 'I would like to discuss a project.',
    ])->assertCreated()
        ->assertJsonPath('data.id', 1);

    $this->assertDatabaseHas('messages', [
        'email' => 'ada@example.com',
        'subject' => 'Portfolio inquiry',
        'is_read' => false,
        'ip_address' => '127.0.0.1',
    ]);
});

it('uses web sessions for portfolio and administrative routes', function (): void {
    $publicRoute = Route::getRoutes()->getByName('backend.projects.index');
    $adminRoute = Route::getRoutes()->getByName('admin.categories.index');

    expect($publicRoute)->not->toBeNull()
        ->and($publicRoute?->gatherMiddleware())->toContain('web')
        ->and($adminRoute)->not->toBeNull()
        ->and($adminRoute?->gatherMiddleware())->toContain('web', 'auth', 'content-editor');

    $this->getJson('/admin/categories')->assertUnauthorized();
});

it('does not expose public user registration', function (): void {
    expect(Route::has('register'))->toBeFalse();

    $this->get('/register')->assertNotFound();
});

it('creates categories for authenticated administrators', function (): void {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->postJson('/admin/categories', [
            'name' => 'Laravel Backend',
            'description' => 'Application services',
            'color' => '#3B82F6',
            'sort_order' => 2,
        ])->assertCreated()
        ->assertJsonPath('data.slug', 'laravel-backend');

    $this->assertDatabaseHas('categories', [
        'name' => 'Laravel Backend',
        'slug' => 'laravel-backend',
    ]);
});

it('allows editors to manage portfolio content', function (): void {
    $user = User::factory()->create(['role' => 'editor']);

    $this->actingAs($user)
        ->postJson('/admin/categories', [
            'name' => 'Editorial',
            'sort_order' => 1,
        ])->assertCreated();
});

it('uploads project media to the public disk', function (): void {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'admin']);
    $project = Project::create([
        'title' => 'Media project',
        'slug' => 'media-project',
        'summary' => 'Project with gallery',
        'status' => 'published',
    ]);

    $response = $this->actingAs($user)
        ->post('/admin/projects/'.$project->id.'/media', [
            'images' => [UploadedFile::fake()->image('gallery.jpg', 800, 600)],
        ]);

    $response->assertCreated()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.is_image', true);

    Storage::disk('public')->assertExists($response->json('data.0.path'));

    $this->assertDatabaseHas('media', [
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'collection' => 'gallery',
    ]);
});

it('requires an end date for completed experiences in the compatible endpoint', function (): void {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user)
        ->postJson(route('admin.experiences.store'), [
            'company' => 'Acme',
            'position' => 'Desarrollador',
            'started_at' => '2025-01-01',
            'is_current' => false,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('finished_at');
});

it('validates effective experience dates during partial updates', function (): void {
    $user = User::factory()->create(['role' => 'admin']);
    $experience = Experience::create([
        'company' => 'Acme',
        'position' => 'Desarrollador',
        'started_at' => '2025-03-01',
        'finished_at' => '2025-12-01',
        'is_current' => false,
    ]);

    $this->actingAs($user)
        ->patchJson(route('admin.experiences.update', $experience), [
            'finished_at' => '2025-02-01',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('finished_at');

    expect($experience->fresh()->finished_at?->toDateString())->toBe('2025-12-01');
});

it('validates effective project dates during partial updates', function (): void {
    $user = User::factory()->create(['role' => 'admin']);
    $project = Project::create([
        'title' => 'Proyecto fechado',
        'slug' => 'proyecto-fechado',
        'summary' => 'Proyecto con un periodo verificable.',
        'started_at' => '2025-03-01',
        'finished_at' => '2025-12-01',
    ]);

    $this->actingAs($user)
        ->patchJson(route('admin.projects.update', $project), [
            'finished_at' => '2025-02-01',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('finished_at');

    expect($project->fresh()->finished_at?->toDateString())->toBe('2025-12-01');
});

it('rejects non-image project media and a ninth gallery image', function (): void {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'admin']);
    $project = Project::create([
        'title' => 'Galería segura',
        'slug' => 'galeria-segura',
        'summary' => 'Proyecto con límites de galería.',
    ]);

    $this->actingAs($user)
        ->withHeader('Accept', 'application/json')
        ->post(route('admin.projects.media.store', $project), [
            'images' => [UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf')],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('images.0');

    foreach (range(1, 8) as $index) {
        $project->media()->create([
            'collection' => 'gallery',
            'disk' => 'public',
            'path' => "images/projects/gallery/{$index}.jpg",
            'filename' => "{$index}.jpg",
            'mime_type' => 'image/jpeg',
            'size' => 100,
            'sort_order' => $index,
        ]);
    }

    $this->actingAs($user)
        ->withHeader('Accept', 'application/json')
        ->post(route('admin.projects.media.store', $project), [
            'images' => [UploadedFile::fake()->image('novena.jpg')],
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('images');

    expect($project->media()->inCollection('gallery')->count())->toBe(8);
});

it('rolls back compatible project creation and compensates files when an upload fails', function (): void {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'admin']);
    $imageService = Mockery::mock(ImageService::class);
    $imageService->shouldReceive('store')
        ->once()
        ->with(Mockery::type(UploadedFile::class), 'projects')
        ->andReturn('images/projects/new-cover.jpg');
    $imageService->shouldReceive('store')
        ->once()
        ->with(Mockery::type(UploadedFile::class), 'projects/gallery')
        ->andThrow(new RuntimeException('Simulated compatible gallery failure.'));
    $imageService->shouldReceive('delete')
        ->once()
        ->with('images/projects/new-cover.jpg');
    app()->instance(ImageService::class, $imageService);

    $this->withoutExceptionHandling();

    expect(fn () => $this->actingAs($user)->post(route('admin.projects.store'), [
        'title' => 'Proyecto compatible atómico',
        'summary' => 'No debe persistir parcialmente.',
        'cover_image' => UploadedFile::fake()->image('cover.jpg'),
        'gallery_images' => [UploadedFile::fake()->image('gallery.jpg')],
    ]))->toThrow(RuntimeException::class, 'Simulated compatible gallery failure.');

    $this->assertDatabaseMissing('projects', ['title' => 'Proyecto compatible atómico']);
    $this->assertDatabaseCount('media', 0);
});
