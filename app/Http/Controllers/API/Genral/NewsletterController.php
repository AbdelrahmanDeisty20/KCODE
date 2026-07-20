<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Services\NewsletterService;
use App\Traits\ApiResponse;
use App\Http\Requests\API\GENERAL\SubscribeNewsletterRequest;

class NewsletterController extends Controller
{
    use ApiResponse;

    public function __construct(private NewsletterService $newsletterService) {}

    /**
     * Subscribe an email to the newsletter.
     */
    public function subscribe(SubscribeNewsletterRequest $request)
    {
        $result = $this->newsletterService->subscribe($request->email);

        if (!$result['status']) {
            return $this->error($result['message']);
        }

        return $this->success(
            $result['data'],
            $result['message']
        );
    }
}
