<?php

namespace App\Services;

use App\Models\Blog;
use Illuminate\Support\Str;

class BlogService
{
    public function __construct(protected SitemapService $sitemapService) {}

    /**
     * Get paginated list of published blogs.
     */
    public function index()
    {
        $blogs = Blog::published()
            ->with(['category', 'author', 'tags', 'seo'])
            ->latest('published_at')
            ->paginate(10);

        if ($blogs->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_blogs_found'),
                'data' => $blogs,
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.blogs_retrieved_successfully'),
            'data' => $blogs,
        ];
    }

    /**
     * Get blog details by slug or id, auto increment views, and attach related blogs.
     */
    public function show($slug)
    {
        $blog = Blog::published()
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug);
                if (is_numeric($slug)) {
                    $q->orWhere('id', $slug);
                }
            })
            ->with(['category', 'author', 'tags', 'seo'])
            ->first();

        if (!$blog) {
            return [
                'status' => false,
                'message' => __('messages.blog_not_found'),
                'data' => null,
            ];
        }

        // Increase views count
        $blog->increment('views');

        // Load 4 related blogs from the same category
        $relatedBlogs = new \Illuminate\Database\Eloquent\Collection();
        if ($blog->category_id) {
            $relatedBlogs = Blog::published()
                ->where('category_id', $blog->category_id)
                ->where('id', '!=', $blog->id)
                ->with(['category', 'author', 'tags', 'seo'])
                ->latest('published_at')
                ->take(4)
                ->get();
        }
        $blog->setRelation('relatedBlogs', $relatedBlogs);

        return [
            'status' => true,
            'message' => __('messages.blog_details_retrieved_successfully'),
            'data' => $blog,
        ];
    }

    /**
     * Get featured published blogs.
     */
    public function featured()
    {
        $blogs = Blog::published()
            ->featured()
            ->with(['category', 'author', 'tags', 'seo'])
            ->latest('published_at')
            ->paginate(10);

        if ($blogs->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_featured_blogs_found'),
                'data' => $blogs,
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.featured_blogs_retrieved_successfully'),
            'data' => $blogs,
        ];
    }

    /**
     * Get popular published blogs ordered by views.
     */
    public function popular()
    {
        $blogs = Blog::published()
            ->popular()
            ->with(['category', 'author', 'tags', 'seo'])
            ->paginate(10);

        if ($blogs->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_popular_blogs_found'),
                'data' => $blogs,
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.popular_blogs_retrieved_successfully'),
            'data' => $blogs,
        ];
    }

    /**
     * Search blogs by keyword, category, tag, and date.
     */
    public function search(array $filters = [])
    {
        $query = Blog::published()->with(['category', 'author', 'tags', 'seo']);

        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('title_ar', 'like', "%{$keyword}%")
                  ->orWhere('title_en', 'like', "%{$keyword}%")
                  ->orWhere('excerpt_ar', 'like', "%{$keyword}%")
                  ->orWhere('excerpt_en', 'like', "%{$keyword}%")
                  ->orWhere('content_ar', 'like', "%{$keyword}%")
                  ->orWhere('content_en', 'like', "%{$keyword}%")
                  ->orWhereHas('category', function ($cq) use ($keyword) {
                      $cq->where('name_ar', 'like', "%{$keyword}%")
                         ->orWhere('name_en', 'like', "%{$keyword}%")
                         ->orWhere('slug', 'like', "%{$keyword}%");
                  });
            });
        }

        if (!empty($filters['category'])) {
            $categoryParam = $filters['category'];
            $query->whereHas('category', function ($q) use ($categoryParam) {
                if (is_numeric($categoryParam)) {
                    $q->where('id', $categoryParam);
                } else {
                    $q->where('slug', $categoryParam);
                }
            });
        }

        if (!empty($filters['tag'])) {
            $tagParam = $filters['tag'];
            $query->whereHas('tags', function ($q) use ($tagParam) {
                if (is_numeric($tagParam)) {
                    $q->where('blog_tags.id', $tagParam);
                } else {
                    $q->where('blog_tags.slug', $tagParam);
                }
            });
        }

        if (!empty($filters['date'])) {
            $query->whereDate('published_at', $filters['date']);
        }

        $blogs = $query->latest('published_at')->paginate(10);

        if ($blogs->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_search_results_found'),
                'data' => $blogs,
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.search_results_retrieved_successfully'),
            'data' => $blogs,
        ];
    }

    /**
     * Store a new blog post.
     */
    public function store(array $data, ?int $authorId = null)
    {
        $authorId = $authorId ?? auth()->id() ?? 1;

        // Auto generate slug if missing
        if (empty($data['slug'])) {
            $titleForSlug = $data['title_en'] ?? $data['title_ar'] ?? 'blog';
            $data['slug'] = $this->generateUniqueSlug($titleForSlug);
        }

        // Auto calculate reading time
        $contentForReading = ($data['content_en'] ?? '') . ' ' . ($data['content_ar'] ?? '');
        $data['reading_time'] = $this->calculateReadingTime($contentForReading);

        // Handle featured image upload
        if (request()->hasFile('featured_image')) {
            $data['featured_image'] = request()->file('featured_image')->store('blogs', 'public');
        } elseif (isset($data['featured_image']) && is_object($data['featured_image']) && method_exists($data['featured_image'], 'store')) {
            $data['featured_image'] = $data['featured_image']->store('blogs', 'public');
        } elseif (empty($data['featured_image'])) {
            unset($data['featured_image']);
        }

        $data['author_id'] = $authorId;
        $data['status'] = $data['status'] ?? 'draft';
        $data['views'] = 0;
        $data['is_featured'] = !empty($data['is_featured']);
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $blog = Blog::create($data);

        // Sync tags if provided
        if (!empty($data['tags'])) {
            $blog->tags()->sync($data['tags']);
        }

        // Create SEO record (if provided, use custom; if omitted, generate smart fallbacks)
        $seoData = $data['seo'] ?? [];
        if (isset($seoData['og_image']) && is_object($seoData['og_image']) && method_exists($seoData['og_image'], 'store')) {
            $seoData['og_image'] = $seoData['og_image']->store('blogs/seo', 'public');
        } else {
            $seoData['og_image'] = $seoData['og_image'] ?? $blog->featured_image;
        }

        $seoData['meta_title_ar'] = $seoData['meta_title_ar'] ?? $blog->title_ar;
        $seoData['meta_title_en'] = $seoData['meta_title_en'] ?? $blog->title_en;
        $seoData['meta_description_ar'] = $seoData['meta_description_ar'] ?? $blog->excerpt_ar ?? Str::limit(strip_tags($blog->content_ar), 160);
        $seoData['meta_description_en'] = $seoData['meta_description_en'] ?? $blog->excerpt_en ?? Str::limit(strip_tags($blog->content_en), 160);
        $seoData['og_title_ar'] = $seoData['og_title_ar'] ?? $blog->title_ar;
        $seoData['og_title_en'] = $seoData['og_title_en'] ?? $blog->title_en;
        $seoData['og_description_ar'] = $seoData['og_description_ar'] ?? $blog->excerpt_ar ?? Str::limit(strip_tags($blog->content_ar), 160);
        $seoData['og_description_en'] = $seoData['og_description_en'] ?? $blog->excerpt_en ?? Str::limit(strip_tags($blog->content_en), 160);

        $blog->seo()->create($seoData);

        $this->sitemapService->saveToPublic();

        return [
            'status' => true,
            'message' => __('messages.blog_created_successfully'),
            'data' => $blog->load(['category', 'author', 'tags', 'seo']),
        ];
    }

    /**
     * Update an existing blog post.
     */
    public function update($id, array $data)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return [
                'status' => false,
                'message' => __('messages.blog_not_found'),
                'data' => null,
            ];
        }

        // Auto update slug if title changed and slug not provided
        if (empty($data['slug']) && (!empty($data['title_en']) || !empty($data['title_ar']))) {
            $titleForSlug = $data['title_en'] ?? $data['title_ar'];
            $data['slug'] = $this->generateUniqueSlug($titleForSlug, $blog->id);
        }

        // Recalculate reading time if content changed
        if (!empty($data['content_en']) || !empty($data['content_ar'])) {
            $contentForReading = ($data['content_en'] ?? $blog->content_en) . ' ' . ($data['content_ar'] ?? $blog->content_ar);
            $data['reading_time'] = $this->calculateReadingTime($contentForReading);
        }

        // Handle featured image upload
        if (request()->hasFile('featured_image')) {
            $data['featured_image'] = request()->file('featured_image')->store('blogs', 'public');
        } elseif (isset($data['featured_image']) && is_object($data['featured_image']) && method_exists($data['featured_image'], 'store')) {
            $data['featured_image'] = $data['featured_image']->store('blogs', 'public');
        } elseif (empty($data['featured_image'])) {
            unset($data['featured_image']);
        }

        if (isset($data['status']) && $data['status'] === 'published' && !$blog->published_at && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $blog->update($data);

        // Sync tags if provided
        if (isset($data['tags'])) {
            $blog->tags()->sync($data['tags']);
        }

        // Update SEO if provided
        if (!empty($data['seo'])) {
            $seoData = $data['seo'];
            if (isset($seoData['og_image']) && is_object($seoData['og_image']) && method_exists($seoData['og_image'], 'store')) {
                $seoData['og_image'] = $seoData['og_image']->store('blogs/seo', 'public');
            }
            $blog->seo()->updateOrCreate([], $seoData);
        }

        $this->sitemapService->saveToPublic();

        return [
            'status' => true,
            'message' => __('messages.blog_updated_successfully'),
            'data' => $blog->load(['category', 'author', 'tags', 'seo']),
        ];
    }

    /**
     * Delete (soft delete) a blog post.
     */
    public function destroy($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return [
                'status' => false,
                'message' => __('messages.blog_not_found'),
                'data' => null,
            ];
        }

        $blog->delete();

        $this->sitemapService->saveToPublic();

        return [
            'status' => true,
            'message' => __('messages.blog_deleted_successfully'),
            'data' => null,
        ];
    }

    /**
     * Change blog status to published.
     */
    public function publish($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return [
                'status' => false,
                'message' => __('messages.blog_not_found'),
                'data' => null,
            ];
        }

        $blog->update([
            'status' => 'published',
            'published_at' => $blog->published_at ?? now(),
        ]);

        $this->sitemapService->saveToPublic();

        return [
            'status' => true,
            'message' => __('messages.blog_published_successfully'),
            'data' => $blog->load(['category', 'author', 'tags', 'seo']),
        ];
    }

    /**
     * Change blog status to draft.
     */
    public function draft($id)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return [
                'status' => false,
                'message' => __('messages.blog_not_found'),
                'data' => null,
            ];
        }

        $blog->update(['status' => 'draft']);

        $this->sitemapService->saveToPublic();

        return [
            'status' => true,
            'message' => __('messages.blog_moved_to_draft_successfully'),
            'data' => $blog->load(['category', 'author', 'tags', 'seo']),
        ];
    }

    /**
     * Upload single featured image for blog.
     */
    public function uploadFeaturedImage($id, $file)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return [
                'status' => false,
                'message' => __('messages.blog_not_found'),
                'data' => null,
            ];
        }

        $path = $file->store('blogs', 'public');
        $blog->update(['featured_image' => $path]);

        return [
            'status' => true,
            'message' => __('messages.featured_image_uploaded_successfully'),
            'data' => $blog,
        ];
    }

    /**
     * Manage SEO settings for a blog.
     */
    public function manageSeo($id, array $seoData)
    {
        $blog = Blog::find($id);

        if (!$blog) {
            return [
                'status' => false,
                'message' => __('messages.blog_not_found'),
                'data' => null,
            ];
        }

        if (isset($seoData['og_image']) && is_object($seoData['og_image']) && method_exists($seoData['og_image'], 'store')) {
            $seoData['og_image'] = $seoData['og_image']->store('blogs/seo', 'public');
        }

        $seo = $blog->seo()->updateOrCreate([], $seoData);

        return [
            'status' => true,
            'message' => __('messages.seo_saved_successfully'),
            'data' => $seo,
        ];
    }

    /**
     * Helper to generate unique slug.
     */
    public function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $slug = Str::slug($title);

        if (empty($slug)) {
            $slug = preg_replace('/\s+/u', '-', trim($title));
            $slug = mb_strtolower($slug, 'UTF-8');
        }

        $originalSlug = $slug;
        $count = 1;

        while (
            Blog::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Helper to calculate reading time in minutes.
     */
    public function calculateReadingTime(string $content): int
    {
        $cleanContent = strip_tags($content);
        $wordCount = str_word_count($cleanContent);

        if ($wordCount === 0 && !empty($cleanContent)) {
            $words = array_filter(explode(' ', preg_replace('/\s+/', ' ', $cleanContent)));
            $wordCount = count($words);
        }

        return (int) max(1, ceil($wordCount / 200));
    }
}
