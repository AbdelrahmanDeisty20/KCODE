<?php

namespace App\Http\Resources\API\ADDRESS;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\LOCATION\CountryResource;
use App\Http\Resources\API\LOCATION\StateResource;
use App\Http\Resources\API\LOCATION\CityResource;

class AddressResource extends JsonResource
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
            'title' => $this->title,
            'phone' => $this->phone,
            'address' => $this->address,
            'country' => $this->country ? new CountryResource($this->country) : null,
            'state' => $this->state ? new StateResource($this->state) : null,
            'city' => $this->city ? new CityResource($this->city) : null,
            'is_default' => $this->is_default,
            'created_at' => $this->created_at,
        ];
    }
}

