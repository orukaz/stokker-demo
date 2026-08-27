<?php

test('a configured source set is publicly available', function () {
    $response = $this->getJson(route('source_code.show', [
        'sourceSet' => 'phone-field',
    ]));

    $response
        ->assertSuccessful()
        ->assertJsonPath('sourceSet', 'phone-field')
        ->assertJsonPath('title', 'Telefonivälja lähtekood')
        ->assertJsonCount(6, 'files')
        ->assertJsonPath('files.0.id', 'demo-page')
        ->assertJsonPath('files.0.language', 'svelte')
        ->assertJsonPath('files.4.language', 'php');

    expect($response->json('files.0.code'))
        ->toContain('<script lang="ts">');
});

test('an unconfigured source set is not available', function () {
    $this->getJson(route('source_code.show', [
        'sourceSet' => 'unconfigured',
    ]))->assertNotFound();
});

test('a configured path outside the allowed roots is not available', function () {
    config()->set('source_code.source_sets.environment', [
        'title' => 'Environment',
        'files' => [
            'environment' => [
                'label' => '.env',
                'language' => 'php',
                'path' => '.env',
            ],
        ],
    ]);

    $this->getJson(route('source_code.show', [
        'sourceSet' => 'environment',
    ]))->assertNotFound();
});
