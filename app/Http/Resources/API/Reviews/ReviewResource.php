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
        $isVerified = (bool) ($this->is_verified ?? false);
        if (!$isVerified && $this->user_id && $this->product_id) {
            static $verifiedCache = [];
            $cacheKey = "{$this->user_id}_{$this->product_id}";
            if (!isset($verifiedCache[$cacheKey])) {
                try {
                    $verifiedCache[$cacheKey] = \App\Models\OrderItem::whereHas('order', function ($q) {
                        $q->where('user_id', $this->user_id)
                          ->whereIn('order_status', ['completed', 'delivered']);
                    })->where('product_id', $this->product_id)->exists();
                } catch (\Throwable $e) {
                    $verifiedCache[$cacheKey] = false;
                }
            }
            $isVerified = $verifiedCache[$cacheKey];
        }

        return [
            'id'                   => $this->id,
            'user'                 => $this->when($this->user_id, ReviewUserResource::make($this->whenLoaded('user'))),
            'product'              => $this->when($this->product_id, ProductListResource::make($this->whenLoaded('product'))),
            'rating'               => $this->rating,
            'comment'              => $this->comment,
            'is_verified_purchase' => $isVerified,
            'is_verified'          => $isVerified,
            'city'                 => $this->whenLoaded('user', fn() => $this->user?->city),
            'created_at'           => $this->created_at,
        ];
    }
}
