<?php

namespace App\Http\Resources\API\FCM;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FcmTokenResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'token'      => $this->token,
            'device_id'  => $this->device_id,
            'created_at' => $this->created_at?->toIso8601String() ?? $this->created_at,
        ];
    }
}
