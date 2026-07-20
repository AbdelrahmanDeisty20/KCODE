<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\PRODUCT\ProductListResource;
use App\Services\FavouriteService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    use ApiResponse;

    public function __construct(private FavouriteService $favouriteService) {}

    /**
     * Get all favorites for the authenticated user.
     */
    public function index()
    {
        $userId = auth('sanctum')->id();
        $result = $this->favouriteService->getFavorites($userId);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(
            ProductListResource::collection($result['data']),
            $result['message']
        );
    }

    /**
     * Add a product to the user's favorites.
     */
    public function add($productId)
    {
        $userId = auth('sanctum')->id();
        $result = $this->favouriteService->addToFavorites($userId,  $productId);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success([], $result['message']);
    }

    /**
     * Remove a product from the user's favorites.
     */
    public function remove($productId)
    {
        $userId = auth('sanctum')->id();
        $result = $this->favouriteService->removeFromFavorites($userId, $productId);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success([], $result['message']);
    }
}
