<?php

namespace App\Models;

use Database\Factories\SavedFilterFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

#[Fillable(['view', 'name', 'filters', 'is_default'])]
class SavedFilter extends Model
{
    /** @use HasFactory<SavedFilterFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'view' => 'orders',
        'is_default' => false,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'is_default' => 'boolean',
        ];
    }

    public function makeDefault(): void
    {
        DB::transaction(function (): void {
            self::query()
                ->where('view', $this->view)
                ->lockForUpdate()
                ->get();

            self::query()
                ->where('view', $this->view)
                ->update(['is_default' => false]);

            self::query()
                ->whereKey($this)
                ->update(['is_default' => true]);

            $this->refresh();
        });
    }
}
