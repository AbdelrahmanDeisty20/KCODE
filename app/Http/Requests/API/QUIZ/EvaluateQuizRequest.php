<?php

namespace App\Http\Requests\API\QUIZ;

use Illuminate\Foundation\Http\FormRequest;

class EvaluateQuizRequest extends FormRequest
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
     * Supports both legacy DB ID payloads and R479 direct contract codes.
     */
    public function rules(): array
    {
        return [
            // Legacy ID fields
            'skin_type_id'          => ['nullable', 'integer'],
            'routine_goal_id'       => ['nullable', 'integer'],
            'is_sensitive'          => ['nullable'],
            'concern_ids'           => ['nullable', 'array'],
            'concern_ids.*'         => ['nullable'],

            // R479 Direct Contract fields
            'primary'               => ['nullable', 'string'],
            'secondary'             => ['nullable'],
            'skin'                  => ['nullable', 'string'],
            'sensitive'             => ['nullable'],

            // R479 UI State fields
            'q1'                    => ['nullable', 'string'],
            'q4'                    => ['nullable'],
            'skin_type'             => ['nullable', 'string'],
            'sensitivity'           => ['nullable'],

            // Option ID fields
            'primary_option_id'     => ['nullable', 'integer'],
            'secondary_option_id'   => ['nullable', 'integer'],
            'skin_option_id'        => ['nullable', 'integer'],
            'sensitivity_option_id' => ['nullable', 'integer'],

            // Option IDs list (sent by frontend & audit suite)
            'option_ids'            => ['nullable', 'array'],
            'option_ids.*'          => ['nullable', 'integer'],
            'options'               => ['nullable', 'array'],
            'options.*'             => ['nullable'],
            'selected_options'      => ['nullable', 'array'],
            'selected_options.*'    => ['nullable', 'integer'],

            // Selected supplements for dynamic schedule calculation (F04)
            'selected_supplements'   => ['nullable', 'array'],
            'selected_supplements.*' => ['nullable', 'string'],
            'supplement_skus'        => ['nullable', 'array'],
            'supplement_skus.*'      => ['nullable', 'string'],

            // Nested answers object
            'answers'               => ['nullable', 'array'],
            'answers.primary'       => ['nullable', 'string'],
            'answers.secondary'     => ['nullable'],
            'answers.skin'          => ['nullable', 'string'],
            'answers.sensitive'     => ['nullable'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'skin_type_id.exists'    => __('messages.invalid_skin_type'),
            'routine_goal_id.exists' => __('messages.invalid_routine_goal'),
            'concern_ids.array'      => __('messages.concerns_must_be_array'),
        ];
    }
}
