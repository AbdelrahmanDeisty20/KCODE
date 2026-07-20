<?php

namespace App\Http\Resources\API\POLICY;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingPolicyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $content = $this->content;
        $decoded = json_decode($content, true);

        return [
            'id'      => $this->id,
            'title'   => $this->title,   // Accessor handles localization
            'content' => json_last_error() === JSON_ERROR_NONE ? $decoded : $content,
        ];
    }
}
