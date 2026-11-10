<?php

namespace App\Observers;

use App\Models\Address;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\Log;

class AddressObserver
{
    public function created(Address $address): void
    {
        try {
            $user = $address->user ?? User::find($address->user_id);
            $userName = $user?->name ?: 'عميل';
            $locationParts = array_filter([
                $address->city?->name_ar ?? $address->city?->name_en,
                $address->state?->name_ar ?? $address->state?->name_en,
                $address->country?->name_ar ?? $address->country?->name_en,
            ]);
            $locationStr = !empty($locationParts) ? implode('، ', $locationParts) : 'عنوان جديد';

            ActivityLogger::log(
                event: 'address_added',
                description: "قام العميل [{$userName}] بإضافة عنوان شحن جديد ({$locationStr}) - التفاصيل: [{$address->address}]",
                subjectType: 'Address',
                subjectId: $address->id,
                newValues: [
                    'address'    => $address->address,
                    'phone'      => $address->phone,
                    'is_default' => (bool) $address->is_default,
                ],
                userId: $user?->id,
                userName: $userName
            );
        } catch (\Throwable $e) {
            Log::warning("AddressObserver created log failed: " . $e->getMessage());
        }
    }

    public function updated(Address $address): void
    {
        try {
            $user = $address->user ?? User::find($address->user_id);
            $userName = $user?->name ?: 'عميل';

            ActivityLogger::log(
                event: 'address_updated',
                description: "قام العميل [{$userName}] بتحديث بيانات عنوان الشحن رقم #{$address->id}",
                subjectType: 'Address',
                subjectId: $address->id,
                oldValues: array_intersect_key($address->getOriginal(), $address->getChanges()),
                newValues: $address->getChanges(),
                userId: $user?->id,
                userName: $userName
            );
        } catch (\Throwable $e) {
            Log::warning("AddressObserver updated log failed: " . $e->getMessage());
        }
    }

    public function deleted(Address $address): void
    {
        try {
            $user = $address->user ?? User::find($address->user_id);
            $userName = $user?->name ?: 'عميل';

            ActivityLogger::log(
                event: 'address_deleted',
                description: "قام العميل [{$userName}] بحذف عنوان الشحن رقم #{$address->id}",
                subjectType: 'Address',
                subjectId: $address->id,
                oldValues: [
                    'address' => $address->address,
                ],
                userId: $user?->id,
                userName: $userName
            );
        } catch (\Throwable $e) {
            Log::warning("AddressObserver deleted log failed: " . $e->getMessage());
        }
    }
}

