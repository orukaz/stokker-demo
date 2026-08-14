<?php

test('the POS signature package page is available', function () {
    $this->withoutVite();

    $this->get(route('pos-signature.show'))
        ->assertSuccessful()
        ->assertSee('pos-signature-app');
});
