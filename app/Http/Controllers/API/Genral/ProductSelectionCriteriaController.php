<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\ProductSelectionCriteria\ProductSelectionCriteriaResource;
use App\Services\ProductSelectionCriteriaService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ProductSelectionCriteriaController extends Controller
{
    use ApiResponse;

    public function __construct(private ProductSelectionCriteriaService $criteriaService) {}

    /**
     * Get product selection criteria & quality charter methodology.
     */
    public function index(): JsonResponse
    {
        $result = $this->criteriaService->getCriteria();

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        $data = [
            'modal_criteria'  => ProductSelectionCriteriaResource::collection($result['data']['modal_criteria']),
            'accordion_items' => ProductSelectionCriteriaResource::collection($result['data']['accordion_items']),
        ];

        return $this->success(
            $data,
            $result['message']
        );
    }
}
