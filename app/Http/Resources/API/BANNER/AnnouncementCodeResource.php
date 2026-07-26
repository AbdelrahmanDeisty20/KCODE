<?php

namespace App\Http\Resources\API\BANNER;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementCodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'code' => is_array($this->resource) ? ($this->resource['code'] ?? null) : ($this->code ?? null),
        ];
    }
}
