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
        $result = $this->skinService->show($id);
        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }

        return response()->json([
            'status'     => true,
            'message'    => $result['message'],
            'skin_type'  => new SkinTypeResource($result['skin_type']),
            'data'       => ProductListResource::collection($result['data']),
            'pagination' => [
                'total'        => $result['data']->total(),
                'count'        => $result['data']->count(),
                'per_page'     => $result['data']->perPage(),
                'current_page' => $result['data']->currentPage(),
                'total_pages'  => $result['data']->lastPage(),
            ]
        ], 200);
    }
}
