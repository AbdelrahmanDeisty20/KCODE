<?php

namespace App\Http\Requests\API\Blog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogTagRequest extends FormRequest
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
        $tagId = $this->route('id') ?? $this->route('tag');
        if (is_object($tagId)) {
            $tagId = $tagId->id;
        }

        return [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_tags,slug,' . $tagId,
        ];
    }
}
