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
        $address = \App\Models\Address::find($this->route('id'));
        $countryId = $this->country_id ?? ($address ? $address->country_id : null);
        $stateId = $this->state_id ?? ($address ? $address->state_id : null);

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
                'required_with:country_id',
                'integer',
                Rule::exists('states', 'id')->where(function ($query) use ($countryId) {
                    $query->where('country_id', $countryId);
                }),
            ],
            'city_id' => [
                'nullable',
                'required_with:country_id,state_id',
                'integer',
                Rule::exists('cities', 'id')->where(function ($query) use ($countryId, $stateId) {
                    $query->where('country_id', $countryId)
                          ->where('state_id', $stateId);
                }),
            ],
            'address' => 'nullable|string',
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
