<?php

namespace App\Services;

use App\Http\Resources\API\Routines\RoutineGoalResource;
use App\Models\RoutineGoal;

class GoalService
{
    public function index()
    {
        $rotiens = RoutineGoal::all();
        if ($rotiens->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.routine_goal_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.routine_goal_found'),
            'data' => RoutineGoalResource::collection($rotiens)
        ];
    }
}
