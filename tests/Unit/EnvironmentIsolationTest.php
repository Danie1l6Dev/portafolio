<?php

use Tests\TestCase;

uses(TestCase::class);

it('boots the test suite against the isolated in-memory database', function (): void {
    expect(app()->environment('testing'))->toBeTrue()
        ->and(config('database.default'))->toBe('sqlite')
        ->and(config('database.connections.sqlite.database'))->toBe(':memory:');
});
