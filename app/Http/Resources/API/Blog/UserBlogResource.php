<?php

namespace App\Http\Resources\API\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $name = $this->name;
        if (empty($name) || $name === 'Blog Author') {
            $isEn = (app()->getLocale() === 'en' || request()->header('lang') === 'en');
            $name = $isEn ? 'KCODE Editorial' : 'فريق KCODE';
        }

        return [
            'id' => $this->id,
            'name' => $name,
            'email' => $this->email,
            'image' => $this->image_path,
            'type' => $this->type,
        ];
    }
}
