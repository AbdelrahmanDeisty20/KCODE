<?php

namespace App\Http\Controllers\API\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Blog\StoreBlogTagRequest;
use App\Http\Requests\API\Blog\UpdateBlogTagRequest;
use App\Http\Resources\API\Blog\BlogListResource;
use App\Http\Resources\API\Blog\TagResource;
use App\Services\BlogTagService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class BlogTagController extends Controller
{
    use ApiResponse;

    public function __construct(private BlogTagService $tagService) {}

    /**
     * Display all blog tags.
     * GET /blog-tags
     */
    public function index(): JsonResponse
    {
        $result = $this->tagService->index();

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(TagResource::collection($result['data']), $result['message']);
    }

    /**
     * Display blogs associated with a specific tag.
     * GET /blog-tags/{slug}/blogs
     */
    public function blogs(string $slug): JsonResponse
    {
        $result = $this->tagService->blogs($slug);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->paginated(BlogListResource::class, $result['data'], $result['message']);
    }

    /**
     * Store a new blog tag.
     * POST /blog-tags
     */
    public function store(StoreBlogTagRequest $request): JsonResponse
    {
        $result = $this->tagService->store($request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->created(new TagResource($result['data']), $result['message']);
    }

    /**
     * Update an existing blog tag.
     * PUT/POST /blog-tags/{id}
     */
    public function update(UpdateBlogTagRequest $request, $id): JsonResponse
    {
        $result = $this->tagService->update($id, $request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(new TagResource($result['data']), $result['message']);
    }

    /**
     * Delete a blog tag.
     * DELETE /blog-tags/{id}
     */
    public function destroy($id): JsonResponse
    {
        $result = $this->tagService->destroy($id);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->deleted($result['message']);
    }
}
