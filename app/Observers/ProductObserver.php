<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\User;
use App\Services\ActivityLogger;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        ActivityLogger::log(
            event: 'created',
            description: "تم إضافة منتج جديد: [{$product->name_ar}] (SKU: {$product->sku})",
            subjectType: 'Product',
            subjectId: $product->id,
            newValues: [
                'name_ar' => $product->name_ar,
                'name_en' => $product->name_en,
                'sku'     => $product->sku,
                'price'   => $product->price,
                'stock'   => $product->stock,
            ]
        );

        $this->checkLowStockAndNotifyAdmin($product);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        if ($product->wasChanged()) {
            ActivityLogger::log(
                event: 'updated',
                description: "تم تحديث بيانات المنتج: [{$product->name_ar}]",
                subjectType: 'Product',
                subjectId: $product->id,
                oldValues: array_intersect_key($product->getOriginal(), $product->getChanges()),
                newValues: $product->getChanges()
            );
        }

        if ($product->wasChanged('stock')) {
            $this->checkLowStockAndNotifyAdmin($product);
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        ActivityLogger::log(
            event: 'deleted',
            description: "تم حذف المنتج: [{$product->name_ar}]",
            subjectType: 'Product',
            subjectId: $product->id
        );
    }

    /**
     * Check if product stock is less than 10 and send notification to Admins.
     */
    protected function checkLowStockAndNotifyAdmin(Product $product): void
    {
        try {
            if ($product->stock < 10) {
                $admins = User::where(function ($query) {
                    $query->where('type', 'admin')
                        ->orWhereHas('roles', fn ($q) => $q->whereIn('name', ['admin', 'super_admin', 'Admin', 'Super Admin']))
                        ->orWhere('email', 'admin@kcode.com');
                })->get();

                if ($admins->isEmpty()) {
                    $admins = User::where('id', 1)->get();
                }

                if ($admins->isNotEmpty()) {
                    $productUrl = \App\Filament\Resources\ProductResource::getUrl('edit', ['record' => $product->id]);

                    Notification::make()
                        ->title("تنبيه مخزون منخفض ⚠️")
                        ->body("المنتج [{$product->name_ar}] (SKU: {$product->sku}) كمية المخزون المتبقية أقل من 10 قطع (المتبقي: {$product->stock} قطع)")
                        ->icon('heroicon-o-exclamation-triangle')
                        ->warning()
                        ->actions([
                            \Filament\Actions\Action::make('view_product')
                                ->label('عرض وتعديل المخزون ⚙️')
                                ->url($productUrl)
                                ->button()
                                ->color('warning'),
                        ])
                        ->sendToDatabase($admins);

                    Log::info("Low stock notification sent to admins for Product ID {$product->id} (Stock: {$product->stock})");
                }
            }
        } catch (\Exception $e) {
            Log::error("ProductObserver low stock notification error: " . $e->getMessage());
        }
    }
}
