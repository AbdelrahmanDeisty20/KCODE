<?php

namespace App\Http\Resources\API\BANNER;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementBannerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'text' => $this['banner_text'] ?? $this['text'] ?? null,
        ];
    }
}
