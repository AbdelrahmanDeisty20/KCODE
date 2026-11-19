<?php

namespace App\Services;

use App\Http\Resources\API\CONCERN\ConcernResource;
use App\Models\Concern;
use App\Support\StaticCache;

class ConcernService
{
    public function index()
    {
        $concerns = StaticCache::remember('concerns', 'all', fn () => Concern::orderBy('id', 'asc')->get());

        if (request()->has('chosen') || request()->has('is_chosen') || request('filter') === 'chosen') {
            $concerns = $concerns->where('is_chosen', true)->values();
        }

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

    public function chosen()
    {
        $all = StaticCache::remember('concerns', 'all', fn () => Concern::orderBy('id', 'asc')->get());
        $concerns = $all->where('is_chosen', true)->values();
        if ($concerns->isEmpty()) {
            $concerns = $all->take(9)->values();
        }
        return [
            'status' => true,
            'message' => __('messages.concerns_retrieved_successfully'),
            'data' => ConcernResource::collection($concerns),
        ];
    }

    public function show($id)
    {
        $all = StaticCache::remember('concerns', 'all', fn () => Concern::orderBy('id', 'asc')->get());
        $concern = $all->firstWhere('id', $id);
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
