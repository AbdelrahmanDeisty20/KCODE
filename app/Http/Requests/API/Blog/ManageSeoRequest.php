<?php

namespace App\Http\Requests\API\Blog;

use Illuminate\Foundation\Http\FormRequest;

class ManageSeoRequest extends FormRequest
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
            'meta_title_ar' => 'nullable|string|max:255',
            'meta_title_en' => 'nullable|string|max:255',
            'meta_description_ar' => 'nullable|string',
            'meta_description_en' => 'nullable|string',
            'meta_keywords_ar' => 'nullable|string',
            'meta_keywords_en' => 'nullable|string',
            'canonical_url' => 'nullable|url|max:255',
            'og_title_ar' => 'nullable|string|max:255',
            'og_title_en' => 'nullable|string|max:255',
            'og_description_ar' => 'nullable|string',
            'og_description_en' => 'nullable|string',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }
}
