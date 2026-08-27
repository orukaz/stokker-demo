<?php

use Inertia\Testing\AssertableInertia as Assert;

test('the DEV-193 phone field demo page is available', function () {
    $this->get(route('demos.dev_193.phone_field'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('demos/PhoneField'),
        );
});
