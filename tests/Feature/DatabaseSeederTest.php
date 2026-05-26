<?php

use App\Models\User;
use App\Services\Stokker\StokkerService;
use Illuminate\Support\Facades\Hash;

use function Pest\Laravel\mock;

test('it seeds demo users and syncs products', function () {
    config(['users.password' => 'secret']);

    mock(StokkerService::class)
        ->expects('syncProducts')
        ->with('SET', 'FO01')
        ->once();

    $this->seed();

    $mari = User::query()->where('email', 'mari@example.com')->firstOrFail();
    $admin = User::query()->where('email', 'admin@example.com')->firstOrFail();

    expect($mari->name)->toBe('Mari Maasikas')
        ->and($admin->name)->toBe('Admin User')
        ->and(Hash::check('secret', $mari->password))->toBeTrue()
        ->and(Hash::check('secret', $admin->password))->toBeTrue();
});
