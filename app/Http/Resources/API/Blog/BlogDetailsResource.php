<?php

namespace App\Http\Resources\API\Blog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogDetailsResource extends JsonResource
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
            'content' => $this->content,
            'featured_image' => $this->featured_image ? (filter_var($this->featured_image, FILTER_VALIDATE_URL) ? $this->featured_image : asset('storage/' . $this->featured_image)) : null,
            'status' => $this->status ?? 'draft',
            'is_featured' => (bool) ($this->is_featured ?? false),
            'reading_time' => (int) ($this->reading_time ?? 1),
            'views' => (int) ($this->views ?? 0),
            'published_at' => $this->published_at?->toDateTimeString(),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'author' => new UserBlogResource($this->whenLoaded('author')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'seo' => new SeoResource($this->whenLoaded('seo')),
            'related_blogs' => BlogListResource::collection($this->whenLoaded('relatedBlogs')),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
