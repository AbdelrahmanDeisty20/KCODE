<?php

namespace App\Http\Resources\API\LOYALTY;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyPointsLedgerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lang = $request->header('lang') ?? app()->getLocale();

        return [
            'id'          => $this->id,
            'points'      => $this->points,
            'source_type' => $this->source_type,
            'source_id'   => $this->source_id,
            'description' => $lang === 'ar' ? $this->description_ar : $this->description_en,
            'created_at'  => $this->created_at ? $this->created_at->toIso8601String() : null,
        ];
    }
}
