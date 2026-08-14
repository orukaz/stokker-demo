<?php

test('the POS signature package page is available', function () {
    $this->withoutVite();

    $this->get(route('pos-signature.show'))
        ->assertSuccessful()
        ->assertSee('pos-signature-app');
});

test('the POS signature package provides the approved stroke colors', function () {
    $signatureContract = file_get_contents(
        base_path('packages/stokker/pos-signature/resources/js/signature.ts'),
    );

    expect($signatureContract)
        ->toContain("{ label: 'Black', value: '#111827' }")
        ->toContain("{ label: 'Stokker Blue', value: '#0075ba' }")
        ->toContain("{ label: 'Red', value: '#e30613' }");
});
