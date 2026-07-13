<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\PRODUCT\ProductListResource;
use App\Services\ProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponse;
    public function __construct(private ProductService $productService) {}

    public function index() {
        $products = $this->productService->index();
        if (!$products['status']) {
            return $this->error($products['message']);
        }
        return $this->paginated(ProductListResource::class,$products['data'], $products['message']);
    }
}
