<?php

namespace App\Http\Resources\API\Contact;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'message'    => $this->message,
            'phone'      => $this->phone,
            'email'      => $this->email,
            'user_id'    => $this->user_id,
            'status'     => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
