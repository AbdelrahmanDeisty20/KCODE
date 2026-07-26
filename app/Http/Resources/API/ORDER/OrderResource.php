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
            'id'            => $this->id,
            'order_number'  => $this->order_number,
            'order_status'  => $this->order_status,
            'created_at'    => $this->created_at,
        ];
    }
}
