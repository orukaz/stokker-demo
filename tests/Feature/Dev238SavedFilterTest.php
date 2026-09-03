<?php

use App\Models\SavedFilter;
use Inertia\Testing\AssertableInertia as Assert;

function orderFilters(array $overrides = []): array
{
    return [
        'search' => '',
        'status' => '',
        'branch' => '',
        'assignee' => '',
        'date_from' => '',
        'date_to' => '',
        ...$overrides,
    ];
}

test('the DEV-238 saved filters demo page is available', function () {
    SavedFilter::factory()->create([
        'name' => 'Tänased aktiivsed',
        'filters' => orderFilters(['status' => 'in_progress']),
        'is_default' => true,
    ]);

    $this->get(route('demos.dev_238.saved_filters.index'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('demos/SavedFilters')
            ->has('savedFilters', 1)
            ->where('savedFilters.0.name', 'Tänased aktiivsed')
            ->where('savedFilters.0.filters.status', 'in_progress')
            ->where('savedFilters.0.isDefault', true),
        );
});

test('a filter can be saved and made the default', function () {
    $existingDefault = SavedFilter::factory()->create([
        'name' => 'Vana vaikefilter',
        'is_default' => true,
    ]);

    $response = $this->postJson(route('demos.dev_238.saved_filters.store'), [
        'view' => 'orders',
        'name' => 'Tartu väljastamata',
        'filters' => orderFilters([
            'status' => 'ready',
            'branch' => 'Tartu',
        ]),
        'is_default' => true,
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('savedFilter.name', 'Tartu väljastamata')
        ->assertJsonPath('savedFilter.filters.status', 'ready')
        ->assertJsonPath('savedFilter.isDefault', true);

    expect($existingDefault->refresh()->is_default)->toBeFalse();

    $savedFilter = SavedFilter::query()
        ->where('name', 'Tartu väljastamata')
        ->firstOrFail();

    expect($savedFilter->filters)->toBe(orderFilters([
        'status' => 'ready',
        'branch' => 'Tartu',
    ]));
});

test('a saved filter can be overwritten and renamed', function () {
    $savedFilter = SavedFilter::factory()->create([
        'name' => 'Minu töölaud',
        'filters' => orderFilters(['assignee' => 'Mari Maasikas']),
    ]);

    $this->patchJson(route('demos.dev_238.saved_filters.update', $savedFilter), [
        'view' => 'orders',
        'name' => 'Mari aktiivsed tellimused',
        'filters' => orderFilters([
            'status' => 'in_progress',
            'assignee' => 'Mari Maasikas',
        ]),
    ])
        ->assertSuccessful()
        ->assertJsonPath('savedFilter.name', 'Mari aktiivsed tellimused')
        ->assertJsonPath('savedFilter.filters.status', 'in_progress');

    $savedFilter->refresh();

    expect($savedFilter->name)->toBe('Mari aktiivsed tellimused')
        ->and($savedFilter->filters)->toBe(orderFilters([
            'status' => 'in_progress',
            'assignee' => 'Mari Maasikas',
        ]));
});

test('a different saved filter can be selected as the default', function () {
    $oldDefault = SavedFilter::factory()->create([
        'name' => 'Vana vaikefilter',
        'is_default' => true,
    ]);
    $newDefault = SavedFilter::factory()->create([
        'name' => 'Uus vaikefilter',
        'is_default' => false,
    ]);

    $this->putJson(route('demos.dev_238.saved_filters.make_default', $newDefault))
        ->assertSuccessful()
        ->assertJsonPath('savedFilter.id', $newDefault->id)
        ->assertJsonPath('savedFilter.isDefault', true);

    expect($oldDefault->refresh()->is_default)->toBeFalse()
        ->and($newDefault->refresh()->is_default)->toBeTrue();
});

test('a saved filter can be deleted', function () {
    $savedFilter = SavedFilter::factory()->create();

    $this->deleteJson(route('demos.dev_238.saved_filters.destroy', $savedFilter))
        ->assertNoContent();

    $this->assertModelMissing($savedFilter);
});

test('saved filter names must be unique within a view', function () {
    SavedFilter::factory()->create(['name' => 'Minu töölaud']);

    $this->postJson(route('demos.dev_238.saved_filters.store'), [
        'view' => 'orders',
        'name' => 'Minu töölaud',
        'filters' => orderFilters(),
        'is_default' => false,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');

    $this->postJson(route('demos.dev_238.saved_filters.store'), [
        'view' => 'service_orders',
        'name' => 'Minu töölaud',
        'filters' => orderFilters(),
        'is_default' => false,
    ])->assertCreated();
});
