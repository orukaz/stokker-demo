<?php

use App\Services\Stokker\StokkerService;
use Illuminate\Console\Scheduling\Schedule;

use function Pest\Laravel\mock;

test('it syncs stokker products', function () {
    mock(StokkerService::class)
        ->expects('syncProducts')
        ->with('SET', 'FO01')
        ->once();

    $this->artisan('stokker:sync-products')
        ->assertSuccessful();
});

test('it schedules stokker product sync hourly', function () {
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event): bool => str_contains($event->command ?? '', 'stokker:sync-products'));

    expect($event)
        ->not->toBeNull()
        ->and($event->getExpression())->toBe('0 * * * *')
        ->and($event->withoutOverlapping)->toBeTrue();
});
