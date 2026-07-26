<?php

namespace App\Http\Resources\API\ORDER;

use App\Http\Resources\API\PRODUCT\ProductListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'product_id'      => $this->product_id,
            'product_name'    => $this->product_name,
            'quantity'        => (int) $this->quantity,
            'unit_price'      => (float) $this->unit_price,
            'discount_amount' => (float) $this->discount_amount,
            'total_price'     => (float) $this->total_price,
            'product'         => new ProductListResource($this->whenLoaded('product')),
        ];
    }
}
