<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\PRODUCT\ProductListResource;
use App\Http\Resources\API\Skins\SkinTypeResource;
use App\Services\SkinService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class SkinController extends Controller
{
    use ApiResponse;
    private $skinService;
    public function __construct(SkinService $skinService) {
        $this->skinService = $skinService;
    }
    public function SkinTypes() {
        $skinTypes = $this->skinService->SkinTypes();
        if (!$skinTypes['status']) {
            return $this->error($skinTypes['message'], 404);
        }
        return $this->paginated(SkinTypeResource::class, $skinTypes['data'], __('messages.skin_types_retrieved_successfully'));
    }
    public function show($id) {
        $skinType = $this->skinService->show($id);
        if (!$skinType['status']) {
            return $this->error($skinType['message'], 404);
        }
        return $this->paginated(ProductListResource::class, $skinType['data'], __('messages.products_retrieved_successfully'));
    }
}
