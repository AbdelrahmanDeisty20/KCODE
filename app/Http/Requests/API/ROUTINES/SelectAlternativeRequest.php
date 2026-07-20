<?php

namespace App\Http\Requests\API\ROUTINES;

use Illuminate\Foundation\Http\FormRequest;

class SelectAlternativeRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'routine_step_id' => ['required', 'exists:routine_steps,id'],
            'product_id'      => ['required', 'exists:products,id'],
        ];
    }
}
