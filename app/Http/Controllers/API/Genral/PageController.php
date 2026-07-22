<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Services\PageService;
use App\Traits\ApiResponse;

class PageController extends Controller
{
    use ApiResponse;

    protected $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * Get About Us page details.
     */
    public function getAboutUs()
    {
        $result = $this->pageService->getAboutUs();

        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }

        return $this->success($result['data'], $result['message']);
    }

    /**
     * Get page content by type.
     */
    public function getPageByType(string $type)
    {
        $result = $this->pageService->getPageByType($type);

        if (!$result['status']) {
            return $this->error($result['message'], 404);
        }

        return $this->success($result['data'], $result['message']);
    }
}
