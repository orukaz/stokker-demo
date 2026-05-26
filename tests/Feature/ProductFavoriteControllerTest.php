<?php

use App\Models\Product;
use App\Models\ProductFavorite;
use App\Models\User;
use App\Support\ProductFavoriteOwner;

function createFavoriteProduct(): Product
{
    return Product::create([
        'code' => '15450255&ECHO',
        'title' => 'Mootorsaag CS-310ES/30RC, Echo',
        'price' => 209,
        'currency' => 'EUR',
    ]);
}

test('authenticated users can add and remove product favorites', function () {
    $user = User::factory()->create();
    $product = createFavoriteProduct();

    $this->actingAs($user)
        ->postJson(route('products.favorite.store', $product))
        ->assertSuccessful()
        ->assertJsonPath('isFavorited', true)
        ->assertJsonPath('favoritesCount', 1);

    $this->assertDatabaseHas('product_favorites', [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);

    $this->actingAs($user)
        ->deleteJson(route('products.favorite.destroy', $product))
        ->assertSuccessful()
        ->assertJsonPath('isFavorited', false)
        ->assertJsonPath('favoritesCount', 0);

    $this->assertDatabaseMissing('product_favorites', [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);
});

test('guests can add and remove product favorites with a visitor cookie', function () {
    $product = createFavoriteProduct();

    $this->postJson(route('products.favorite.store', $product))
        ->assertSuccessful()
        ->assertJsonPath('isFavorited', true)
        ->assertJsonPath('favoritesCount', 1);

    $favorite = ProductFavorite::query()->sole();

    expect($favorite->visitor_id)->not->toBeNull()
        ->and($favorite->product_id)->toBe($product->id);

    $this->withCredentials()
        ->withCookie(ProductFavoriteOwner::VISITOR_COOKIE, $favorite->visitor_id)
        ->deleteJson(route('products.favorite.destroy', $product))
        ->assertSuccessful()
        ->assertJsonPath('isFavorited', false)
        ->assertJsonPath('favoritesCount', 0);

    $this->assertDatabaseMissing('product_favorites', [
        'visitor_id' => $favorite->visitor_id,
        'product_id' => $product->id,
    ]);
});

test('guests can add and remove product favorites as json', function () {
    $product = createFavoriteProduct();

    $this->postJson(route('products.favorite.store', $product))
        ->assertSuccessful()
        ->assertJsonPath('isFavorited', true)
        ->assertJsonPath('favoritesCount', 1);

    $favorite = ProductFavorite::query()->sole();

    expect($favorite->visitor_id)->not->toBeNull();

    $this->withCredentials()
        ->withCookie(ProductFavoriteOwner::VISITOR_COOKIE, $favorite->visitor_id)
        ->deleteJson(route('products.favorite.destroy', $product))
        ->assertSuccessful()
        ->assertJsonPath('isFavorited', false)
        ->assertJsonPath('favoritesCount', 0);

    $this->assertDatabaseMissing('product_favorites', [
        'visitor_id' => $favorite->visitor_id,
        'product_id' => $product->id,
    ]);
});

test('adding the same favorite twice is idempotent', function () {
    $product = createFavoriteProduct();
    $visitorId = '2c89918a-a29a-4979-9f51-bbb00ec4174c';

    $this->withCredentials()
        ->withCookie(ProductFavoriteOwner::VISITOR_COOKIE, $visitorId)
        ->postJson(route('products.favorite.store', $product))
        ->assertSuccessful()
        ->assertJsonPath('isFavorited', true)
        ->assertJsonPath('favoritesCount', 1);

    $this->withCredentials()
        ->withCookie(ProductFavoriteOwner::VISITOR_COOKIE, $visitorId)
        ->postJson(route('products.favorite.store', $product))
        ->assertSuccessful()
        ->assertJsonPath('isFavorited', true)
        ->assertJsonPath('favoritesCount', 1);

    expect(ProductFavorite::query()->count())->toBe(1);
});
