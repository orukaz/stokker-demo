<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('users.password');

        User::query()->updateOrCreate(
            ['email' => 'mari@example.com'],
            [
                'name' => 'Mari Maasikas',
                'password' => $password,
                'email_verified_at' => now(),
            ],
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => $password,
                'email_verified_at' => now(),
            ],
        );
    }
}
