<?php

namespace App\Http\Resources\API\NOTIFICATION;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $locale = $request->header('lang') ?? app()->getLocale();

        return [
            'id'         => $this->id,
            'user_id'    => $this->user_id,
            'title'      => $locale === 'en' ? ($this->title_en ?: $this->title_ar) : ($this->title_ar ?: $this->title_en),
            'message'    => $locale === 'en' ? ($this->message_en ?: $this->message_ar) : ($this->message_ar ?: $this->message_en),
            'title_ar'   => $this->title_ar,
            'title_en'   => $this->title_en,
            'message_ar' => $this->message_ar,
            'message_en' => $this->message_en,
            'type'       => $this->type,
            'data'       => $this->data,
            'is_read'    => (bool) $this->is_read,
            'created_at' => $this->created_at?->toIso8601String() ?? $this->created_at,
        ];
    }
}
