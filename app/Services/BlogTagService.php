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
                'message' => 'No tags found',
                'data' => [],
            ];
        }

        return [
            'status' => true,
            'message' => 'Tags retrieved successfully',
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
                'message' => 'Tag not found',
                'data' => null,
            ];
        }

        $perPage = request('per_page', 15);

        $blogs = $tag->blogs()
            ->published()
            ->with(['category', 'author', 'tags', 'seo'])
            ->latest('published_at')
            ->paginate($perPage);

        return [
            'status' => true,
            'message' => 'Tag blogs retrieved successfully',
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
            'message' => 'Tag created successfully',
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
                'message' => 'Tag not found',
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
            'message' => 'Tag updated successfully',
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
                'message' => 'Tag not found',
                'data' => null,
            ];
        }

        $tag->delete();

        return [
            'status' => true,
            'message' => 'Tag deleted successfully',
            'data' => null,
        ];
    }
}
