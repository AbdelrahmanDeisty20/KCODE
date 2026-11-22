<?php

namespace App\Http\Resources\API\QUIZ;

use App\Http\Resources\API\PRODUCT\ProductListResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoutineSupportResource extends JsonResource
{
    /**
     * Transform the routine support item into an array.
     */
    public function toArray(Request $request): array
    {
        $productData = is_array($this['product'] ?? null) ? $this['product'] : null;
        $productModel = is_object($this['product'] ?? null) ? $this['product'] : null;

        $defaultRole = app()->getLocale() === 'en' ? 'Optional Support' : 'عناية مكمّلة';

        return [
            'display_order'       => $this['display_order'] ?? 1,
            'selected_by_default' => (bool) ($this['selected_by_default'] ?? false),
            'step_id'             => $this['step_id'] ?? null,
            'sku'                 => $this['sku'] ?? ($productModel->sku ?? ($productData['sku'] ?? null)),
            'kind'                => $this['kind'] ?? null,
            'roles'               => $this['roles'] ?? ['supplement'],
            'display_role'        => $this['display_role'] ?? ($this['display_role_ar'] ?? ($this['routine_step_ar'] ?? $defaultRole)),
            'display_role_ar'     => $this['display_role_ar'] ?? ($this['routine_step_ar'] ?? $defaultRole),
            'routine_step'        => $this['routine_step'] ?? ($this['routine_step_ar'] ?? $defaultRole),
            'routine_step_ar'     => $this['routine_step_ar'] ?? $defaultRole,
            'routine_step_code'   => $this['routine_step_code'] ?? 'supplement',
            'use_time'            => $this['use_time'] ?? ($this['use_time_ar'] ?? ''),
            'use_time_ar'         => $this['use_time_ar'] ?? '',
            'usage_badge'         => $this['usage_badge'] ?? ($this['usage_badge_ar'] ?? ''),
            'usage_badge_ar'      => $this['usage_badge_ar'] ?? '',
            'chosen_for'          => $this['chosen_for'] ?? ($this['chosen_for_ar'] ?? ''),
            'chosen_for_ar'       => $this['chosen_for_ar'] ?? '',
            'reason'              => $this['reason'] ?? ($this['reason_ar'] ?? ''),
            'reason_ar'           => $this['reason_ar'] ?? '',
            'usage_short'         => $this['usage_short'] ?? ($this['usage_short_ar'] ?? ''),
            'usage_short_ar'      => $this['usage_short_ar'] ?? '',
            'usage_text'          => $this['usage_text'] ?? ($this['usage_text_ar'] ?? ''),
            'usage_text_ar'       => $this['usage_text_ar'] ?? '',
            'usage_tiles'         => $this['usage_tiles'] ?? [],
            'usage_policy'        => $this['usage_policy'] ?? null,
            'usage_notice'        => $this['usage_notice'] ?? ($this['usage_notice_ar'] ?? ''),
            'usage_notice_ar'     => $this['usage_notice_ar'] ?? '',
            'usage_schedule_step' => $this['usage_schedule_step'] ?? null,
            'night_variant'       => $this['night_variant'] ?? null,
            'product'             => $productModel ? new ProductListResource($productModel) : $productData,
        ];
    }
}
