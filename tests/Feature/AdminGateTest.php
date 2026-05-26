<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(['web', 'auth', 'can:admin'])->get('/admin-test', fn () => 'ok');
});

test('admin user can access admin routes', function () {
    $admin = User::factory()->create([
        'email' => 'admin@example.com',
    ]);

    $this->actingAs($admin)
        ->get('/admin-test')
        ->assertOk();
});

test('regular user cannot access admin routes', function () {
    $user = User::factory()->create([
        'email' => 'mari@example.com',
    ]);

    $this->actingAs($user)
        ->get('/admin-test')
        ->assertForbidden();
});
