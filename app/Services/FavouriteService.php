<?php

namespace App\Services;

use App\Models\Favourite;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class FavouriteService
{
    /**
     * Get all active favorites of the user.
     */
    public function getFavorites($userId)
    {
        try {
            $productIds = Favourite::where('user_id', $userId)
                ->where('is_active', true)
                ->pluck('product_id');

            $products = Product::whereIn('id', $productIds)
                ->with(['brand', 'subCategory', 'category', 'offers'])
                ->get();

            return [
                'status' => true,
                'message' => __('messages.favoritesRetrievedSuccessfully'),
                'data' => $products
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching favorites: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    /**
     * Add a product to the user's favorites.
     */
    public function addToFavorites($userId, $productId)
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                return [
                    'status' => false,
                    'message' => __('messages.product_not_found')
                ];
            }

            $favourite = Favourite::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($favourite && $favourite->is_active) {
                return [
                    'status' => false,
                    'message' => __('messages.productAlreadyInFavorites')
                ];
            }

            if ($favourite) {
                $favourite->update(['is_active' => true]);
            } else {
                Favourite::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'is_active' => true
                ]);
            }

            return [
                'status' => true,
                'message' => __('messages.productAddedToFavorites')
            ];
        } catch (\Exception $e) {
            Log::error('Error adding to favorites: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Remove a product from the user's favorites.
     */
    public function removeFromFavorites($userId, $productId)
    {
        try {
            $product = Product::find($productId);
            if (!$product) {
                return [
                    'status' => false,
                    'message' => __('messages.product_not_found')
                ];
            }

            $favourite = Favourite::where('user_id', $userId)
                ->where('product_id', $productId)
                ->where('is_active', true)
                ->first();

            if (!$favourite) {
                return [
                    'status' => false,
                    'message' => __('messages.productNotInFavorites')
                ];
            }

            $favourite->update(['is_active' => false]);

            return [
                'status' => true,
                'message' => __('messages.productRemovedFromFavorites')
            ];
        } catch (\Exception $e) {
            Log::error('Error removing from favorites: ' . $e->getMessage());
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
