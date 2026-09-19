<?php

use Illuminate\Support\Facades\Vite;

beforeEach(function (): void {
    // Un servidor de Vite en marcha en la máquina local no debe alterar los tests.
    Vite::useHotFile(storage_path('framework/testing/vite-hot-inexistente'));
});

it('sends the content security policy in report-only mode by default', function (): void {
    $response = $this->get(route('home'))->assertOk();

    $policy = $response->headers->get('Content-Security-Policy-Report-Only');

    expect($policy)
        ->toContain("default-src 'self'")
        ->toContain("object-src 'none'")
        ->toContain("frame-ancestors 'self'")
        ->toContain('https://cdn.simpleicons.org');

    expect($response->headers->has('Content-Security-Policy'))->toBeFalse();
});

it('enforces the policy when report-only mode is turned off', function (): void {
    config(['security.csp.report_only' => false]);

    $response = $this->get(route('home'))->assertOk();

    expect($response->headers->get('Content-Security-Policy'))->toContain("default-src 'self'")
        ->and($response->headers->has('Content-Security-Policy-Report-Only'))->toBeFalse();
});

it('does not send the policy when it is disabled', function (): void {
    config(['security.csp.enabled' => false]);

    $response = $this->get(route('home'))->assertOk();

    expect($response->headers->has('Content-Security-Policy'))->toBeFalse()
        ->and($response->headers->has('Content-Security-Policy-Report-Only'))->toBeFalse();
});

it('only loads images and scripts from origins the policy allows', function (): void {
    $html = $this->get(route('home'))->assertOk()->getContent();

    preg_match_all('/\ssrc="(https?:\/\/[^"]+)"/', $html, $matches);

    foreach ($matches[1] as $url) {
        expect(parse_url($url, PHP_URL_HOST))->toBeIn(['localhost', 'cdn.simpleicons.org']);
    }
});

it('skips the policy while the Vite dev server is running', function (): void {
    $hotFile = tempnam(sys_get_temp_dir(), 'vite-hot-');
    file_put_contents($hotFile, 'http://localhost:5173');
    Vite::useHotFile($hotFile);

    $response = $this->get(route('home'))->assertOk();

    @unlink($hotFile);

    expect($response->headers->has('Content-Security-Policy'))->toBeFalse()
        ->and($response->headers->has('Content-Security-Policy-Report-Only'))->toBeFalse();
});
