<?php

namespace App\Http\Resources\API\Rivews;

use App\Http\Resources\API\AUHT\UserResource;
use App\Http\Resources\API\PRODUCT\ProductResource;
use App\Http\Resources\API\Reviews\ReviewUserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'product' => $this->when($this->product_id , new ProductResource($this->whenLoaded('product'))),
            'user' => new ReviewUserResource($this->whenLoaded('user')),
            'rating' => $this->rating,
            'comment' => $this->comment,
            'created_at' => $this->created_at,
        ];
    }
}
