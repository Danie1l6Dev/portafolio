<?php

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
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
    $publicRoute = Route::getRoutes()->getByName('portfolio.projects.index');
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
