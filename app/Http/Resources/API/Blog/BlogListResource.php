<?php

namespace App\Http\Resources\API\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogListResource extends JsonResource
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
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'featured_image' => $this->featured_image ? (filter_var($this->featured_image, FILTER_VALIDATE_URL) ? $this->featured_image : asset('storage/' . $this->featured_image)) : null,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'author' => new UserBlogResource($this->whenLoaded('author')),
            'reading_time' => $this->reading_time,
            'views' => $this->views,
            'published_at' => $this->published_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
