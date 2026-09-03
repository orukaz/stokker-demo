<?php

namespace App\Http\Requests;

use App\Models\SavedFilter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

abstract class SavedFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function savedFilterRules(?SavedFilter $savedFilter = null): array
    {
        $uniqueName = Rule::unique((new SavedFilter)->getTable(), 'name')
            ->where('view', $this->string('view')->toString());

        return [
            'view' => ['required', 'string', 'alpha_dash', 'max:50'],
            'name' => [
                'required',
                'string',
                'max:60',
                $this->ignoreCurrentFilter($uniqueName, $savedFilter),
            ],
            'filters' => ['required', 'array'],
            'filters.search' => ['present', 'nullable', 'string', 'max:100'],
            'filters.status' => ['present', 'nullable', 'string', Rule::in(['', 'new', 'in_progress', 'ready', 'completed'])],
            'filters.branch' => ['present', 'nullable', 'string', Rule::in(['', 'Tallinn', 'Tartu', 'Pärnu', 'Rakvere'])],
            'filters.assignee' => ['present', 'nullable', 'string', Rule::in(['', 'Mari Maasikas', 'Karl Kask', 'Anna Tamm'])],
            'filters.date_from' => ['present', 'nullable', 'date_format:Y-m-d'],
            'filters.date_to' => [
                'present',
                'nullable',
                'date_format:Y-m-d',
                Rule::when($this->filled('filters.date_from'), 'after_or_equal:filters.date_from'),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $submittedFilters = $this->input('filters');

        if (! is_array($submittedFilters)) {
            return;
        }

        $filters = collect([
            'search' => '',
            'status' => '',
            'branch' => '',
            'assignee' => '',
            'date_from' => '',
            'date_to' => '',
            ...$submittedFilters,
        ])->map(fn (mixed $value): mixed => $value ?? '')->all();

        $this->merge(['filters' => $filters]);
    }

    private function ignoreCurrentFilter(Unique $rule, ?SavedFilter $savedFilter): Unique
    {
        return $savedFilter ? $rule->ignore($savedFilter) : $rule;
    }
}
