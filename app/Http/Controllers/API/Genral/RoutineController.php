<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Services\RoutineService;
use App\Http\Resources\API\QUIZ\FinalRoutineResource;
use App\Http\Resources\API\QUIZ\RoutineResource;
use App\Http\Resources\API\PRODUCT\ProductListResource;
use App\Traits\ApiResponse;

class RoutineController extends Controller
{
    use ApiResponse;

    protected $routineService;

    public function __construct(RoutineService $routineService)
    {
        $this->routineService = $routineService;
    }

    /**
     * Get user's saved routine.
     */
    public function getRoutine()
    {
        $result = $this->routineService->getUserRoutine();

        if (!$result['status']) {
            $code = $result['code'] ?? 400;
            return $this->error($result['message'], $code);
        }

        return $this->success(
            FinalRoutineResource::collection($result['data']),
            $result['message']
        );
    }

    /**
     * Save/finalize routine to user account.
     */
    public function saveFinalRoutine()
    {
        $result = $this->routineService->saveFinalRoutine();

        if (!$result['status']) {
            $code = $result['code'] ?? 400;
            return $this->error($result['message'], $code);
        }

        return $this->success([], $result['message']);
    }

    /**
     * Select/replace product in routine with an alternative.
     */
    public function selectAlternative(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'routine_step_id' => 'required|exists:routine_steps,id',
            'product_id' => 'required|exists:products,id',
        ]);

        $result = $this->routineService->selectAlternativeProduct(
            $request->routine_step_id,
            $request->product_id
        );

        if (!$result['status']) {
            $code = $result['code'] ?? 400;
            return $this->error($result['message'], $code);
        }

        return $this->success(new ProductListResource($result['data']), $result['message']);
    }

    /**
     * Get the quiz-suggested routine (before confirm).
     */
    public function getSuggestedRoutine()
    {
        $result = $this->routineService->getSuggestedRoutine();

        if (!$result['status']) {
            $code = $result['code'] ?? 400;
            return $this->error($result['message'], $code);
        }

        return $this->success(
            RoutineResource::collection($result['data']),
            $result['message']
        );
    }
}
