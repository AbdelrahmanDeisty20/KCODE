<?php

namespace App\Services;

use App\Http\Resources\API\Reviews\ReviewResource;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ReviewService
{
    /**
     * Store a new product review.
     */
    public function genralReview(array $filters = [])
    {
        $query = Review::whereNull('product_id')->with('user');

        // ?featured=1 → only featured reviews (for homepage section)
        if (!empty($filters['featured'])) {
            $query->where('featured', true);
        }

        // ?limit=4 → take N reviews (no pagination)
        $paginated = empty($filters['limit']);

        if (!$paginated) {
            $limit   = min(max((int) $filters['limit'], 1), 50);
            $reviews = $query->orderByDesc('rating')->orderByDesc('created_at')
                ->limit($limit)->get();
        } else {
            $reviews = $query->orderByDesc('created_at')->paginate(10);
        }

        return [
            'status'    => true,
            'paginated' => $paginated,
            'message'   => $reviews->isEmpty()
                ? __('messages.noReviewsFound')
                : __('messages.reviewsRetrievedSuccessfully'),
            'data'      => $reviews,
        ];
    }
    public function storeReview(int $userId, array $data)
    {

        try {
            $productId = $data['product_id'];

            // 1. Check if user already reviewed this product
            $existing = Review::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($existing) {
                return [
                    'status' => false,
                    'message' => __('messages.reviewAlreadyExists'),
                    'data' => []
                ];
            }

            // 2. Create the review
            $review = Review::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);

            (new LoyaltyService())->addPoints(
                $userId,
                25,
                'review',
                $review->id,
                'نقاط كسب من كتابة تقييم',
                'Points earned for writing a review'
            );

            return [
                'status' => true,
                'message' => __('messages.reviewCreatedSuccessfully'),
                'data' => ReviewResource::make($review->load('user', 'product'))
            ];
        } catch (\Exception $e) {
            Log::error('Error storing review: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Update an existing product review.
     */
    public function updateReview(int $userId, int $reviewId, array $data)
    {
        try {
            $review = Review::find($reviewId);

            if (!$review) {
                return [
                    'status' => false,
                    'message' => __('messages.reviewNotFound'),
                    'data' => []
                ];
            }

            // Enforce ownership
            if ($review->user_id !== $userId) {
                return [
                    'status' => false,
                    'message' => __('messages.reviewNotOwned'),
                    'data' => []
                ];
            }

            $review->update([
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);

            return [
                'status' => true,
                'message' => __('messages.reviewUpdatedSuccessfully'),
                'data' => $review->load('user', 'product')
            ];
        } catch (\Exception $e) {
            Log::error('Error updating review: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Delete a product review.
     */
    public function deleteReview(int $userId, int $reviewId)
    {
        try {
            $review = Review::find($reviewId);

            if (!$review) {
                return [
                    'status' => false,
                    'message' => __('messages.reviewNotFound')
                ];
            }

            // Enforce ownership
            if ($review->user_id !== $userId) {
                return [
                    'status' => false,
                    'message' => __('messages.reviewNotOwned')
                ];
            }

            $review->delete();

            return [
                'status' => true,
                'message' => __('messages.reviewDeletedSuccessfully')
            ];
        } catch (\Exception $e) {
            Log::error('Error deleting review: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all reviews created by the user.
     */
    public function getUserReviews(int $userId)
    {
        try {
            $reviews = Review::where('user_id', $userId)
                ->with(['product', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            return [
                'status' => true,
                'message' => __('messages.myReviewsRetrievedSuccessfully'),
                'data' => $reviews
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching user reviews: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Store a website-wide review (without product_id).
     */
    public function storeWebsiteReview(int $userId, array $data)
    {
        try {
            // Check if user already reviewed the website
            $existing = Review::where('user_id', $userId)
                ->whereNull('product_id')
                ->first();

            if ($existing) {
                return [
                    'status' => false,
                    'message' => __('messages.websiteReviewAlreadyExists'),
                    'data' => []
                ];
            }

            $review = Review::create([
                'user_id' => $userId,
                'product_id' => null,
                'rating' => $data['rating'],
                'comment' => $data['comment'] ?? null,
            ]);

            (new LoyaltyService())->addPoints(
                $userId,
                25,
                'review',
                $review->id,
                'نقاط كسب من كتابة تقييم للموقع',
                'Points earned for writing a website review'
            );

            return [
                'status' => true,
                'message' => __('messages.websiteReviewCreatedSuccessfully'),
                'data' => $review->load('user')
            ];
        } catch (\Exception $e) {
            Log::error('Error storing website review: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }
}
