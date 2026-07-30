<?php

namespace App\Services;

use App\Models\BlogCategory;
use Illuminate\Support\Str;

class BlogCategoryService
{
    /**
     * Get all blog categories.
     */
    public function index()
    {
        $categories = BlogCategory::withCount(['blogs' => fn($q) => $q->published()])->get();

        if ($categories->isEmpty()) {
            return [
                'status' => false,
                'message' => 'No categories found',
                'data' => [],
            ];
        }

        return [
            'status' => true,
            'message' => 'Categories retrieved successfully',
            'data' => $categories,
        ];
    }

    /**
     * Get blogs by category slug or id.
     */
    public function blogs($slug)
    {
        $category = BlogCategory::where('slug', $slug)
            ->when(is_numeric($slug), fn($q) => $q->orWhere('id', $slug))
            ->first();

        if (!$category) {
            return [
                'status' => false,
                'message' => 'Category not found',
                'data' => null,
            ];
        }

        $perPage = request('per_page', 15);

        $blogs = $category->blogs()
            ->published()
            ->with(['category', 'author', 'tags', 'seo'])
            ->latest('published_at')
            ->paginate($perPage);

        return [
            'status' => true,
            'message' => 'Category blogs retrieved successfully',
            'data' => $blogs,
        ];
    }

    /**
     * Store a new blog category.
     */
    public function store(array $data)
    {
        if (empty($data['slug'])) {
            $titleForSlug = $data['name_en'] ?? $data['name_ar'] ?? 'category';
            $data['slug'] = Str::slug($titleForSlug);
        }

        if (isset($data['image']) && is_object($data['image']) && method_exists($data['image'], 'store')) {
            $data['image'] = $data['image']->store('blog-categories', 'public');
        }

        $category = BlogCategory::create($data);

        return [
            'status' => true,
            'message' => 'Category created successfully',
            'data' => $category,
        ];
    }

    /**
     * Update an existing blog category.
     */
    public function update($id, array $data)
    {
        $category = BlogCategory::find($id);

        if (!$category) {
            return [
                'status' => false,
                'message' => 'Category not found',
                'data' => null,
            ];
        }

        if (empty($data['slug']) && (!empty($data['name_en']) || !empty($data['name_ar']))) {
            $titleForSlug = $data['name_en'] ?? $data['name_ar'];
            $data['slug'] = Str::slug($titleForSlug);
        }

        if (isset($data['image']) && is_object($data['image']) && method_exists($data['image'], 'store')) {
            $data['image'] = $data['image']->store('blog-categories', 'public');
        }

        $category->update($data);

        return [
            'status' => true,
            'message' => 'Category updated successfully',
            'data' => $category,
        ];
    }

    /**
     * Delete a blog category.
     */
    public function destroy($id)
    {
        $category = BlogCategory::find($id);

        if (!$category) {
            return [
                'status' => false,
                'message' => 'Category not found',
                'data' => null,
            ];
        }

        $category->delete();

        return [
            'status' => true,
            'message' => 'Category deleted successfully',
            'data' => null,
        ];
    }
}
