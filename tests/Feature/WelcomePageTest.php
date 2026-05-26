<?php

use Inertia\Testing\AssertableInertia as Assert;

test('welcome page renders the welcome component', function () {
    $this->get(route('welcome'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome'),
        );
});

test('home redirects to products index route', function () {
    $this->get(route('home'))
        ->assertRedirect(route('products.index'));
});
