<?php

namespace App\Http\Resources\API\Reviews;

use App\Http\Resources\API\PRODUCT\ProductListResource;
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
            $cacheKey = "{$this->user_id}_{$this->product_id}";
            $verifiedMap = $request->attributes->get('kcode_review_verified_map', []);
            if (!array_key_exists($cacheKey, $verifiedMap)) {
                try {
                    $verifiedMap[$cacheKey] = \App\Models\OrderItem::whereHas('order', function ($q) {
                        $q->where('user_id', $this->user_id)
                          ->whereIn('order_status', ['completed', 'delivered']);
                    })->where('product_id', $this->product_id)->exists();
                } catch (\Throwable $e) {
                    $verifiedMap[$cacheKey] = false;
                }
                $request->attributes->set('kcode_review_verified_map', $verifiedMap);
            }
            $isVerified = $verifiedMap[$cacheKey];
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
