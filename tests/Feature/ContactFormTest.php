<?php

use App\Actions\StoreContactMessage;
use App\Livewire\Portfolio\ContactForm;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

afterEach(function (): void {
    RateLimiter::clear(StoreContactMessage::rateLimitKey('127.0.0.1'));
});

it('stores a contact message and clears the form after success', function (): void {
    Livewire::test(ContactForm::class)
        ->set('name', 'Ada Lovelace')
        ->set('email', 'ada@example.com')
        ->set('subject', 'Nuevo proyecto')
        ->set('body', 'Quiero conversar sobre una nueva plataforma web.')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('name', '')
        ->assertSet('email', '')
        ->assertSet('subject', '')
        ->assertSet('body', '')
        ->assertSet('successMessage', 'Mensaje enviado correctamente. Me pondré en contacto contigo pronto.');

    $this->assertDatabaseHas('messages', [
        'name' => 'Ada Lovelace',
        'email' => 'ada@example.com',
        'subject' => 'Nuevo proyecto',
        'body' => 'Quiero conversar sobre una nueva plataforma web.',
        'ip_address' => '127.0.0.1',
        'is_read' => false,
    ]);
});

it('validates contact data and preserves every field when validation fails', function (): void {
    Livewire::test(ContactForm::class)
        ->set('name', 'A')
        ->set('email', 'correo-invalido')
        ->set('subject', 'No')
        ->set('body', 'Corto')
        ->call('submit')
        ->assertHasErrors(['name', 'email', 'subject', 'body'])
        ->assertSet('name', 'A')
        ->assertSet('email', 'correo-invalido')
        ->assertSet('subject', 'No')
        ->assertSet('body', 'Corto')
        ->assertSet('successMessage', null);

    $this->assertDatabaseCount('messages', 0);
});

it('silently rejects honeypot submissions without storing a message', function (): void {
    Livewire::test(ContactForm::class)
        ->set('name', 'Bot Example')
        ->set('email', 'bot@example.com')
        ->set('subject', 'Spam automatizado')
        ->set('body', 'Este contenido no debe persistirse en la base de datos.')
        ->set('website', 'https://spam.example.com')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSet('name', '')
        ->assertSet('website', '')
        ->assertSet('successMessage', 'Mensaje enviado correctamente. Me pondré en contacto contigo pronto.');

    $this->assertDatabaseCount('messages', 0);
});

it('limits repeated Livewire submissions by IP and preserves the entered data', function (): void {
    $key = StoreContactMessage::rateLimitKey('127.0.0.1');

    foreach (range(1, StoreContactMessage::MAX_ATTEMPTS) as $attempt) {
        RateLimiter::hit($key, StoreContactMessage::DECAY_SECONDS);
    }

    Livewire::test(ContactForm::class)
        ->set('name', 'Grace Hopper')
        ->set('email', 'grace@example.com')
        ->set('subject', 'Consulta profesional')
        ->set('body', 'Necesito información para desarrollar una aplicación web.')
        ->call('submit')
        ->assertHasErrors(['form'])
        ->assertSet('name', 'Grace Hopper')
        ->assertSet('email', 'grace@example.com')
        ->assertSet('subject', 'Consulta profesional')
        ->assertSet('body', 'Necesito información para desarrollar una aplicación web.')
        ->assertSet('successMessage', null)
        ->assertSet('errorMessage', 'Has enviado varios mensajes en poco tiempo. Espera un minuto antes de intentarlo de nuevo.');

    $this->assertDatabaseCount('messages', 0);
});

it('renders accessible feedback, field state, and loading hooks', function (): void {
    Livewire::test(ContactForm::class)
        ->assertSeeHtml('aria-live="polite"')
        ->assertSeeHtml('aria-live="assertive"')
        ->assertSeeHtml('aria-invalid="false"')
        ->assertSeeHtml('wire:loading.attr="disabled"')
        ->set('email', 'correo-invalido')
        ->call('submit')
        ->assertHasErrors(['email'])
        ->assertSeeHtml('aria-invalid="true"')
        ->assertSeeHtml('aria-describedby="contact-email-error"');
});

it('clears a stale field error as soon as that field is corrected', function (): void {
    Livewire::test(ContactForm::class)
        ->set('name', 'Ada Lovelace')
        ->set('email', 'correo-invalido')
        ->set('subject', 'Nuevo proyecto')
        ->set('body', 'Quiero conversar sobre una nueva plataforma web.')
        ->call('submit')
        ->assertHasErrors(['email'])
        ->set('email', 'ada@example.com')
        ->assertHasNoErrors(['email'])
        ->assertSeeHtml('aria-invalid="false"');
});
