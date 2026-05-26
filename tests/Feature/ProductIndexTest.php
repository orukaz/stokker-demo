<?php

use App\Models\Product;
use App\Models\ProductFavorite;
use App\Models\User;
use App\Support\ProductFavoriteOwner;
use Illuminate\Pagination\Cursor;
use Inertia\Testing\AssertableInertia as Assert;

function createMootorsaagProducts(int $count): void
{
    foreach (range(1, $count) as $number) {
        Product::create([
            'code' => "SAAG-{$number}",
            'title' => "Mootorsaag {$number}",
            'link' => "https://www.stokker.ee/et/mootorsaed/SAAG-{$number}",
            'image_url' => "https://media.stokker.com/{$number}.jpg",
            'brand' => 'Echo',
            'availability' => $number % 5 === 0 ? 'out of stock' : 'in stock',
            'quantity' => $number % 5 === 0 ? 0 : $number,
            'price' => 100 + $number,
            'currency' => 'EUR',
        ]);
    }
}

test('mootorsaed page renders the product index with twenty initial products', function () {
    createMootorsaagProducts(25);

    $this->get(route('products.index'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('products/Index')
            ->has('products.data', 20)
            ->where('products.data.0.title', 'Mootorsaag 1')
            ->where('products.data.0.quantity', 1)
            ->where('products.data.0.isFavorited', false)
            ->missing('products.data.0.availabilityLabel')
            ->missing('products.data.0.isAvailable')
            ->where('favoritesCount', 0)
            ->where('sidebarOpen', true),
        );
});

test('mootorsaed page shares closed sidebar state from cookie', function () {
    createMootorsaagProducts(1);

    $this->withCookie('sidebar_state', 'false')
        ->get(route('products.index'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->where('sidebarOpen', false),
        );
});

test('mootorsaed page returns ten products after the initial cursor', function () {
    createMootorsaagProducts(35);

    $cursor = (new Cursor(['id' => 20], true))->encode();

    $this->get(route('products.index', ['cursor' => $cursor]))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('products/Index')
            ->has('products.data', 10)
            ->where('products.data.0.title', 'Mootorsaag 21'),
        );
});

test('mootorsaed page shares guest favorite state and count', function () {
    createMootorsaagProducts(3);

    ProductFavorite::create([
        'visitor_id' => '2c89918a-a29a-4979-9f51-bbb00ec4174c',
        'product_id' => Product::query()->firstOrFail()->id,
    ]);

    $this->withCookie(ProductFavoriteOwner::VISITOR_COOKIE, '2c89918a-a29a-4979-9f51-bbb00ec4174c')
        ->get(route('products.index'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->where('favoritesCount', 1)
            ->where('products.data.0.isFavorited', true),
        );
});

test('mootorsaed page shares authenticated user favorite state and count', function () {
    createMootorsaagProducts(3);

    $user = User::factory()->create();

    ProductFavorite::create([
        'user_id' => $user->id,
        'product_id' => Product::query()->firstOrFail()->id,
    ]);

    $this->actingAs($user)
        ->get(route('products.index'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->where('favoritesCount', 1)
            ->where('products.data.0.isFavorited', true),
        );
});

test('login route remains available', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('auth/Login'),
        );
});
