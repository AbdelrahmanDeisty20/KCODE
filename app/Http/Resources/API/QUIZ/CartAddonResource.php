<?php

namespace App\Http\Resources\API\QUIZ;

use App\Http\Resources\API\PRODUCT\ProductListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartAddonResource extends JsonResource
{
    /**
     * Transform the cart addon item into an array.
     */
    public function toArray(Request $request): array
    {
        $productData = is_array($this['product'] ?? null) ? $this['product'] : null;
        $productModel = is_object($this['product'] ?? null) ? $this['product'] : null;

        return [
            'display_order'       => $this['display_order'] ?? 1,
            'selected_by_default' => false,
            'cart_note_ar'        => $this['cart_note_ar'] ?? '',
            'product'             => $productModel ? new ProductListResource($productModel) : $productData,
        ];
    }
}
