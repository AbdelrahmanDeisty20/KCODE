<?php

namespace App\Http\Resources\API\POLICY;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturnPolicyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'title'   => $this->title,   // Accessor handles localization
            'content' => $this->content, // Accessor handles localization
        ];
    }
}
