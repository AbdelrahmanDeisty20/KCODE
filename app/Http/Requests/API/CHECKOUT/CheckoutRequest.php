<?php

namespace App\Http\Requests\API\CHECKOUT;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            // User info
            'user_name'      => ['required', 'string', 'max:255'],
            'user_phone'          => ['required', 'string', 'max:50'],

            // Address ID (optional if new address fields are provided)
            'address_id'     => ['required', 'integer', 'exists:addresses,id'],

            // Inline Address fields (required if address_id is not passed)
            'country_id'     => ['required_without:address_id', 'nullable', 'integer', 'exists:countries,id'],
            'state_id'       => ['required', 'integer', 'exists:states,id'],
            'city_id'        => ['required_without:address_id', 'nullable', 'integer', 'exists:cities,id'],
            'address'        => ['required_without:address_id', 'nullable', 'string', 'max:500'],
            'street'         => ['nullable', 'string', 'max:255'],
            'title'          => ['nullable', 'string', 'max:100'],

            // Checkout options
            'session_id'     => ['required', 'string'],
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
            'address_id.exists'         => __('messages.invalid_address'),
            'country_id.required_without' => __('messages.country_required'),
            'city_id.required_without'    => __('messages.city_required'),
            'address.required_without'    => __('messages.address_required'),
            'user_name.required'        => __('validation.required'),
            'user_phone.required'       => __('validation.required'),
            'session_id.required'       => __('validation.required'),
            'payment_method.required'   => __('validation.required'),
        ];
    }
}
