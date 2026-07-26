<?php

namespace App\Http\Resources\API\AUHT;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserQuoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'quote' => is_array($this->resource) ? ($this->resource['quote'] ?? null) : ($this->quote ?? null),
        ];
    }
}
