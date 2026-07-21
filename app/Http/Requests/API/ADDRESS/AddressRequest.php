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
            'title' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
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
                Rule::unique('addresses', 'city_id')
                    ->where('user_id', auth()->id())
                    ->ignore($this->route('id')),
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
            'city_id.unique'  => __('validation.city_already_exists_in_addresses'),
            'title.required' => __('validation.required'),
            'phone.required' => __('validation.required'),
            'country_id.required' => __('validation.required'),
            'state_id.required' => __('validation.required'),
            'city_id.required' => __('validation.required'),
            'address.required' => __('validation.required'),
            'title.max' => __('validation.max'),
            'phone.max' => __('validation.max'),
            'address.max' => __('validation.max'),
            'title.string' => __('validation.string'),
            'phone.string' => __('validation.string'),
            'address.string' => __('validation.string'),
            
        ];
    }
}
