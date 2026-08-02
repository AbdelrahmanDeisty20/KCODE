<?php

namespace App\Http\Resources\API\SETTINGS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhilosophyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'badge'    => $this['badge'] ?? null,
            'title'    => $this['title'] ?? null,
            'subtitle' => $this['subtitle'] ?? null,
            'quote'    => $this['quote'] ?? null,
            'features' => $this['features'] ?? [],
        ];
    }
}
