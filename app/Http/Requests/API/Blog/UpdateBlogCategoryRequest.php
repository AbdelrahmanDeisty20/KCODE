<?php

namespace App\Http\Requests\API\Blog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogCategoryRequest extends FormRequest
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
        $categoryId = $this->route('id') ?? $this->route('category');
        if (is_object($categoryId)) {
            $categoryId = $categoryId->id;
        }

        return [
            'name_ar' => 'sometimes|required|string|max:255',
            'name_en' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug,' . $categoryId,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ];
    }
}
