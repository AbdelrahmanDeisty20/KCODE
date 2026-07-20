<?php

namespace App\Services;

use App\Models\NewsletterSubscription;

class NewsletterService
{
    /**
     * Subscribe an email to the newsletter.
     */
    public function subscribe(string $email): array
    {
        $existing = NewsletterSubscription::where('email', $email)->first();

        if ($existing) {
            if ($existing->is_active) {
                return [
                    'status'  => false,
                    'message' => __('messages.newsletter_already_subscribed'),
                ];
            }

            // If it was inactive, activate it again
            $existing->update(['is_active' => true]);

            return [
                'status'  => true,
                'message' => __('messages.newsletter_subscribed_successfully'),
                'data'    => $existing,
            ];
        }

        $subscription = NewsletterSubscription::create([
            'email'     => $email,
            'is_active' => true,
        ]);

        return [
            'status'  => true,
            'message' => __('messages.newsletter_subscribed_successfully'),
            'data'    => $subscription,
        ];
    }
}
