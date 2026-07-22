<?php

namespace App\Services;

use App\Models\NewsletterSubscription;
use App\Mail\NewsletterWelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
            $subscription = $existing;
        } else {
            $subscription = NewsletterSubscription::create([
                'email'     => $email,
                'is_active' => true,
            ]);
        }

        // Send welcome email to subscriber via Queue (with discount coupon and locale support)
        try {
            Mail::to($email)
                ->locale(app()->getLocale())
                ->queue(new NewsletterWelcomeMail($email, 'KCODE10'));
        } catch (\Throwable $e) {
            Log::error("Failed to queue newsletter welcome email to {$email}: " . $e->getMessage());
        }

        return [
            'status'  => true,
            'message' => __('messages.newsletter_subscribed_successfully'),
            'data'    => $subscription,
        ];
    }
}
