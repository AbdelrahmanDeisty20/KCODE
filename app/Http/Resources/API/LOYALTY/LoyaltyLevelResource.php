<?php

namespace App\Http\Resources\API\LOYALTY;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyLevelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name, // Accessor handles localization
            'description' => $this->description, // Accessor handles localization
            'policy'      => $this->policy, // Accessor handles localization
            'min_points'  => $this->min_points,
            'max_points'  => $this->max_points,
            'icon'        => $this->icon,
            'sort_order'  => $this->sort_order,
        ];
    }
}
