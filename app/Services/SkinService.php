<?php

namespace App\Services;

use App\Http\Resources\API\CATEGORY\SkinTypeResource;
use App\Models\SkinType;

class SkinService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        
    }
    public function SkinTypes() {
        $skinTypes = SkinType::paginate(10);
        if ($skinTypes->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.no_skin_types_found'),
                'data' => [],
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.skin_types_retrieved_successfully'),
            'data' => $skinTypes,
        ];
    }
    public function show($id) {
        $skinType = SkinType::find($id);
        if (!$skinType) {
            return [
                'status' => false,
                'message' => __('messages.skin_type_not_found'),
                'data' => null,
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.skin_type_retrieved_successfully'),
            'data' => $skinType,
        ];
    }
}
