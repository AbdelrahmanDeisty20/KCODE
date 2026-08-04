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
            'name_ar' => 'sometimes|nullable|string|max:255',
            'name_en' => 'sometimes|nullable|string|max:255',
            'name' => 'sometimes|nullable|string|max:255',
            'title_ar' => 'sometimes|nullable|string|max:255',
            'title_en' => 'sometimes|nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs,slug,' . $blogId,
            'excerpt_ar' => 'sometimes|nullable|string',
            'excerpt_en' => 'sometimes|nullable|string',
            'content_ar' => 'sometimes|nullable|string',
            'content_en' => 'sometimes|nullable|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'category_id' => 'sometimes|nullable|exists:blog_categories,id',
            'tags' => 'sometimes|nullable|array',
            'tags.*' => 'nullable|exists:blog_tags,id',
            'status' => 'nullable|in:draft,published',
            'is_featured' => 'nullable|boolean',
            'published_at' => 'nullable|date',

            // SEO Object (All Optional during update)
            'seo' => 'nullable|array',
            'seo.meta_title_ar' => 'nullable|string|max:255',
            'seo.meta_title_en' => 'nullable|string|max:255',
            'seo.meta_description_ar' => 'nullable|string',
            'seo.meta_description_en' => 'nullable|string',
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

    /**
     * Get custom attribute names for localized validation errors.
     */
    public function attributes(): array
    {
        return [
            'title_ar' => __('messages.title_ar'),
            'title_en' => __('messages.title_en'),
            'excerpt_ar' => __('messages.excerpt_ar'),
            'excerpt_en' => __('messages.excerpt_en'),
            'content_ar' => __('messages.content_ar'),
            'content_en' => __('messages.content_en'),
            'featured_image' => __('messages.featured_image'),
            'category_id' => __('messages.category_id'),
            'tags' => __('messages.tags'),
            'seo' => 'SEO',
            'seo.meta_title_ar' => __('messages.meta_title_ar'),
            'seo.meta_title_en' => __('messages.meta_title_en'),
            'seo.meta_description_ar' => __('messages.meta_description_ar'),
            'seo.meta_description_en' => __('messages.meta_description_en'),
            'seo.meta_keywords_ar' => __('messages.meta_keywords_ar'),
            'seo.meta_keywords_en' => __('messages.meta_keywords_en'),
            'seo.canonical_url' => __('messages.canonical_url'),
            'seo.og_title_ar' => __('messages.og_title_ar'),
            'seo.og_title_en' => __('messages.og_title_en'),
            'seo.og_description_ar' => __('messages.og_description_ar'),
            'seo.og_description_en' => __('messages.og_description_en'),
        ];
    }
}
