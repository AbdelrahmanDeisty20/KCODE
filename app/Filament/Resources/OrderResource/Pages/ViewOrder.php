<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('change_order_status')
                ->label('تغيير حالة الطلب')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->form([
                    Forms\Components\Select::make('order_status')
                        ->label('حالة الطلب الجديدة')
                        ->options([
                            'pending'   => 'قيد الانتظار',
                            'accepted'  => 'مقبول',
                            'delivered' => 'تم التسليم',
                            'cancelled' => 'ملغي',
                        ])
                        ->default(fn ($record) => $record->order_status)
                        ->required(),
                ])
                ->action(function (array $data, $record): void {
                    $record->update([
                        'order_status' => $data['order_status'],
                    ]);

                    Notification::make()
                        ->title('تم تحديث حالة الطلب بنجاح')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('change_payment_status')
                ->label('تغيير حالة الدفع')
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->form([
                    Forms\Components\Select::make('payment_status')
                        ->label('حالة الدفع الجديدة')
                        ->options([
                            'pending'  => 'معلق',
                            'paid'     => 'مدفوع',
                            'failed'   => 'فشل الدفع',
                            'refunded' => 'مسترجع',
                        ])
                        ->default(fn ($record) => $record->payment_status)
                        ->required(),
                ])
                ->action(function (array $data, $record): void {
                    $record->update([
                        'payment_status' => $data['payment_status'],
                    ]);

                    Notification::make()
                        ->title('تم تحديث حالة الدفع بنجاح')
                        ->success()
                        ->send();
                }),

            Actions\EditAction::make()
                ->label('تعديل'),

            Actions\DeleteAction::make()
                ->label('حذف'),
        ];
    }
}
