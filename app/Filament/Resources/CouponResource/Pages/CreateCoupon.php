<?php

namespace App\Filament\Resources\CouponResource\Pages;

use App\Filament\Resources\CouponResource;
use App\Models\Coupon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $targetType = $data['target_type'] ?? 'general';
        $userIds    = $data['target_user_ids'] ?? [];

        // Remove virtual form fields before saving to DB
        unset($data['target_type'], $data['target_user_ids']);

        if ($targetType === 'specific' && !empty($userIds)) {
            $firstCreated = null;

            foreach ($userIds as $index => $userId) {
                $couponData = $data;
                $couponData['user_id']    = $userId;
                $couponData['is_general'] = false;

                // Ensure a unique code starting with KCODE- for each target user
                if ($index === 0 && !empty($data['code'])) {
                    $couponData['code'] = $data['code'];
                } else {
                    $newCode = 'KCODE-' . strtoupper(Str::random(6));
                    while (Coupon::where('code', $newCode)->exists()) {
                        $newCode = 'KCODE-' . strtoupper(Str::random(6));
                    }
                    $couponData['code'] = $newCode;
                }

                $created = static::getModel()::create($couponData);

                if ($index === 0) {
                    $firstCreated = $created;
                }
            }

            return $firstCreated;
        }

        // Default General Coupon creation
        $data['is_general'] = true;
        $data['user_id']    = null;

        return static::getModel()::create($data);
    }
}
