<?php

use App\Livewire\Admin\ResumeManager;
use App\Models\User;
use App\Services\ResumeService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    Storage::fake('public');
});

function resumeEditor(string $role = 'admin'): User
{
    return User::factory()->create(['role' => $role]);
}

function unverifiedEditor(): User
{
    return User::factory()->unverified()->create(['role' => 'editor']);
}

it('shows the resume page in the admin panel only to verified content editors', function (): void {
    $this->get(route('panel.resume'))->assertRedirect(route('login'));

    $this->actingAs(unverifiedEditor())
        ->get(route('panel.resume'))
        ->assertRedirect(route('verification.notice'));

    $this->actingAs(resumeEditor())
        ->get(route('panel.resume'))
        ->assertOk()
        ->assertSee('Hoja de vida')
        ->assertSee('PDF original incluido en el proyecto');
});

it('publishes an uploaded pdf and serves it from the public download button', function (): void {
    $file = UploadedFile::fake()->create('cv-nueva.pdf', 200, 'application/pdf');

    Livewire::actingAs(resumeEditor())
        ->test(ResumeManager::class)
        ->set('resume', $file)
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('resume', null)
        ->assertSee('Subida desde el panel');

    Storage::disk('public')->assertExists(ResumeService::PATH);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(Storage::disk('public')->url(ResumeService::PATH).'?v=', false)
        ->assertDontSee(asset(config('portfolio.resume.path')), false);
});

it('replaces the previous upload instead of keeping old copies', function (): void {
    $editor = resumeEditor();

    foreach (['primera.pdf', 'segunda.pdf'] as $name) {
        Livewire::actingAs($editor)
            ->test(ResumeManager::class)
            ->set('resume', UploadedFile::fake()->create($name, 100, 'application/pdf'))
            ->call('save')
            ->assertHasNoErrors();
    }

    expect(Storage::disk('public')->allFiles('documents'))->toBe([ResumeService::PATH]);
});

it('rejects files that are not pdf or exceed the size limit', function (): void {
    $component = Livewire::actingAs(resumeEditor())->test(ResumeManager::class);

    $component->set('resume', UploadedFile::fake()->image('foto.png'))
        ->assertHasErrors(['resume' => 'mimes']);

    $component->set('resume', UploadedFile::fake()->create('enorme.pdf', 6_000, 'application/pdf'))
        ->assertHasErrors(['resume' => 'max']);

    $component->call('save')->assertHasErrors(['resume']);

    Storage::disk('public')->assertMissing(ResumeService::PATH);
});

it('requires a file before saving', function (): void {
    Livewire::actingAs(resumeEditor())
        ->test(ResumeManager::class)
        ->call('save')
        ->assertHasErrors(['resume' => 'required']);
});

it('restores the bundled resume when the upload is removed', function (): void {
    Storage::disk('public')->put(ResumeService::PATH, '%PDF-1.4 subido');

    Livewire::actingAs(resumeEditor())
        ->test(ResumeManager::class)
        ->assertSee('Subida desde el panel')
        ->call('confirmReset')
        ->call('resetToDefault')
        ->assertSee('PDF original incluido en el proyecto');

    Storage::disk('public')->assertMissing(ResumeService::PATH);
    expect(app(ResumeService::class)->url())->toBe(asset(config('portfolio.resume.path')));
});

it('forbids unverified users from changing the resume through component actions', function (): void {
    Livewire::actingAs(unverifiedEditor())
        ->test(ResumeManager::class)
        ->set('resume', UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'))
        ->call('save')
        ->assertForbidden();

    Livewire::actingAs(unverifiedEditor())
        ->test(ResumeManager::class)
        ->call('resetToDefault')
        ->assertForbidden();

    Storage::disk('public')->assertMissing(ResumeService::PATH);
});
