<?php

namespace Database\Seeders;

use App\Models\SavedFilter;
use Illuminate\Database\Seeder;

class SavedFilterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SavedFilter::query()
            ->where('view', 'orders')
            ->update(['is_default' => false]);

        foreach ($this->filters() as $filter) {
            SavedFilter::query()->updateOrCreate(
                [
                    'view' => 'orders',
                    'name' => $filter['name'],
                ],
                $filter,
            );
        }
    }

    /**
     * @return list<array{
     *     name: string,
     *     filters: array<string, string>,
     *     is_default: bool,
     *     position: int
     * }>
     */
    private function filters(): array
    {
        return [
            [
                'name' => 'Tänased aktiivsed',
                'filters' => [
                    'search' => '',
                    'status' => 'in_progress',
                    'branch' => '',
                    'assignee' => '',
                    'date_from' => '2026-09-03',
                    'date_to' => '2026-09-03',
                ],
                'is_default' => true,
                'position' => 0,
            ],
            [
                'name' => 'Minu töölaud',
                'filters' => [
                    'search' => '',
                    'status' => '',
                    'branch' => '',
                    'assignee' => 'Mari Maasikas',
                    'date_from' => '',
                    'date_to' => '',
                ],
                'is_default' => false,
                'position' => 1,
            ],
            [
                'name' => 'Tartu väljastamata',
                'filters' => [
                    'search' => '',
                    'status' => 'ready',
                    'branch' => 'Tartu',
                    'assignee' => '',
                    'date_from' => '',
                    'date_to' => '',
                ],
                'is_default' => false,
                'position' => 2,
            ],
        ];
    }
}
