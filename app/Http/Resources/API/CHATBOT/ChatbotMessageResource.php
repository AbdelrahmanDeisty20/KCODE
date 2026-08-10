<?php

namespace App\Http\Resources\API\CHATBOT;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatbotMessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'prompt' => $this->prompt,
            'reply' => $this->reply,
            'recommended_products' => $this->recommended_products ?? [],
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
