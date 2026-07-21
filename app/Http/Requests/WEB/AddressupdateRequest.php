<?php

namespace App\Http\Requests\WEB;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressupdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
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
            'title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'country_id' => [
                'nullable',
                'integer',
                Rule::exists('countries', 'id'),
            ],
            'state_id' => [
                'nullable',
                'integer',
                'exists:states,id',
                Rule::exists('states', 'id')->where(function ($query) {
                    $query->where('country_id', $this->country_id);
                }),
            ],
            'city_id' => [
                'required',
                'integer',
                Rule::exists('cities', 'id')
                    ->where('country_id', $this->country_id)
                    ->where('state_id', $this->state_id),
            ],
            'address' => 'required|string',
            'is_default' => 'nullable|boolean',
        ];
    }
}
