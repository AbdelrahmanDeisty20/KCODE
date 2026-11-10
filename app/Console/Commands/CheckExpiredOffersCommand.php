<?php

namespace App\Console\Commands;

use App\Models\Offer;
use App\Models\Coupon;
use App\Services\ActivityLogger;
use Illuminate\Console\Command;

class CheckExpiredOffersCommand extends Command
{
    protected $signature = 'kcode:check-expired-offers';
    protected $description = 'Deactivate and log expired offers and coupons';

    public function handle(): int
    {
        $now = now();

        // 1. Check Offers whose end_date has passed
        $expiredOffers = Offer::where('is_active', true)
            ->whereNotNull('end_date')
            ->where('end_date', '<', $now)
            ->with('product')
            ->get();

        foreach ($expiredOffers as $offer) {
            $offer->update(['is_active' => false]);
            $pName = $offer->product?->name_ar ?: ($offer->product?->name_en ?: 'منتج');
            $this->info("Expired offer deactivated for product: {$pName}");
        }

        // 2. Check Coupons whose end_date has passed
        $expiredCoupons = Coupon::where('is_active', true)
            ->whereNotNull('end_date')
            ->where('end_date', '<', $now)
            ->get();

        foreach ($expiredCoupons as $coupon) {
            $coupon->update(['is_active' => false]);
            $this->info("Expired coupon deactivated: {$coupon->code}");
        }

        $this->info("Checked expired offers (" . $expiredOffers->count() . ") and coupons (" . $expiredCoupons->count() . ").");

        return Command::SUCCESS;
    }
}

