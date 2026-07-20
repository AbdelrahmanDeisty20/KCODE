<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\FAQ\FaqResource;
use App\Services\FaqService;
use App\Traits\ApiResponse;

class FaqController extends Controller
{
    use ApiResponse;

    public function __construct(private FaqService $faqService) {}

    /**
     * Get all active FAQs.
     */
    public function getFaqs()
    {
        $result = $this->faqService->getActiveFaqs();

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(
            FaqResource::collection($result['data']),
            $result['message']
        );
    }
}
