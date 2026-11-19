<?php

namespace App\Services;

use App\Models\Page;
use App\Support\StaticCache;

class PageService
{
    /**
     * Get About Us page sections structured logically (cached).
     */
    public function getAboutUs(): array
    {
        return $this->getPageByType('about_us');
    }

    /**
     * Get page by type (cached).
     */
    public function getPageByType(string $type): array
    {
        $pages = StaticCache::remember('pages', "type_{$type}", function () use ($type) {
            return Page::where('type', $type)->get();
        });

        if ($pages->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.page_not_found'),
            ];
        }

        $formatted = [];
        foreach ($pages as $page) {
            $formatted[$page->key_en] = [
                'key' => $page->key,
                'value' => $page->value,
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.page_retrieved_successfully'),
            'data' => [
                'type' => $type,
                'sections' => $formatted,
            ]
        ];
    }
}
