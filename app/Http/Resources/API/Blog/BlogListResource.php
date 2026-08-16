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
            'name' => $this->name,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'featured_image' => $this->featured_image_path,
            'status' => $this->status ?? 'draft',
            'category' => new CategoryResource($this->whenLoaded('category')),
            'author' => new UserBlogResource($this->whenLoaded('author')),
            'reading_time' => (int) ($this->reading_time ?? 1),
            'views' => (int) ($this->views ?? 0),
            'published_at' => $this->published_at?->toDateTimeString(),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
