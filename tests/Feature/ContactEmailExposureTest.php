<?php

it('does not expose the public email in the home HTML', function (): void {
    $email = config('portfolio.email');

    $html = $this->get(route('home'))->assertOk()->getContent();

    expect($html)
        ->not->toContain($email)
        ->not->toContain('mailto:'.$email)
        ->toContain('copyEmail(')
        ->toContain('contacto\/correo');
});

it('does not expose the public email in the structured data', function (): void {
    $html = $this->get(route('home'))->assertOk()->getContent();

    preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);

    expect($matches)->toHaveKey(1)
        ->and($matches[1])->not->toContain('"email"')
        ->and($matches[1])->not->toContain(config('portfolio.email'));
});

it('returns the email only from the copy endpoint without caching it', function (): void {
    $this->getJson(route('portfolio.contact.email'))
        ->assertOk()
        ->assertExactJson(['email' => config('portfolio.email')])
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
        ->assertHeaderContains('Cache-Control', 'no-store');
});
