<?php

namespace App\Services;

use App\Models\BlogTag;
use Illuminate\Support\Str;

class BlogTagService
{
    /**
     * Get all blog tags.
     */
    public function index()
    {
        $tags = BlogTag::all();

        if ($tags->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_blog_tags_found'),
                'data' => [],
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.blog_tags_retrieved_successfully'),
            'data' => $tags,
        ];
    }

    /**
     * Get blogs by tag slug or id.
     */
    public function blogs($slug)
    {
        $tag = BlogTag::where('slug', $slug)
            ->when(is_numeric($slug), fn($q) => $q->orWhere('id', $slug))
            ->first();

        if (!$tag) {
            return [
                'status' => false,
                'message' => __('messages.blog_tag_not_found'),
                'data' => null,
            ];
        }

        $blogs = $tag->blogs()
            ->published()
            ->with(['category', 'author', 'tags', 'seo'])
            ->latest('published_at')
            ->paginate(10);

        return [
            'status' => true,
            'message' => __('messages.tag_blogs_retrieved_successfully'),
            'data' => $blogs,
        ];
    }

    /**
     * Store a new blog tag.
     */
    public function store(array $data)
    {
        if (empty($data['slug'])) {
            $titleForSlug = $data['name_en'] ?? $data['name_ar'] ?? 'tag';
            $data['slug'] = Str::slug($titleForSlug);
        }

        $tag = BlogTag::create($data);

        return [
            'status' => true,
            'message' => __('messages.blog_tag_created_successfully'),
            'data' => $tag,
        ];
    }

    /**
     * Update an existing blog tag.
     */
    public function update($id, array $data)
    {
        $tag = BlogTag::find($id);

        if (!$tag) {
            return [
                'status' => false,
                'message' => __('messages.blog_tag_not_found'),
                'data' => null,
            ];
        }

        if (empty($data['slug']) && (!empty($data['name_en']) || !empty($data['name_ar']))) {
            $titleForSlug = $data['name_en'] ?? $data['name_ar'];
            $data['slug'] = Str::slug($titleForSlug);
        }

        $tag->update($data);

        return [
            'status' => true,
            'message' => __('messages.blog_tag_updated_successfully'),
            'data' => $tag,
        ];
    }

    /**
     * Delete a blog tag.
     */
    public function destroy($id)
    {
        $tag = BlogTag::find($id);

        if (!$tag) {
            return [
                'status' => false,
                'message' => __('messages.blog_tag_not_found'),
                'data' => null,
            ];
        }

        $tag->delete();

        return [
            'status' => true,
            'message' => __('messages.blog_tag_deleted_successfully'),
            'data' => null,
        ];
    }
}
