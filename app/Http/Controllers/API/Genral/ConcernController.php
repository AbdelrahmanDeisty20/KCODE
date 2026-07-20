<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Services\ConcernService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ConcernController extends Controller
{
    use ApiResponse;

    protected $concernService;

    public function __construct(ConcernService $concernService)
    {
        $this->concernService = $concernService;
    }

    public function index()
    {
        $concerns = $this->concernService->index();
        if (!$concerns['status']) {
            return $this->error($concerns['message'], 404);
        }
        return $this->success($concerns['data'], $concerns['message']);
    }

    public function show($id)
    {
        $concern = $this->concernService->show($id);
        if (!$concern['status']) {
            return $this->error($concern['message'], 404);
        }
        return $this->success($concern['data'], $concern['message']);
    }
}
