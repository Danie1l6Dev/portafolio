<?php

use App\Http\Controllers\ProjectPageController;
use App\Livewire\Portfolio\ProjectBrowser;
use App\Models\Category;
use App\Models\Project;
use App\Models\Skill;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

function createPublicProject(array $attributes = []): Project
{
    $title = $attributes['title'] ?? 'Proyecto '.Str::random(8);

    return Project::query()->create(array_merge([
        'category_id' => null,
        'title' => $title,
        'slug' => Str::slug($title).'-'.Str::lower(Str::random(5)),
        'summary' => 'Resumen verificable del proyecto para la vista pública.',
        'description' => 'Descripción completa del proyecto y de la solución implementada.',
        'demo_url' => null,
        'repo_url' => null,
        'cover_image' => null,
        'status' => 'published',
        'is_featured' => false,
        'sort_order' => 1,
        'started_at' => '2025-01-01',
        'finished_at' => null,
    ], $attributes));
}

test('el navegador público muestra únicamente proyectos publicados', function (): void {
    $published = createPublicProject(['title' => 'Proyecto público']);
    createPublicProject(['title' => 'Borrador reservado', 'status' => 'draft']);
    createPublicProject(['title' => 'Proyecto archivado', 'status' => 'archived']);

    Livewire::test(ProjectBrowser::class)
        ->assertSee($published->title)
        ->assertDontSee('Borrador reservado')
        ->assertDontSee('Proyecto archivado')
        ->assertViewHas('projects', function (LengthAwarePaginator $projects) use ($published): bool {
            return $projects->total() === 1
                && $projects->getCollection()->modelKeys() === [$published->id];
        });
});

test('la búsqueda y los filtros se hidratan desde la URL y se combinan', function (): void {
    $web = Category::query()->create([
        'name' => 'Web',
        'slug' => 'web',
        'description' => null,
        'color' => '#0284c7',
        'sort_order' => 1,
    ]);
    $mobile = Category::query()->create([
        'name' => 'Mobile',
        'slug' => 'mobile',
        'description' => null,
        'color' => '#0f172a',
        'sort_order' => 2,
    ]);
    $laravel = Skill::query()->create([
        'name' => 'Laravel',
        'slug' => 'laravel',
        'group' => 'Backend',
        'icon' => null,
        'sort_order' => 1,
        'is_featured' => true,
    ]);
    $react = Skill::query()->create([
        'name' => 'React',
        'slug' => 'react',
        'group' => 'Frontend',
        'icon' => null,
        'sort_order' => 2,
        'is_featured' => false,
    ]);

    $matching = createPublicProject([
        'title' => 'Sistema académico',
        'summary' => 'Gestión de eventos universitarios.',
        'category_id' => $web->id,
        'sort_order' => 1,
    ]);
    $matching->skills()->attach($laravel);

    $differentCategory = createPublicProject([
        'title' => 'Sistema académico móvil',
        'category_id' => $mobile->id,
        'sort_order' => 2,
    ]);
    $differentCategory->skills()->attach($laravel);

    $differentTechnology = createPublicProject([
        'title' => 'Sistema académico React',
        'category_id' => $web->id,
        'sort_order' => 3,
    ]);
    $differentTechnology->skills()->attach($react);

    Livewire::withQueryParams([
        'buscar' => 'académico',
        'categoria' => 'web',
        'tecnologia' => 'laravel',
    ])->test(ProjectBrowser::class)
        ->assertSet('search', 'académico')
        ->assertSet('category', 'web')
        ->assertSet('technology', 'laravel')
        ->assertSee($matching->title)
        ->assertDontSee($differentCategory->title)
        ->assertDontSee($differentTechnology->title)
        ->assertViewHas('projects', fn (LengthAwarePaginator $projects): bool => $projects->total() === 1);
});

test('el listado público pagina los resultados sin cargar todo el catálogo', function (): void {
    foreach (range(1, 12) as $index) {
        createPublicProject([
            'title' => "Proyecto paginado {$index}",
            'sort_order' => $index,
        ]);
    }

    Livewire::test(ProjectBrowser::class)
        ->assertViewHas('projects', function (LengthAwarePaginator $projects): bool {
            return $projects->count() === 9
                && $projects->total() === 12
                && $projects->lastPage() === 2;
        })
        ->call('nextPage', 'pagina')
        ->assertViewHas('projects', function (LengthAwarePaginator $projects): bool {
            return $projects->currentPage() === 2
                && $projects->count() === 3;
        });
});

test('el controlador prepara el detalle publicado con relaciones y metadatos', function (): void {
    $category = Category::query()->create([
        'name' => 'Web',
        'slug' => 'web',
        'description' => null,
        'color' => '#0284c7',
        'sort_order' => 1,
    ]);
    $skill = Skill::query()->create([
        'name' => 'Laravel',
        'slug' => 'laravel',
        'group' => 'Backend',
        'icon' => null,
        'sort_order' => 1,
        'is_featured' => true,
    ]);
    $project = createPublicProject([
        'title' => 'Proyecto con detalle',
        'category_id' => $category->id,
        'cover_image' => 'projects/cover.webp',
    ]);
    $project->skills()->attach($skill);
    $project->media()->create([
        'collection' => 'gallery',
        'disk' => 'public',
        'path' => 'projects/gallery.webp',
        'filename' => 'gallery.webp',
        'mime_type' => 'image/webp',
        'size' => 1024,
        'alt' => 'Vista del proyecto',
        'sort_order' => 1,
    ]);

    $view = app(ProjectPageController::class)($project->fresh());
    $data = $view->getData();

    expect($view->name())->toBe('pages.projects.show')
        ->and($data['project']->relationLoaded('category'))->toBeTrue()
        ->and($data['project']->relationLoaded('skills'))->toBeTrue()
        ->and($data['project']->relationLoaded('media'))->toBeTrue()
        ->and($data['metaTitle'])->toBe('Proyecto con detalle — Daniel Sierra')
        ->and($data['metaDescription'])->toBe($project->summary)
        ->and($data['canonicalUrl'])->toBe(route('portfolio.projects.show', ['project' => $project->slug]))
        ->and($data['metaImage'])->toContain('/storage/projects/cover.webp')
        ->and($data['schema']['@type'])->toBe('SoftwareApplication')
        ->and($data['schema']['operatingSystem'])->toBe('Web');
});

test('el detalle responde 404 cuando el proyecto no está publicado', function (string $status): void {
    $project = createPublicProject([
        'title' => "Proyecto {$status}",
        'status' => $status,
    ]);

    expect(fn () => app(ProjectPageController::class)($project))
        ->toThrow(NotFoundHttpException::class);
})->with(['draft', 'archived']);

test('las rutas públicas de listado y detalle renderizan las vistas Laravel completas', function (): void {
    $project = createPublicProject([
        'title' => 'Proyecto renderizado',
        'slug' => 'proyecto-renderizado',
    ]);
    $project->media()->create([
        'collection' => 'gallery',
        'disk' => 'public',
        'path' => 'projects/render.webp',
        'filename' => 'render.webp',
        'mime_type' => 'image/webp',
        'size' => 2048,
        'alt' => 'Captura renderizada',
        'sort_order' => 1,
    ]);

    $this->get(route('portfolio.projects.index'))
        ->assertOk()
        ->assertSee('Proyectos de software — Daniel Sierra')
        ->assertSee('Explora proyectos de software desarrollados con Laravel', false)
        ->assertSee('Sistemas construidos para resolver problemas reales.')
        ->assertSee($project->title);

    $this->get(route('portfolio.projects.show', ['project' => $project->slug]))
        ->assertOk()
        ->assertSee($project->title)
        ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
        ->assertSee('<meta property="og:image" content="'.asset(config('portfolio.seo.default_image')).'">', false)
        ->assertSee('Sobre el proyecto')
        ->assertSee('Capturas del proyecto')
        ->assertSee('Captura renderizada')
        ->assertSee('Volver al archivo de proyectos');
});

test('las imágenes optimizadas ofrecen un srcset con la versión ligera y la detallada', function (): void {
    Storage::fake('public');

    $coverPath = app(ImageService::class)->store(UploadedFile::fake()->image('portada.png', 2400, 1600), 'projects');
    $galleryPath = app(ImageService::class)->store(UploadedFile::fake()->image('galeria.png', 2400, 1600), 'projects');

    $project = createPublicProject([
        'title' => 'Proyecto con imágenes optimizadas',
        'slug' => 'proyecto-imagenes-optimizadas',
        'cover_image' => $coverPath,
    ]);
    $project->media()->create([
        'collection' => 'gallery',
        'disk' => 'public',
        'path' => $galleryPath,
        'filename' => 'galeria.webp',
        'mime_type' => 'image/webp',
        'size' => 2048,
        'alt' => 'Vista de la galería',
        'sort_order' => 1,
    ]);

    $coverSrcset = ImageService::url($coverPath, true).' 960w, '.ImageService::url($coverPath, false).' 1920w';
    $gallerySrcset = ImageService::url($galleryPath, true).' 960w, '.ImageService::url($galleryPath, false).' 1920w';

    $this->get(route('portfolio.projects.index'))
        ->assertOk()
        ->assertSee('srcset="'.$coverSrcset.'"', false);

    $this->get(route('portfolio.projects.show', ['project' => $project->slug]))
        ->assertOk()
        ->assertSee('srcset="'.$coverSrcset.'"', false)
        ->assertSee('srcset="'.$gallerySrcset.'"', false);
});

test('los datos estructurados neutralizan contenido HTML almacenado', function (): void {
    $project = createPublicProject([
        'title' => 'Proyecto seguro',
        'slug' => 'proyecto-seguro',
        'summary' => '</script><script>window.compromised = true</script>',
    ]);

    $this->get(route('portfolio.projects.show', ['project' => $project->slug]))
        ->assertOk()
        ->assertDontSee('</script><script>window.compromised = true</script>', false)
        ->assertSee('\\u003C/script\\u003E\\u003Cscript\\u003Ewindow.compromised = true\\u003C/script\\u003E', false);
});
