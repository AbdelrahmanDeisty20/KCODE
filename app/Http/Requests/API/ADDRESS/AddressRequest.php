<?php

namespace App\Http\Requests\API\ADDRESS;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddressRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'country_id' => 'required|exists:countries,id',
            'state_id' => [
                'required',
                Rule::exists('states', 'id')->where(function ($query) {
                    $query->where('country_id', $this->country_id);
                }),
            ],
            'city_id' => [
                'required',
                Rule::exists('cities', 'id')->where(function ($query) {
                    $query->where('state_id', $this->state_id)
                          ->where('country_id', $this->country_id);
                }),
            ],
            'address' => 'required|string',
            'is_default' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'state_id.exists' => __('validation.state_must_belong_to_country'),
            'city_id.exists'  => __('validation.city_must_belong_to_state_and_country'),
        ];
    }
}
