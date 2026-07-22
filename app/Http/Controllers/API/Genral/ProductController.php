<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\PRODUCT\ProductListResource;
use App\Http\Resources\API\PRODUCT\ProductResource;
use App\Services\ProductService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Requests\API\GENERAL\ProductFilterRequest;
use App\Http\Requests\API\GENERAL\ProductSearchRequest;

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
    public function show($id) {
        $product = $this->productService->show($id);
        if (!$product['status']) {
            return $this->error($product['message']);
        }
        return $this->success(new ProductResource($product['data']), $product['message']);
    }

    public function alternatives($id) {
        $result = $this->productService->alternatives($id);
        if (!$result['status']) {
            return $this->error($result['message']);
        }
        return $this->success(ProductListResource::collection($result['data']), $result['message']);
    }

    public function bestSellers() {
        $products = $this->productService->bestSellers();
        if (!$products['status']) {
            return $this->error($products['message']);
        }
        return $this->paginated(ProductListResource::class, $products['data'], $products['message']);
    }

    public function bySkinType($skinTypeId) {
        $products = $this->productService->bySkinType($skinTypeId);
        if (!$products['status']) {
            return $this->error($products['message']);
        }
        return $this->paginated(ProductListResource::class, $products['data'], $products['message']);
    }

    public function byGoal($goalId) {
        $products = $this->productService->byGoal($goalId);
        if (!$products['status']) {
            return $this->error($products['message']);
        }
        return $this->paginated(ProductListResource::class, $products['data'], $products['message']);
    }

    public function filter(ProductFilterRequest $request) {
        $products = $this->productService->filter($request->validated());
        if (!$products['status']) {
            return $this->error($products['message']);
        }
        return $this->paginated(ProductListResource::class, $products['data'], $products['message']);
    }

    public function search(ProductSearchRequest $request) {
        $products = $this->productService->search($request->validated());
        if (!$products['status']) {
            return $this->error($products['message']);
        }
        return $this->paginated(ProductListResource::class, $products['data'], $products['message']);
    }
}
