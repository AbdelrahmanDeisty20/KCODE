<?php

namespace App\Services;

use App\Models\NewsletterSubscription;
use App\Mail\NewsletterWelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NewsletterService
{
    /**
     * Subscribe an email to the newsletter with optional device_id.
     */
    public function subscribe(string $email, ?string $deviceId = null): array
    {
        $existing = NewsletterSubscription::where('email', $email)->first();

        if ($existing) {
            if ($existing->is_active && $existing->device_id === $deviceId) {
                return [
                    'status'  => false,
                    'message' => __('messages.newsletter_already_subscribed'),
                ];
            }

            // Update active status and device_id
            $existing->update([
                'is_active' => true,
                'device_id' => $deviceId ?? $existing->device_id,
            ]);
            $subscription = $existing;
        } else {
            $subscription = NewsletterSubscription::create([
                'email'     => $email,
                'device_id' => $deviceId,
                'is_active' => true,
            ]);
        }

        // Send welcome email to subscriber via Queue
        try {
            Mail::to($email)
                ->locale(app()->getLocale())
                ->queue(new NewsletterWelcomeMail($email));
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
