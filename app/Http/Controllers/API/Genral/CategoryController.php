<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\CATEGORY\CategoryResource;
use App\Services\CategoryService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponse;
    private $categoryService;
    public function __construct(CategoryService $categoryService) {
        $this->categoryService = $categoryService;
    }
    public function index() {
        $categories = $this->categoryService->index();
        if (!$categories['status']) {
            return $this->error($categories['message'], 404);
        }
        return $this->paginated(CategoryResource::class, $categories['data'], __('messages.categories_retrieved_successfully'));
    }
    public function show($id) {
        $category = $this->categoryService->show($id);
        if (!$category['status']) {
            return $this->error($category['message'], 404);
        }
        return $this->success(new CategoryResource($category['data']), __('messages.category_retrieved_successfully'));
    }
}
