<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Reviews\StoreReviewRequest;
use App\Http\Requests\API\Reviews\StoreWebsiteReviewRequest;
use App\Http\Requests\API\Reviews\UpdateReviewRequest;
use App\Http\Resources\API\Reviews\ReviewResource;
use App\Services\ReviewService;
use App\Traits\ApiResponse;

class ReviewController extends Controller
{
    use ApiResponse;

    public function __construct(private ReviewService $reviewService) {}

    /**
     * Get all reviews created by the authenticated user.
     */
    public function genralReview(){
        $result = $this->reviewService->genralReview();
        return $this->success($result['data'], $result['message']);
    }
    public function myReviews()
    {
        $userId = auth('sanctum')->id();
        $result = $this->reviewService->getUserReviews($userId);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(
            ReviewResource::collection($result['data']),
            $result['message']
        );
    }

    /**
     * Store a website-wide review (without product).
     */
    public function storeWebsiteReview(StoreWebsiteReviewRequest $request)
    {
        $userId = auth('sanctum')->id();
        $result = $this->reviewService->storeWebsiteReview($userId, $request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->created(
            ReviewResource::make($result['data']),
            $result['message']
        );
    }

    /**
     * Store a new product review.
     */
    public function store(StoreReviewRequest $request)
    {
        $userId = auth('sanctum')->id();
        $result = $this->reviewService->storeReview($userId, $request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->created(
            ReviewResource::make($result['data']),
            $result['message']
        );
    }

    /**
     * Update an existing product review.
     */
    public function update(UpdateReviewRequest $request, $id)
    {
        $userId = auth('sanctum')->id();
        $result = $this->reviewService->updateReview($userId, (int) $id, $request->validated());

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(
            ReviewResource::make($result['data']),
            $result['message']
        );
    }

    /**
     * Delete an existing product review.
     */
    public function destroy($id)
    {
        $userId = auth('sanctum')->id();
        $result = $this->reviewService->deleteReview($userId, (int) $id);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success([], $result['message']);
    }
}
