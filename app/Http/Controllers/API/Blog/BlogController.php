<?php

namespace App\Http\Controllers\API\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Blog\ManageSeoRequest;
use App\Http\Requests\API\Blog\SearchBlogRequest;
use App\Http\Requests\API\Blog\StoreBlogRequest;
use App\Http\Requests\API\Blog\UpdateBlogRequest;
use App\Http\Requests\API\Blog\UploadFeaturedImageRequest;
use App\Http\Resources\API\Blog\BlogDetailsResource;
use App\Http\Resources\API\Blog\BlogListResource;
use App\Http\Resources\API\Blog\SeoResource;
use App\Services\BlogService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    use ApiResponse;

    public function __construct(private BlogService $blogService) {}

    /**
     * Display a paginated listing of published blogs.
     * GET /blogs
     */
    public function index(): JsonResponse
    {
        $result = $this->blogService->index();

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->paginated(BlogListResource::class, $result['data'], $result['message']);
    }

    /**
     * Display the specified blog by slug.
     * GET /blogs/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $result = $this->blogService->show($slug);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(new BlogDetailsResource($result['data']), $result['message']);
    }

    /**
     * Display featured published blogs.
     * GET /blogs/featured
     */
    public function featured(): JsonResponse
    {
        $result = $this->blogService->featured();

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->paginated(BlogListResource::class, $result['data'], $result['message']);
    }

    /**
     * Display popular published blogs ordered by views.
     * GET /blogs/popular
     */
    public function popular(): JsonResponse
    {
        $result = $this->blogService->popular();

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->paginated(BlogListResource::class, $result['data'], $result['message']);
    }

    /**
     * Search blogs by keyword, category, tag, or date.
     * GET /blogs/search
     */
    public function search(SearchBlogRequest $request): JsonResponse
    {
        $result = $this->blogService->search($request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->paginated(BlogListResource::class, $result['data'], $result['message']);
    }

    /**
     * Store a new blog post.
     * POST /blogs
     */
    public function store(StoreBlogRequest $request): JsonResponse
    {
        $result = $this->blogService->store($request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->created(new BlogDetailsResource($result['data']), $result['message']);
    }

    /**
     * Update an existing blog post.
     * PUT/POST /blogs/{id}
     */
    public function update(UpdateBlogRequest $request, $id): JsonResponse
    {
        $result = $this->blogService->update($id, $request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(new BlogDetailsResource($result['data']), $result['message']);
    }

    /**
     * Delete (soft delete) a blog post.
     * DELETE /blogs/{id}
     */
    public function destroy($id): JsonResponse
    {
        $result = $this->blogService->destroy($id);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->deleted($result['message']);
    }

    /**
     * Publish a blog post.
     * POST /blogs/{id}/publish
     */
    public function publish($id): JsonResponse
    {
        $result = $this->blogService->publish($id);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(new BlogDetailsResource($result['data']), $result['message']);
    }

    /**
     * Draft a blog post.
     * POST /blogs/{id}/draft
     */
    public function draft($id): JsonResponse
    {
        $result = $this->blogService->draft($id);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(new BlogDetailsResource($result['data']), $result['message']);
    }

    /**
     * Upload single featured image for a blog.
     * POST /blogs/{id}/upload-featured-image
     */
    public function uploadFeaturedImage(UploadFeaturedImageRequest $request, $id): JsonResponse
    {
        $file = $request->file('featured_image');
        $result = $this->blogService->uploadFeaturedImage($id, $file);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(new BlogDetailsResource($result['data']), $result['message']);
    }

    /**
     * Manage SEO settings for a blog post.
     * POST /blogs/{id}/seo
     */
    public function manageSeo(ManageSeoRequest $request, $id): JsonResponse
    {
        $result = $this->blogService->manageSeo($id, $request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(new SeoResource($result['data']), $result['message']);
    }
}
