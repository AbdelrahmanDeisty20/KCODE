<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\PRODUCT\ProductListResource;
use App\Services\OfferService;
use App\Traits\ApiResponse;

class OfferController extends Controller
{
    use ApiResponse;

    public function __construct(private OfferService $offerService) {}

    /**
     * Display a listing of products with active offers.
     */
    public function index()
    {
        $result = $this->offerService->getOfferProducts();
        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->paginated(ProductListResource::class, $result['data'], $result['message']);
    }
}
