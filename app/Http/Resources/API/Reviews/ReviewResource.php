<?php

namespace App\Http\Resources\API\Reviews;

use App\Http\Resources\API\PRODUCT\ProductListResource;
use App\Http\Resources\API\PRODUCT\ProductResource;
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
            'name' => $this->name,
            'user' => $this->when($this->user_id, ReviewUserResource::make($this->whenLoaded('user'))),
            'product'=>$this->when($this->product_id, ProductListResource::make($this->whenLoaded('product'))),
            'rating'=>$this->rating,
            'comment'=>$this->comment,
            'created_at'=>$this->created_at,
        ];
    }
}
