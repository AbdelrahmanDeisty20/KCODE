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
     */
    public function rules(): array
    {
        return [
            'skin_type_id' => ['required', 'exists:skin_types,id'],
            'routine_goal_id' => ['required', 'exists:routine_goals,id'],
            'concern_ids' => ['nullable', 'array'],
            'concern_ids.*' => ['exists:concerns,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'skin_type_id.required' => __('messages.skin_type_required'),
            'skin_type_id.exists' => __('messages.invalid_skin_type'),
            'routine_goal_id.required' => __('messages.routine_goal_required'),
            'routine_goal_id.exists' => __('messages.invalid_routine_goal'),
            'concern_ids.array' => __('messages.concerns_must_be_array'),
            'concern_ids.*.exists' => __('messages.invalid_concern'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'skin_type_id' => __('messages.skin_type'),
            'routine_goal_id' => __('messages.routine_goal'),
            'concern_ids' => __('messages.concerns'),
        ];
    }

    }
}
