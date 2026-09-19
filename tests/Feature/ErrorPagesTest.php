<?php

it('renders the custom 404 page in Spanish', function (): void {
    $this->get('/ruta-que-no-existe')
        ->assertNotFound()
        ->assertSee('Página no encontrada')
        ->assertSee('Volver al inicio');
});

it('ships a custom view for every common error status', function (int $status): void {
    expect(view()->exists("errors.{$status}"))->toBeTrue();
})->with([403, 404, 419, 429, 500, 503]);
