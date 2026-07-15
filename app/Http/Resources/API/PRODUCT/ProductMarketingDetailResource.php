<?php

namespace App\Http\Resources\API\PRODUCT;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductMarketingDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (!$this->resource) {
            return [];
        }

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'primary_badge' => $this->primary_badge,
            'result_promise' => $this->result_promise,
            'objection_answer' => $this->objection_answer,
            'routine_reason' => $this->routine_reason,
            'bundle_cta' => $this->bundle_cta,
            'add_to_cart_microcopy' => $this->add_to_cart_microcopy,
        ];
    }
}
