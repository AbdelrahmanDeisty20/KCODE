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

    /**
     * Add loyalty points for a user and record in ledger.
     */
    public function addPoints(
        int $userId,
        int $points,
        string $sourceType,
        ?int $sourceId = null,
        ?string $descriptionAr = null,
        ?string $descriptionEn = null
    ): ?\App\Models\LoyaltyPointsLedger {
        if ($points <= 0) {
            return null;
        }

        return \App\Models\LoyaltyPointsLedger::create([
            'user_id'        => $userId,
            'points'         => $points,
            'source_type'    => $sourceType,
            'source_id'      => $sourceId,
            'description_ar' => $descriptionAr ?? "كسب نقاط من {$sourceType}",
            'description_en' => $descriptionEn ?? "Earned points from {$sourceType}",
        ]);
    }
}
