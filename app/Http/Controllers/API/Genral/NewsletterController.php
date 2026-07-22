<?php

namespace App\Http\Controllers\API\Genral;

use App\Http\Controllers\Controller;
use App\Services\NewsletterService;
use App\Traits\ApiResponse;
use App\Http\Requests\API\GENERAL\SubscribeNewsletterRequest;

use App\Http\Resources\API\NEWSLETTER\NewsletterSubscriptionResource;

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
            new NewsletterSubscriptionResource($result['data']),
            $result['message']
        );
    }
}
