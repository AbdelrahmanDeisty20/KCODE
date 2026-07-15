<?php

namespace App\Http\Resources\API\PRODUCT;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAuditResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if (!$this->resource) {
            return [];
        }

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'avoid_pairing_same_routine' => $this->avoid_pairing_same_routine,
            'developer_output_rule' => $this->developer_output_rule,
            'show_alternatives_button' => (bool)$this->show_alternatives_button,
            'remove_if_customer_has_it' => (bool)$this->remove_if_customer_has_it,
            'source_url' => $this->source_url,
            'data_confidence' => $this->data_confidence,
            'needs_manual_check' => (bool)$this->needs_manual_check,
        ];
    }
}
