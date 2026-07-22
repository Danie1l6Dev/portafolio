<?php

use App\Livewire\Admin\CategoryManager;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ExperienceManager;
use App\Livewire\Admin\MessageInbox;
use App\Livewire\Admin\ProjectManager;
use App\Livewire\Admin\SkillManager;
use App\Models\Category;
use App\Models\Experience;
use App\Models\Message;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

function portfolioEditor(string $role = 'admin'): User
{
    return User::factory()->create(['role' => $role]);
}

/** @return array<int, Message> */
function portfolioMessages(int $count): array
{
    $messages = [];

    foreach (range(1, $count) as $index) {
        $messages[] = Message::create([
            'name' => "Contacto {$index}",
            'email' => "contacto{$index}@example.com",
            'subject' => "Mensaje {$index}",
            'body' => "Contenido del mensaje {$index}.",
        ]);
    }

    return $messages;
}

it('renders every protected administrative page through Laravel and Livewire', function (): void {
    $user = portfolioEditor('editor');

    $pages = [
        'panel.dashboard' => 'Pulso editorial',
        'panel.projects' => 'Proyectos',
        'panel.categories' => 'Categorías',
        'panel.skills' => 'Habilidades',
        'panel.experiences' => 'Experiencias',
        'panel.messages' => 'Mensajes',
    ];

    foreach ($pages as $routeName => $expectedText) {
        $this->actingAs($user)
            ->get(route($routeName))
            ->assertOk()
            ->assertSee($expectedText);
    }
});

it('keeps the administrative workspace private', function (): void {
    $this->get(route('panel.dashboard'))
        ->assertRedirect(route('login'));
});

it('requires verified email addresses for both admin interfaces', function (): void {
    $user = User::factory()->unverified()->create(['role' => 'editor']);

    $this->actingAs($user)
        ->get(route('panel.dashboard'))
        ->assertRedirect(route('verification.notice'));

    $this->actingAs($user)
        ->getJson(route('admin.categories.index'))
        ->assertForbidden();
});

it('renders the complete administrative workspace with its real content', function (): void {
    $user = portfolioEditor();
    $category = Category::create(['name' => 'Web', 'slug' => 'web']);
    Project::create([
        'category_id' => $category->id,
        'title' => 'Portfolio editorial',
        'slug' => 'portfolio-editorial',
        'summary' => 'A published portfolio project.',
        'status' => 'published',
        'is_featured' => true,
    ]);
    Skill::create(['name' => 'Laravel', 'slug' => 'laravel', 'group' => 'Backend']);
    Experience::create([
        'company' => 'Acme',
        'position' => 'Developer',
        'started_at' => '2025-01-01',
        'is_current' => true,
    ]);
    Message::create([
        'name' => 'Ada',
        'email' => 'ada@example.com',
        'subject' => 'Proyecto nuevo',
        'body' => 'Hablemos del alcance.',
    ]);

    Livewire::actingAs($user)->test(Dashboard::class)
        ->assertSee('Pulso editorial')
        ->assertSee('Portfolio editorial')
        ->assertSee('Proyecto nuevo');

    Livewire::actingAs($user)->test(CategoryManager::class)->assertSee('Web')->call('create')->assertSee('Nueva categoría');
    Livewire::actingAs($user)
        ->test(SkillManager::class)
        ->assertSee('Laravel')
        ->assertDontSee('Dominio')
        ->call('create')
        ->assertSee('Nueva habilidad')
        ->assertDontSee('Nivel (1–5)');
    Livewire::actingAs($user)->test(ExperienceManager::class)->assertSee('Acme')->call('create')->assertSee('Nueva experiencia');
    Livewire::actingAs($user)->test(MessageInbox::class)->assertSee('Proyecto nuevo');
    Livewire::actingAs($user)->test(ProjectManager::class)->assertSee('Portfolio editorial')->call('create')->assertSee('Nuevo proyecto');
});

it('requires an editor role again inside mutating component actions', function (): void {
    Livewire::test(CategoryManager::class)
        ->set('name', 'Unauthorized')
        ->set('sortOrder', 1)
        ->call('save')
        ->assertForbidden();
});

it('rejects unverified editors inside direct Livewire actions', function (): void {
    $user = User::factory()->unverified()->create(['role' => 'editor']);

    Livewire::actingAs($user)
        ->test(CategoryManager::class)
        ->set('name', 'Categoría no autorizada')
        ->set('sortOrder', 1)
        ->call('save')
        ->assertForbidden();

    $this->assertDatabaseMissing('categories', ['name' => 'Categoría no autorizada']);
});

it('creates and updates categories while protecting categories in use', function (): void {
    $user = portfolioEditor('editor');

    Livewire::actingAs($user)
        ->test(CategoryManager::class)
        ->set('name', 'Aplicaciones web')
        ->set('description', 'Productos publicados en la web.')
        ->set('color', '#3b82f6')
        ->set('sortOrder', 4)
        ->call('save')
        ->assertHasNoErrors();

    $category = Category::where('slug', 'aplicaciones-web')->firstOrFail();

    expect($category->color)->toBe('#3B82F6');

    Project::create([
        'category_id' => $category->id,
        'title' => 'Attached project',
        'slug' => 'attached-project',
        'summary' => 'This project keeps the category in use.',
    ]);

    Livewire::actingAs($user)
        ->test(CategoryManager::class)
        ->call('confirmDelete', $category->id)
        ->call('delete')
        ->assertHasErrors('delete');

    expect($category->fresh())->not->toBeNull();
});

it('manages skill metadata and detaches deleted skills from projects', function (): void {
    $user = portfolioEditor();

    Livewire::actingAs($user)
        ->test(SkillManager::class)
        ->set('name', 'Livewire')
        ->set('group', 'Backend')
        ->set('icon', 'si:livewire')
        ->set('sortOrder', 12)
        ->set('isFeatured', true)
        ->call('save')
        ->assertHasNoErrors();

    $skill = Skill::where('slug', 'livewire')->firstOrFail();
    expect($skill->getAttributes())->not->toHaveKey('level');

    $project = Project::create([
        'title' => 'Reactive project',
        'slug' => 'reactive-project',
        'summary' => 'A project associated with Livewire.',
    ]);
    $project->skills()->attach($skill);

    Livewire::actingAs($user)
        ->test(SkillManager::class)
        ->call('confirmDelete', $skill->id)
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    $this->assertDatabaseMissing('project_skill', ['skill_id' => $skill->id]);
});

it('creates projects with a cover, gallery and synchronized skills', function (): void {
    Storage::fake('public');

    $user = portfolioEditor();
    $category = Category::create(['name' => 'Backend', 'slug' => 'backend']);
    $skill = Skill::create(['name' => 'PHP', 'slug' => 'php', 'group' => 'Backend']);

    Livewire::actingAs($user)
        ->test(ProjectManager::class)
        ->set('title', 'Gestor Laravel')
        ->set('categoryId', (string) $category->id)
        ->set('summary', 'Panel administrativo construido completamente con Laravel.')
        ->set('description', 'Una descripción más extensa para el detalle.')
        ->set('status', 'published')
        ->set('isFeatured', true)
        ->set('sortOrder', 1)
        ->set('skillIds', [$skill->id])
        ->set('coverImage', UploadedFile::fake()->image('cover.jpg', 1200, 800))
        ->set('galleryImages', [UploadedFile::fake()->image('gallery.png', 900, 600)])
        ->call('save')
        ->assertHasNoErrors();

    $project = Project::query()->with(['skills', 'media'])->where('slug', 'gestor-laravel')->firstOrFail();

    expect($project->skills)->toHaveCount(1)
        ->and($project->media)->toHaveCount(1)
        ->and($project->status)->toBe('published');

    Storage::disk('public')->assertExists($project->cover_image);
    Storage::disk('public')->assertExists($project->media->first()->path);
});

it('enforces the gallery limit across existing and newly uploaded images', function (): void {
    Storage::fake('public');

    $user = portfolioEditor();
    $project = Project::create([
        'title' => 'Galería completa',
        'slug' => 'galeria-completa',
        'summary' => 'Proyecto con ocho imágenes existentes.',
        'status' => 'published',
    ]);

    foreach (range(1, 8) as $index) {
        $project->media()->create([
            'collection' => 'gallery',
            'disk' => 'public',
            'path' => "images/projects/gallery/{$index}.jpg",
            'filename' => "{$index}.jpg",
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'sort_order' => $index,
        ]);
    }

    Livewire::actingAs($user)
        ->test(ProjectManager::class)
        ->call('edit', $project->id)
        ->set('galleryImages', [UploadedFile::fake()->image('nueva.jpg', 900, 600)])
        ->call('save')
        ->assertHasErrors(['galleryImages']);

    expect($project->media()->where('collection', 'gallery')->count())->toBe(8);
});

it('shows validation instead of crashing when a cover is not previewable', function (): void {
    Storage::fake('public');

    Livewire::actingAs(portfolioEditor())
        ->test(ProjectManager::class)
        ->set('title', 'Portada inválida')
        ->set('summary', 'El archivo debe rechazarse mediante validación.')
        ->set('coverImage', UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf'))
        ->call('save')
        ->assertHasErrors(['coverImage']);
});

it('compensates newly stored project files when persistence fails', function (): void {
    Storage::fake('public');

    $user = portfolioEditor();
    $imageService = Mockery::mock(ImageService::class);
    $imageService->shouldReceive('store')
        ->once()
        ->with(Mockery::type(UploadedFile::class), 'projects')
        ->andReturn('images/projects/new-cover.jpg');
    $imageService->shouldReceive('store')
        ->once()
        ->with(Mockery::type(UploadedFile::class), 'projects/gallery')
        ->andThrow(new RuntimeException('Simulated gallery failure.'));
    $imageService->shouldReceive('delete')
        ->once()
        ->with('images/projects/new-cover.jpg');
    app()->instance(ImageService::class, $imageService);

    expect(fn () => Livewire::actingAs($user)
        ->test(ProjectManager::class)
        ->set('title', 'Proyecto atómico')
        ->set('summary', 'Debe revertir base de datos y archivos.')
        ->set('coverImage', UploadedFile::fake()->image('cover.jpg', 1200, 800))
        ->set('galleryImages', [UploadedFile::fake()->image('gallery.jpg', 900, 600)])
        ->call('save'))
        ->toThrow(RuntimeException::class, 'Simulated gallery failure.');

    $this->assertDatabaseMissing('projects', ['title' => 'Proyecto atómico']);
});

it('keeps non-gallery project media out of gallery management', function (): void {
    $user = portfolioEditor();
    $project = Project::create([
        'title' => 'Proyecto con documento',
        'slug' => 'proyecto-con-documento',
        'summary' => 'Incluye un medio de otra colección.',
    ]);
    $document = $project->media()->create([
        'collection' => 'documents',
        'disk' => 'public',
        'path' => 'documents/specification.pdf',
        'filename' => 'specification.pdf',
        'mime_type' => 'application/pdf',
        'size' => 2048,
        'sort_order' => 1,
    ]);

    $component = Livewire::actingAs($user)
        ->test(ProjectManager::class)
        ->call('edit', $project->id)
        ->assertViewHas('editingProject', fn (Project $editingProject): bool => $editingProject->media->isEmpty());

    expect(fn () => $component->call('confirmMediaDelete', $document->id))
        ->toThrow(ModelNotFoundException::class);
});

it('creates current experience entries with a managed logo', function (): void {
    Storage::fake('public');

    $user = portfolioEditor();

    Livewire::actingAs($user)
        ->test(ExperienceManager::class)
        ->set('company', 'Universidad de La Guajira')
        ->set('position', 'Desarrollador')
        ->set('location', 'Riohacha')
        ->set('startedAt', '2025-01-01')
        ->set('finishedAt', '2025-12-01')
        ->set('isCurrent', true)
        ->set('companyLogo', UploadedFile::fake()->image('logo.png', 300, 300))
        ->call('save')
        ->assertHasNoErrors();

    $experience = Experience::firstOrFail();

    expect($experience->is_current)->toBeTrue()
        ->and($experience->finished_at)->toBeNull();
    Storage::disk('public')->assertExists($experience->company_logo);
});

it('requires an end date for experiences that are not current', function (): void {
    $user = portfolioEditor();

    Livewire::actingAs($user)
        ->test(ExperienceManager::class)
        ->set('company', 'Acme')
        ->set('position', 'Developer')
        ->set('startedAt', '2025-01-01')
        ->set('finishedAt', '')
        ->set('isCurrent', false)
        ->call('save')
        ->assertHasErrors(['finishedAt' => 'required']);

    $this->assertDatabaseCount('experiences', 0);
});

it('clears the end date error when an experience becomes current', function (): void {
    $user = portfolioEditor();

    Livewire::actingAs($user)
        ->test(ExperienceManager::class)
        ->set('company', 'Acme')
        ->set('position', 'Developer')
        ->set('startedAt', '2025-01-01')
        ->set('finishedAt', '')
        ->set('isCurrent', false)
        ->call('save')
        ->assertHasErrors(['finishedAt' => 'required'])
        ->set('isCurrent', true)
        ->assertSet('finishedAt', '')
        ->assertHasNoErrors('finishedAt');
});

it('resets inbox pagination when search or status filters change', function (): void {
    $user = portfolioEditor();
    portfolioMessages(16);

    Livewire::actingAs($user)
        ->test(MessageInbox::class)
        ->call('gotoPage', 2)
        ->assertSet('paginators.page', 2)
        ->set('search', 'Contacto')
        ->assertSet('paginators.page', 1)
        ->call('gotoPage', 2)
        ->set('filter', 'unread')
        ->assertSet('paginators.page', 1);
});

it('clamps inbox pagination after selecting the only unread message on page two', function (): void {
    $user = portfolioEditor();
    $messages = portfolioMessages(16);

    Livewire::actingAs($user)
        ->test(MessageInbox::class)
        ->set('filter', 'unread')
        ->call('gotoPage', 2)
        ->assertSet('paginators.page', 2)
        ->call('selectMessage', $messages[0]->id)
        ->assertSet('selectedMessageId', $messages[0]->id)
        ->assertSet('paginators.page', 1);
});

it('clamps inbox pagination after marking the only unread message on page two as read', function (): void {
    $user = portfolioEditor();
    $messages = portfolioMessages(16);

    Livewire::actingAs($user)
        ->test(MessageInbox::class)
        ->set('filter', 'unread')
        ->call('gotoPage', 2)
        ->assertSet('paginators.page', 2)
        ->call('markAsRead', $messages[0]->id)
        ->assertSet('paginators.page', 1);
});

it('clamps inbox pagination after marking every unread message as read', function (): void {
    $user = portfolioEditor();
    portfolioMessages(16);

    Livewire::actingAs($user)
        ->test(MessageInbox::class)
        ->set('filter', 'unread')
        ->call('gotoPage', 2)
        ->assertSet('paginators.page', 2)
        ->call('markAllAsRead')
        ->assertSet('paginators.page', 1);
});

it('clamps inbox pagination after deleting the only message on page two', function (): void {
    $user = portfolioEditor();
    $messages = portfolioMessages(16);

    Livewire::actingAs($user)
        ->test(MessageInbox::class)
        ->call('gotoPage', 2)
        ->assertSet('paginators.page', 2)
        ->call('confirmDelete', $messages[0]->id)
        ->call('delete')
        ->assertSet('paginators.page', 1);
});

it('reads, clears and deletes contact messages from the inbox', function (): void {
    $user = portfolioEditor();
    $first = Message::create([
        'name' => 'Grace',
        'email' => 'grace@example.com',
        'subject' => 'Primero',
        'body' => 'Mensaje uno.',
    ]);
    $second = Message::create([
        'name' => 'Linus',
        'email' => 'linus@example.com',
        'subject' => 'Segundo',
        'body' => 'Mensaje dos.',
    ]);

    Livewire::actingAs($user)
        ->test(MessageInbox::class)
        ->call('selectMessage', $first->id)
        ->assertSet('selectedMessageId', $first->id)
        ->call('markAllAsRead')
        ->call('confirmDelete', $second->id)
        ->call('delete')
        ->assertHasNoErrors();

    expect($first->fresh()->is_read)->toBeTrue();
    $this->assertDatabaseMissing('messages', ['id' => $second->id]);
});
