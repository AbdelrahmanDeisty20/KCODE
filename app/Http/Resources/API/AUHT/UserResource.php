<?php

namespace App\Http\Resources\API\AUHT;

use App\Http\Resources\API\Skins\SkinTypeResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date,
            'quote' => $this->quote,
            'image' => $this->image_path,
            'skin_type' => when($this->skin_type_id, new SkinTypeResource($this->whenLoaded('skin_type'))),
            'type' => $this->type,
            'status' => $this->status,
            'created_at' => $this->created_at,
        ];
    }
}
