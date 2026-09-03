<?php

use App\Models\SavedFilter;
use Illuminate\Support\Facades\File;
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

test('saved filters use inline management controls', function () {
    $component = File::get(resource_path('js/pages/demos/SavedFilters.svelte'));

    expect($component)
        ->toContain('editingFilterId === savedFilter.id')
        ->toContain('Aktiivne')
        ->toContain('data-testid="inline-filter-save"')
        ->toContain('data-testid="inline-filter-create"')
        ->toContain('data-testid="inline-filter-create-save"')
        ->toContain('data-testid="selected-filter-indicator"')
        ->toContain('data-testid={`saved-filter-actions-${savedFilter.id}`}')
        ->toContain('isCreatingFilter')
        ->toContain('is_default: false')
        ->toContain('data-testid="saved-filters-header"')
        ->toContain('data-testid="unsaved-filter-actions"')
        ->toContain('Salvesta üle')
        ->toContain('Salvesta uuena')
        ->toContain('Filter:')
        ->toContain('GripVertical')
        ->toContain('DragDropProvider')
        ->toContain('useSortable')
        ->toContain('{@attach handleRef}')
        ->toContain('reorder()')
        ->toContain('hover:bg-stokker-primary-50')
        ->toContain('min-w-0 flex-1 truncate')
        ->toContain('ml-auto flex shrink-0')
        ->toContain('grid min-w-0 gap-1')
        ->toContain('relative z-0 min-w-0')
        ->toContain('left-3 z-50 sm:left-auto sm:w-[22rem]')
        ->toContain('aria-pressed={savedFilter.isDefault}')
        ->toContain("'text-amber-500 hover:text-amber-600'")
        ->not->toContain('filterSummary(')
        ->not->toContain('saveDialogOpen')
        ->not->toContain('saveAsDefault')
        ->not->toContain('renameDialogOpen');
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

test('saved filters can be reordered', function () {
    $firstFilter = SavedFilter::factory()->create([
        'name' => 'Esimene filter',
        'position' => 0,
    ]);
    $secondFilter = SavedFilter::factory()->create([
        'name' => 'Teine filter',
        'position' => 1,
    ]);

    $this->putJson(route('demos.dev_238.saved_filters.reorder'), [
        'ids' => [$secondFilter->id, $firstFilter->id],
    ])->assertNoContent();

    expect($secondFilter->refresh()->position)->toBe(0)
        ->and($firstFilter->refresh()->position)->toBe(1);

    $this->get(route('demos.dev_238.saved_filters.index'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->where('savedFilters.0.id', $secondFilter->id)
            ->where('savedFilters.1.id', $firstFilter->id),
        );
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
