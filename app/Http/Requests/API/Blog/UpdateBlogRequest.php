<?php

namespace App\Http\Requests\API\Blog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
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
        $blogId = $this->route('id') ?? $this->route('blog');
        if (is_object($blogId)) {
            $blogId = $blogId->id;
        }

        return [
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs,slug,' . $blogId,
            'excerpt_ar' => 'nullable|string',
            'excerpt_en' => 'nullable|string',
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'category_id' => 'required|exists:blog_categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:blog_tags,id',
            'status' => 'nullable|in:draft,published',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',

            // SEO fields (Required)
            'seo' => 'required|array',
            'seo.meta_title_ar' => 'required|string|max:255',
            'seo.meta_title_en' => 'required|string|max:255',
            'seo.meta_description_ar' => 'required|string',
            'seo.meta_description_en' => 'required|string',
            'seo.meta_keywords_ar' => 'nullable|string',
            'seo.meta_keywords_en' => 'nullable|string',
            'seo.canonical_url' => 'nullable|url|max:255',
            'seo.og_title_ar' => 'nullable|string|max:255',
            'seo.og_title_en' => 'nullable|string|max:255',
            'seo.og_description_ar' => 'nullable|string',
            'seo.og_description_en' => 'nullable|string',
            'seo.og_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ];
    }
}
