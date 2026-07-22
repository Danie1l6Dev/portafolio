<?php

use App\Enums\AchievementType;
use App\Livewire\Admin\AchievementManager;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

it('renders only visible achievements and prioritizes featured evidence', function (): void {
    Achievement::factory()->create([
        'title' => 'Certificación visible',
        'type' => AchievementType::Certification,
        'organization' => 'Entidad técnica',
        'achieved_at' => '2025-05-01',
        'sort_order' => 1,
    ]);
    Achievement::factory()->featured()->create([
        'title' => 'Ganador de Hackathon',
        'type' => AchievementType::Hackathon,
        'organization' => 'Hackathon regional',
        'result' => 'Primer lugar',
        'role' => 'Desarrollo backend y arquitectura',
        'description' => 'Construimos una solución funcional en equipo.',
        'achieved_at' => '2026-06-15',
        'external_url' => 'https://example.com/evidencia',
        'sort_order' => 20,
    ]);
    Achievement::factory()->hidden()->create([
        'title' => 'Reconocimiento privado',
        'organization' => 'Organización privada',
        'achieved_at' => '2024-01-01',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('id="logros"', false)
        ->assertSee('Logros que respaldan el trabajo.')
        ->assertSee('Ganador de Hackathon')
        ->assertSee('Desarrollo backend y arquitectura')
        ->assertSee('https://example.com/evidencia', false)
        ->assertDontSee('Reconocimiento privado')
        ->assertSeeInOrder(['Ganador de Hackathon', 'Certificación visible']);
});

it('keeps the achievement section and navigation hidden without public records', function (): void {
    Achievement::factory()->hidden()->create([
        'title' => 'Logro todavía privado',
        'organization' => 'Organización',
        'achieved_at' => '2025-01-01',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('id="logros"', false)
        ->assertDontSee('href="'.route('home').'#logros"', false)
        ->assertDontSee('Logro todavía privado');
});

it('lets verified editors create achievements with managed image and certificate files', function (): void {
    Storage::fake('public');

    $editor = User::factory()->create(['role' => 'editor']);

    Livewire::actingAs($editor)
        ->test(AchievementManager::class)
        ->set('title', 'Ganador Hackathon Guajira')
        ->set('type', AchievementType::Hackathon->value)
        ->set('organization', 'Universidad de La Guajira')
        ->set('result', 'Primer lugar')
        ->set('role', 'Backend y modelo de datos')
        ->set('description', 'Solución desarrollada colaborativamente durante la competencia.')
        ->set('achievedAt', '2026-06-20')
        ->set('externalUrl', 'https://example.com/hackathon')
        ->set('isFeatured', true)
        ->set('isVisible', true)
        ->set('sortOrder', 1)
        ->set('image', UploadedFile::fake()->image('equipo.jpg', 1200, 700))
        ->set('certificate', UploadedFile::fake()->create('certificado.pdf', 200, 'application/pdf'))
        ->call('save')
        ->assertHasNoErrors();

    $achievement = Achievement::query()->firstOrFail();

    expect($achievement->type)->toBe(AchievementType::Hackathon)
        ->and($achievement->is_featured)->toBeTrue()
        ->and($achievement->is_visible)->toBeTrue();
    Storage::disk('public')->assertExists($achievement->image_path);
    Storage::disk('public')->assertExists($achievement->certificate_path);
});

it('validates achievement dates and certificate types', function (): void {
    Storage::fake('public');

    Livewire::actingAs(User::factory()->create(['role' => 'admin']))
        ->test(AchievementManager::class)
        ->set('title', 'Dato inválido')
        ->set('type', AchievementType::Award->value)
        ->set('organization', 'Organización')
        ->set('achievedAt', now()->addDay()->toDateString())
        ->set('certificate', UploadedFile::fake()->image('no-es-pdf.jpg'))
        ->call('save')
        ->assertHasErrors(['achievedAt', 'certificate']);

    $this->assertDatabaseCount('achievements', 0);
});

it('deletes achievement records and their managed files together', function (): void {
    Storage::fake('public');
    Storage::disk('public')->put('images/achievements/evidence.jpg', 'image');
    Storage::disk('public')->put('documents/achievements/certificate.pdf', 'pdf');

    $achievement = Achievement::factory()->create([
        'image_path' => 'images/achievements/evidence.jpg',
        'certificate_path' => 'documents/achievements/certificate.pdf',
    ]);

    Livewire::actingAs(User::factory()->create(['role' => 'admin']))
        ->test(AchievementManager::class)
        ->call('confirmDelete', $achievement->id)
        ->call('delete')
        ->assertHasNoErrors();

    $this->assertDatabaseMissing('achievements', ['id' => $achievement->id]);
    Storage::disk('public')->assertMissing('images/achievements/evidence.jpg');
    Storage::disk('public')->assertMissing('documents/achievements/certificate.pdf');
});

it('protects the achievement manager and its mutations', function (): void {
    $this->get(route('panel.achievements'))->assertRedirect(route('login'));

    Livewire::test(AchievementManager::class)
        ->set('title', 'No autorizado')
        ->set('organization', 'Organización')
        ->set('achievedAt', '2026-01-01')
        ->call('save')
        ->assertForbidden();
});

it('renders the achievement editor for verified content editors', function (): void {
    $editor = User::factory()->create(['role' => 'editor']);
    Achievement::factory()->create([
        'title' => 'Certificación Laravel',
        'organization' => 'Entidad técnica',
        'achieved_at' => '2025-05-01',
    ]);

    $this->actingAs($editor)
        ->get(route('panel.achievements'))
        ->assertOk()
        ->assertSee('Logros y reconocimientos')
        ->assertSee('Certificación Laravel');
});
