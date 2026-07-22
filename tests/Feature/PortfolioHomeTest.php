<?php

use App\Models\Experience;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Support\Facades\Storage;

function homeProject(array $attributes = []): Project
{
    return Project::query()->create(array_merge([
        'title' => 'Sistema publicado',
        'slug' => 'sistema-publicado',
        'summary' => 'Una solución verificable construida con Laravel.',
        'description' => 'Contexto completo de la solución.',
        'status' => 'published',
        'is_featured' => true,
        'sort_order' => 1,
        'started_at' => '2025-01-01',
    ], $attributes));
}

it('renders the factual one-page portfolio with SEO and security headers', function (): void {
    $published = homeProject();
    homeProject([
        'title' => 'Borrador privado',
        'slug' => 'borrador-privado',
        'status' => 'draft',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('id="inicio"', false)
        ->assertSee('id="sobre-mi"', false)
        ->assertSee('id="proyectos"', false)
        ->assertSee('id="habilidades"', false)
        ->assertSee('id="contacto"', false)
        ->assertSee('data-portfolio-mark', false)
        ->assertSee('data-service-panel', false)
        ->assertSee('id="hero-services-title"', false)
        ->assertSee('Sistemas de gestión')
        ->assertSee('Procesos operativos')
        ->assertSee('Aplicaciones Laravel completas')
        ->assertDontSee('Sistema / portafolio')
        ->assertDontSee('id="experiencia"', false)
        ->assertSee($published->title)
        ->assertDontSee('Borrador privado')
        ->assertSee('data-featured-carousel', false)
        ->assertSee('data-featured-project="sistema-publicado"', false)
        ->assertDontSee('data-carousel-control=', false)
        ->assertSee('application/ld+json', false)
        ->assertSee('ProfilePage')
        ->assertSee('<link rel="canonical" href="'.route('home').'">', false)
        ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});

it('renders the configured technology icon inside each skill card', function (): void {
    Skill::query()->create([
        'name' => 'Laravel',
        'slug' => 'laravel',
        'group' => 'Backend',
        'icon' => 'si:laravel',
        'is_featured' => true,
        'sort_order' => 1,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('data-skill-icon="laravel"', false)
        ->assertSee('https://cdn.simpleicons.org/laravel', false)
        ->assertDontSee('Nivel 5 de 5');
});

it('renders technology icons inside the featured-project carousel', function (): void {
    $project = homeProject();
    $skill = Skill::query()->create([
        'name' => 'Laravel',
        'slug' => 'laravel',
        'group' => 'Backend',
        'icon' => 'si:laravel',
        'is_featured' => true,
        'sort_order' => 1,
    ]);

    $project->skills()->attach($skill);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('data-carousel-skill="laravel"', false)
        ->assertSeeInOrder([
            'data-featured-project="sistema-publicado"',
            'data-skill-icon="laravel"',
            'data-carousel-status',
        ], false);
});

it('renders every published featured project in the carousel and keeps the full archive as cards', function (): void {
    homeProject([
        'title' => 'Destacado tercero',
        'slug' => 'destacado-tercero',
        'sort_order' => 30,
    ]);
    homeProject([
        'title' => 'Destacado primero',
        'slug' => 'destacado-primero',
        'sort_order' => 10,
    ]);
    homeProject([
        'title' => 'Destacado cuarto',
        'slug' => 'destacado-cuarto',
        'sort_order' => 40,
    ]);
    homeProject([
        'title' => 'Destacado segundo',
        'slug' => 'destacado-segundo',
        'sort_order' => 20,
    ]);
    homeProject([
        'title' => 'Publicado no destacado',
        'slug' => 'publicado-no-destacado',
        'is_featured' => false,
        'sort_order' => 5,
    ]);
    homeProject([
        'title' => 'Destacado en borrador',
        'slug' => 'destacado-en-borrador',
        'status' => 'draft',
        'sort_order' => 1,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('aria-roledescription="carrusel"', false)
        ->assertSee('aria-live="polite"', false)
        ->assertSee('data-carousel-control="previous"', false)
        ->assertSee('data-carousel-control="next"', false)
        ->assertSeeInOrder([
            'data-featured-project="destacado-primero"',
            'data-featured-project="destacado-segundo"',
            'data-featured-project="destacado-tercero"',
            'data-featured-project="destacado-cuarto"',
        ], false)
        ->assertDontSee('data-featured-project="publicado-no-destacado"', false)
        ->assertDontSee('data-featured-project="destacado-en-borrador"', false);

    $this->get(route('portfolio.projects.index'))
        ->assertOk()
        ->assertSee('Publicado no destacado')
        ->assertDontSee('data-featured-carousel', false);
});

it('shows the featured-project empty state when no published project is highlighted', function (): void {
    homeProject([
        'title' => 'Solo en el archivo',
        'slug' => 'solo-en-el-archivo',
        'is_featured' => false,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('data-service-panel', false)
        ->assertSee('Aún no hay proyectos destacados.')
        ->assertDontSee('data-featured-carousel', false)
        ->assertDontSee('Solo en el archivo');
});

it('adds the experience section only when real experience data exists', function (): void {
    Experience::query()->create([
        'company' => 'Universidad de La Guajira',
        'position' => 'Desarrollador',
        'started_at' => '2025-01-01',
        'is_current' => true,
        'sort_order' => 1,
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('id="experiencia"', false)
        ->assertSee('Universidad de La Guajira')
        ->assertSee('Experiencia');
});

it('redirects former section pages back to their one-page anchors', function (string $routeName, string $anchor): void {
    $this->get(route($routeName))->assertRedirect('/#'.$anchor);
})->with([
    ['portfolio.skills.redirect', 'habilidades'],
    ['portfolio.experience.redirect', 'experiencia'],
    ['portfolio.contact.redirect', 'contacto'],
]);

it('publishes only visible projects in the XML sitemap', function (): void {
    $published = homeProject();
    $draft = homeProject([
        'title' => 'Proyecto no publicado',
        'slug' => 'proyecto-no-publicado',
        'status' => 'draft',
    ]);

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee(route('portfolio.projects.show', ['project' => $published->slug]))
        ->assertDontSee(route('portfolio.projects.show', ['project' => $draft->slug]));
});

it('advertises the sitemap with an absolute URL in robots.txt', function (): void {
    $this->get(route('robots'))
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('Sitemap: '.route('sitemap'));
});

it('ships only the verified inventory cover as a public migrated project asset', function (): void {
    $cover = 'images/projects/994328cd-03b3-449e-a469-37c7989b5500.png';

    expect(Storage::disk('public')->exists($cover))->toBeTrue()
        ->and(Storage::disk('public')->size($cover))->toBeGreaterThan(200_000);
});
