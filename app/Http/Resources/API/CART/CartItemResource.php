<?php

namespace App\Http\Resources\API\CART;

use App\Http\Resources\API\PRODUCT\ProductListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\PRODUCT\ProductResource;

class CartItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $unitPrice = (float) $this->unit_price;
        $discountAmount = (float) $this->discount_amount;
        $discountPercentage = (float) $this->discount_percentage;
        $rawPriceAfterDiscount = (float) $this->price_after_discount;

        $priceAfterDiscount = ($discountAmount > 0 || $discountPercentage > 0) && $rawPriceAfterDiscount > 0
            ? round($rawPriceAfterDiscount, 2)
            : 0;

        return [
            'id' => $this->id,
            'quantity' => (int) $this->quantity,
            'unit_price' => $unitPrice,
            'discount_amount' => $discountAmount,
            'discount_percentage' => (float) $this->discount_percentage,
            'price_after_discount' => round($priceAfterDiscount, 2),
            'total_price' => (float) $this->total_price,
            'product' => new ProductListResource($this->whenLoaded('product')),
        ];
    }
}
