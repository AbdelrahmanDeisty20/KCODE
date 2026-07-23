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
    public function getRoutine(\Illuminate\Http\Request $request)
    {
        $routineId = $request->query('routine_id') ?? $request->input('routine_id');
        $result = $this->routineService->getUserRoutine($routineId ? (int) $routineId : null);

        if (!$result['status']) {
            $code = $result['code'] ?? 400;
            return $this->error($result['message'], $code);
        }

        return $this->success($result['data'], $result['message']);
    }

    /**
     * Save/finalize routine to user account by routine_id.
     */
    public function saveFinalRoutine(\App\Http\Requests\API\ROUTINES\ConfirmRoutineRequest $request)
    {
        $result = $this->routineService->saveFinalRoutine((int) $request->routine_id);

        if (!$result['status']) {
            $code = $result['code'] ?? 400;
            return $this->error($result['message'], $code);
        }

        return $this->success([], $result['message']);
    }

    /**
     * Select/replace product in routine with an alternative.
     */
    public function selectAlternative(\App\Http\Requests\API\ROUTINES\SelectAlternativeRequest $request)
    {
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

        return $this->success([
            'id'    => $result['data']['id'],
            'items' => RoutineResource::collection($result['data']['items']),
        ], $result['message']);
    }

    /**
     * Delete/reset routine by routine_id.
     */
    public function deleteRoutine(\App\Http\Requests\API\ROUTINES\DeleteRoutineRequest $request)
    {
        $result = $this->routineService->deleteRoutine((int) $request->routine_id);

        if (!$result['status']) {
            $code = $result['code'] ?? 400;
            return $this->error($result['message'], $code);
        }

        return $this->success([], $result['message']);
    }

    /**
     * Remove a single product from the specified routine by routine_id.
     */
    public function removeProduct(\App\Http\Requests\API\ROUTINES\RemoveProductRequest $request)
    {
        $result = $this->routineService->removeProduct(
            (int) $request->product_id,
            (int) $request->routine_id
        );

        if (!$result['status']) {
            $code = $result['code'] ?? 400;
            return $this->error($result['message'], $code);
        }

        return $this->success([], $result['message']);
    }
}
