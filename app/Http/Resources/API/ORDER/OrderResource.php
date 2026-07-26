<?php

namespace App\Http\Resources\API\ORDER;

use App\Http\Resources\API\ADDRESS\AddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'order_number'     => $this->order_number,
            'user_name'        => $this->user_name,
            'user_phone'       => $this->user_phone,
            'payment_method'   => $this->payment_method,
            'payment_status'   => $this->payment_status,
            'order_status'     => $this->order_status,
            'subtotal'         => (float) $this->subtotal,
            'discount_amount'  => (float) $this->discount_amount,
            'shipping_fee'     => (float) $this->shipping_fee,
            'total'            => (float) $this->total,
            'coupon_code'      => $this->coupon_code,
            'notes'            => $this->notes,
            'shipping_address' => $this->shipping_address,
            'address'          => new AddressResource($this->whenLoaded('address')),
            'items'            => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at'       => $this->created_at,
        ];
    }
}
