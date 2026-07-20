<?php

namespace App\Http\Requests\API\GENERAL;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
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
            'category_id' => ['nullable', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'exists:sub_categories,id'],
            'brand_id' => ['nullable'],
            'brand_id.*' => ['exists:brands,id'],
            'skin_type_id' => ['nullable'],
            'skin_type_id.*' => ['exists:skin_types,id'],
            'goal_id' => ['nullable'],
            'goal_id.*' => ['exists:routine_goals,id'],
            'concern_id' => ['nullable'],
            'concern_id.*' => ['exists:concerns,id'],
            'is_best_seller' => ['nullable', 'boolean'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'ratings' => ['nullable', 'numeric', 'min:1', 'max:5'],
            'sort' => ['nullable', 'string', 'in:min_price,max_price,best_sellers,sales_count,ratings,latest'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
