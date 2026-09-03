<?php

namespace Database\Factories;

use App\Models\SavedFilter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SavedFilter>
 */
class SavedFilterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'view' => 'orders',
            'name' => fake()->unique()->words(3, true),
            'filters' => [
                'search' => '',
                'status' => fake()->randomElement(['', 'new', 'in_progress', 'ready', 'completed']),
                'branch' => fake()->randomElement(['', 'Tallinn', 'Tartu', 'Pärnu', 'Rakvere']),
                'assignee' => '',
                'date_from' => '',
                'date_to' => '',
            ],
            'is_default' => false,
        ];
    }
}
