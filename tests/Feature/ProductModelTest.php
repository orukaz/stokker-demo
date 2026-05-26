<?php

use App\Models\Product;
use App\Models\ProductFavorite;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

test('it stores stokker product fields', function () {
    $product = Product::create([
        'code' => '15450255&ECHO',
        'title' => 'Mootorsaag CS-310ES/30RC, Echo',
        'quantity' => '1511.0000',
        'price' => '209.00',
        'currency' => 'EUR',
    ])->refresh();

    expect($product->code)->toBe('15450255&ECHO')
        ->and($product->title)->toBe('Mootorsaag CS-310ES/30RC, Echo')
        ->and($product->quantity)->toBe('1511.0000')
        ->and($product->price)->toBe('209.0000')
        ->and($product->currency)->toBe('EUR');
});

test('users and visitors can favorite products once', function () {
    $user = User::factory()->create();
    $visitorId = (string) Str::uuid();
    $product = Product::create([
        'code' => '15450255&ECHO',
        'title' => 'Mootorsaag CS-310ES/30RC, Echo',
    ]);

    ProductFavorite::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);

    ProductFavorite::create([
        'visitor_id' => $visitorId,
        'product_id' => $product->id,
    ]);

    expect($user->favoriteProducts()->first()->is($product))->toBeTrue()
        ->and($product->favoritedByUsers()->first()->is($user))->toBeTrue()
        ->and($product->favorites)->toHaveCount(2);

    expect(fn () => ProductFavorite::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
    ])
    )->toThrow(QueryException::class);

    expect(fn () => ProductFavorite::create([
        'visitor_id' => $visitorId,
        'product_id' => $product->id,
    ])
    )->toThrow(QueryException::class);
});
