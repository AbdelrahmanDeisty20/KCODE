<?php

namespace App\Http\Resources\API\CART;

use App\Http\Resources\API\AUHT\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'session_id' => $this->session_id,
            'items' => CartItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
