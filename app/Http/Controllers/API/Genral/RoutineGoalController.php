<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Services\GoalService;
use App\Services\RoutineService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class RoutineGoalController extends Controller
{
    use ApiResponse;
    protected $routinegoalService;
    public function __construct(GoalService $routinegoalService){
        $this->routinegoalService= $routinegoalService;
    }
    public function index(){
        $routines = $this->routinegoalService->index();
        if(!$routines['status'])
        {
            $this->error($routines['message'],400);
        }
        $this->success($routines['data'],$routines['messages'],200);
    }
}
