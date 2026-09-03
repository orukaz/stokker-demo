<?php

namespace App\Http\Requests;

use App\Models\SavedFilter;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateSavedFilterRequest extends SavedFilterRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $savedFilter = $this->route('savedFilter');

        return $this->savedFilterRules(
            $savedFilter instanceof SavedFilter ? $savedFilter : null,
        );
    }
}
