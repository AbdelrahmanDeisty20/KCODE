<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\CATEGORY\CategoryResource;
use App\Http\Resources\API\CATEGORY\SubCategoryResource;
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
    public function sub_categories() {
        $sub_categories = $this->categoryService->sub_categories();
        if (!$sub_categories['status']) {
            return $this->error($sub_categories['message'], 404);
        }
        return $this->paginated(SubCategoryResource::class, $sub_categories['data'], __('messages.sub_categories_retrieved_successfully'));
    }
}
