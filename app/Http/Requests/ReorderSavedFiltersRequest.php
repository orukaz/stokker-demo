<?php

namespace App\Http\Requests;

use App\Models\SavedFilter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ReorderSavedFiltersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ids' => ['required', 'array', 'min:1', 'max:100'],
            'ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists((new SavedFilter)->getTable(), 'id')
                    ->where('view', 'orders'),
            ],
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                if (count($this->array('ids')) !== SavedFilter::query()->where('view', 'orders')->count()) {
                    $validator->errors()->add('ids', 'Kõik filtrid peavad olema järjestuses.');
                }
            },
        ];
    }
}
