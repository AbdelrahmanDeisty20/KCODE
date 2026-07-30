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
            'meta_title_ar' => 'required|string|max:255',
            'meta_title_en' => 'required|string|max:255',
            'meta_description_ar' => 'required|string',
            'meta_description_en' => 'required|string',
            'meta_keywords_ar' => 'required|string',
            'meta_keywords_en' => 'required|string',
            'canonical_url' => 'nullable|url|max:255',
            'og_title_ar' => 'required|string|max:255',
            'og_title_en' => 'required|string|max:255',
            'og_description_ar' => 'required|string',
            'og_description_en' => 'required|string',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }

    /**
     * Get custom attribute names for localized validation errors.
     */
    public function attributes(): array
    {
        return [
            'meta_title_ar' => __('messages.meta_title_ar'),
            'meta_title_en' => __('messages.meta_title_en'),
            'meta_description_ar' => __('messages.meta_description_ar'),
            'meta_description_en' => __('messages.meta_description_en'),
            'meta_keywords_ar' => __('messages.meta_keywords_ar'),
            'meta_keywords_en' => __('messages.meta_keywords_en'),
            'canonical_url' => __('messages.canonical_url'),
            'og_title_ar' => __('messages.og_title_ar'),
            'og_title_en' => __('messages.og_title_en'),
            'og_description_ar' => __('messages.og_description_ar'),
            'og_description_en' => __('messages.og_description_en'),
        ];
    }
}
