<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class ProductSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $term = $this->input('query') ?? $this->input('search') ?? $this->input('q') ?? '';
        $this->merge([
            'query' => is_string($term) ? trim($term) : '',
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'query'        => ['nullable', 'string'],
            'search'       => ['nullable', 'string'],
            'q'            => ['nullable', 'string'],
            'per_page'     => ['nullable', 'integer', 'min:1', 'max:100'],
            'skin_type_id' => ['nullable'],
            'concern_id'   => ['nullable'],
            'goal_id'      => ['nullable'],
        ];
    }
}
