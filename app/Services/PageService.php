<?php

namespace App\Services;

use App\Models\Page;

class PageService
{
    /**
     * Get About Us page sections structured logically.
     */
    public function getAboutUs(): array
    {
        $pages = Page::where('type', 'about_us')->get();

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
                'type' => 'about_us',
                'sections' => $formatted,
            ]
        ];
    }

    /**
     * Get page by type.
     */
    public function getPageByType(string $type): array
    {
        $pages = Page::where('type', $type)->get();

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
