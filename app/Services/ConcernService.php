<?php

namespace App\Services;

use App\Http\Resources\API\CONCERN\ConcernResource;
use App\Models\Concern;

class ConcernService
{
    public function index()
    {
        $concerns = Concern::all();
        if ($concerns->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_concerns_found'),
                'data' => [],
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.concerns_retrieved_successfully'),
            'data' => ConcernResource::collection($concerns),
        ];
    }

    public function show($id)
    {
        $concern = Concern::find($id);
        if (!$concern) {
            return [
                'status' => false,
                'message' => __('messages.concern_not_found'),
                'data' => null,
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.concern_retrieved_successfully'),
            'data' => new ConcernResource($concern),
        ];
    }
}
