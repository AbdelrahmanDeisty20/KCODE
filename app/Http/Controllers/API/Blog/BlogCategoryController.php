<?php

namespace App\Http\Controllers\API\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Blog\StoreBlogCategoryRequest;
use App\Http\Requests\API\Blog\UpdateBlogCategoryRequest;
use App\Http\Resources\API\Blog\BlogListResource;
use App\Http\Resources\API\Blog\CategoryResource;
use App\Services\BlogCategoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class BlogCategoryController extends Controller
{
    use ApiResponse;

    public function __construct(private BlogCategoryService $categoryService) {}

    /**
     * Display all blog categories.
     * GET /blog-categories
     */
    public function index(): JsonResponse
    {
        $result = $this->categoryService->index();

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(CategoryResource::collection($result['data']), $result['message']);
    }

    /**
     * Display blogs belonging to a specific category.
     * GET /blog-categories/{slug}/blogs
     */
    public function blogs(string $slug): JsonResponse
    {
        $result = $this->categoryService->blogs($slug);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->paginated(BlogListResource::class, $result['data'], $result['message']);
    }

    /**
     * Store a new blog category.
     * POST /blog-categories
     */
    public function store(StoreBlogCategoryRequest $request): JsonResponse
    {
        $result = $this->categoryService->store($request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->created(new CategoryResource($result['data']), $result['message']);
    }

    /**
     * Update an existing blog category.
     * PUT/POST /blog-categories/{id}
     */
    public function update(UpdateBlogCategoryRequest $request, $id): JsonResponse
    {
        $result = $this->categoryService->update($id, $request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(new CategoryResource($result['data']), $result['message']);
    }

    /**
     * Delete a blog category.
     * DELETE /blog-categories/{id}
     */
    public function destroy($id): JsonResponse
    {
        $result = $this->categoryService->destroy($id);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->deleted($result['message']);
    }
}
