<?php

namespace App\Http\Resources\API\LOYALTY;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currentLevel = $this->loyalty_level;
        $nextLevel = $this->next_loyalty_level;
        $pointsBalance = $this->loyalty_points_balance;

        return [
            'points_balance' => $pointsBalance,
            'current_level'  => $currentLevel ? new LoyaltyLevelResource($currentLevel) : null,
            'next_level'     => $nextLevel ? array_merge(
                (new LoyaltyLevelResource($nextLevel))->toArray($request),
                ['points_needed' => max(0, $nextLevel->min_points - $pointsBalance)]
            ) : null,
            'progress'       => $this->loyalty_progress,
            'history'        => LoyaltyPointsLedgerResource::collection($this->whenLoaded('loyaltyLedger')),
        ];
    }
}
