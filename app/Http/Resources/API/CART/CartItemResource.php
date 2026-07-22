<?php

namespace App\Http\Resources\API\CART;

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
        $priceAfterDiscount = $this->price_after_discount !== null 
            ? (float) $this->price_after_discount 
            : max(0, $unitPrice - $discountAmount);

        return [
            'id' => $this->id,
            'quantity' => (int) $this->quantity,
            'unit_price' => $unitPrice,
            'discount_amount' => $discountAmount,
            'discount_percentage' => (float) $this->discount_percentage,
            'price_after_discount' => round($priceAfterDiscount, 2),
            'total_price' => (float) $this->total_price,
            'product' => new ProductResource($this->product),
        ];
    }
}
