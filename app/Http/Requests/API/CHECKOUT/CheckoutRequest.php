<?php

namespace App\Http\Requests\API\CHECKOUT;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
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
        $hasAddressId = $this->filled('address_id');

        return [
            // User info
            'user_name'      => ['required', 'string', 'max:255'],
            'user_phone'     => ['required', 'string', 'max:50'],

            // Address ID
            'address_id'     => ['nullable', 'integer', 'exists:addresses,id'],

            // Inline Address fields (Required ONLY if address_id is missing or empty)
            'country_id'     => [Rule::requiredIf(!$hasAddressId), 'nullable', 'integer', 'exists:countries,id'],
            'state_id'       => [
                Rule::requiredIf(!$hasAddressId),
                'nullable',
                'integer',
                Rule::exists('states', 'id')->where(function ($query) {
                    if ($this->filled('country_id')) {
                        $query->where('country_id', $this->country_id);
                    }
                }),
            ],
            'city_id'        => [
                Rule::requiredIf(!$hasAddressId),
                'nullable',
                'integer',
                Rule::exists('cities', 'id')->where(function ($query) {
                    if ($this->filled('state_id')) {
                        $query->where('state_id', $this->state_id);
                    }
                    if ($this->filled('country_id')) {
                        $query->where('country_id', $this->country_id);
                    }
                }),
            ],
            'address'        => [Rule::requiredIf(!$hasAddressId), 'nullable', 'string', 'max:500'],

            // Optional inline address fields
            'street'         => ['nullable', 'string', 'max:255'],
            'title'          => ['nullable', 'string', 'max:100'],

            // Checkout options
            'session_id'     => ['nullable', 'string'],
            'coupon_code'    => ['nullable', 'string'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'string', 'in:cash'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'address_id.exists'           => __('messages.invalid_address'),
            'country_id.required'         => __('messages.country_required'),
            'country_id.required_if'      => __('messages.country_required'),
            'country_id.required_without' => __('messages.country_required'),
            'state_id.required'           => __('messages.state_required'),
            'state_id.required_if'        => __('messages.state_required'),
            'state_id.required_without'   => __('messages.state_required'),
            'state_id.exists'             => __('validation.state_must_belong_to_country'),
            'city_id.required'            => __('messages.city_required'),
            'city_id.required_if'         => __('messages.city_required'),
            'city_id.required_without'    => __('messages.city_required'),
            'city_id.exists'              => __('validation.city_must_belong_to_state_and_country'),
            'address.required'            => __('messages.address_required'),
            'address.required_if'         => __('messages.address_required'),
            'address.required_without'    => __('messages.address_required'),
            'user_name.required'          => __('validation.required'),
            'user_phone.required'         => __('validation.required'),
            'payment_method.required'     => __('validation.required'),
        ];
    }
}
