<?php

namespace App\Services;

use App\Models\User;
use App\Models\LoyaltyLevel;

class LoyaltyService
{
    /**
     * Get loyalty details (points, current level, progress, next level, ledger history) for a user.
     */
    public function getUserLoyaltyData(int $userId): array
    {
        $user = User::with(['loyaltyLedger' => function ($query) {
            $query->orderBy('id', 'desc');
        }])->find($userId);

        if (!$user) {
            return [
                'status'  => false,
                'message' => __('messages.user_not_found'),
            ];
        }

        return [
            'status'  => true,
            'message' => __('messages.loyalty_retrieved_successfully'),
            'data'    => $user,
        ];
    }

    /**
     * Get all active loyalty levels.
     */
    public function getAllLevels(): array
    {
        $levels = LoyaltyLevel::active()->orderBy('sort_order', 'asc')->get();

        return [
            'status'  => true,
            'message' => __('messages.loyalty_retrieved_successfully'),
            'data'    => $levels,
        ];
    }
}
