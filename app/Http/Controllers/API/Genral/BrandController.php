<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\BRAND\BrandResource;
use App\Services\BrandService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    use ApiResponse;

    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index()
    {
        $brands = $this->brandService->index();
        if (!$brands['status']) {
            return $this->error($brands['message'], 404);
        }
        return $this->paginated(BrandResource::class, $brands['data'], __('messages.brandsGetSuccessfully'));
    }
    public function show($id)
    {
        $brand = $this->brandService->show($id);
        if (!$brand['status']) {
            return $this->error($brand['message'], 404);
        }
        return $this->success(new BrandResource($brand['data']), __('messages.brandGetSuccessfully'));
    }
}
