<?php

namespace App\Filament\Resources\ActivityLogResource\Pages;

use App\Filament\Resources\ActivityLogResource;
use App\Models\ActivityLog;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListActivityLogs extends ListRecords
{
    protected static string $resource = ActivityLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('clear_all_logs')
                ->label('تفريغ كافة السجلات')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('تفريغ وحذف كافة سجلات العمليات')
                ->modalDescription(function () {
                    $total = ActivityLog::count();
                    return "هل أنت متأكد تماماً من رغبتك في تفريغ وحذف جميع سجلات العمليات بالكامل؟ إجمالي السجلات الحالية: " . number_format($total) . " سجل. هذا الإجراء نهائي وسيتم مسح كافة البيانات لتحرير مساحة التخزين فوراً.";
                })
                ->modalSubmitActionLabel('نعم، تفريغ الكل الآن')
                ->modalCancelActionLabel('إلغاء')
                ->action(function () {
                    $count = ActivityLog::count();
                    try {
                        DB::table('activity_logs')->truncate();
                    } catch (\Throwable $e) {
                        ActivityLog::query()->delete();
                    }

                    Notification::make()
                        ->title('تم تفريغ سجلات العمليات بنجاح')
                        ->body("تم حذف " . number_format($count) . " سجل بالكامل من قاعدة البيانات.")
                        ->success()
                        ->send();
                }),

            Actions\Action::make('clean_older_logs')
                ->label('تنظيف السجلات القديمة')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->form([
                    Forms\Components\Select::make('period')
                        ->label('حذف السجلات التي مر عليها أكثر من:')
                        ->options([
                            '7' => '7 أيام (أسبوع)',
                            '14' => '14 يوماً (أسبوعين)',
                            '30' => '30 يوماً (شهر)',
                            '60' => '60 يوماً (شهرين)',
                            '90' => '90 يوماً (3 أشهر)',
                        ])
                        ->default('30')
                        ->required(),
                ])
                ->modalHeading('تنظيف السجلات السابقة حسب المدة')
                ->modalDescription('اختر المدة الزمنية لحذف السجلات القديمة والإبقاء على أحدث السجلات.')
                ->modalSubmitActionLabel('تنظيف الآن')
                ->modalCancelActionLabel('إلغاء')
                ->action(function (array $data) {
                    $days = (int) $data['period'];
                    $date = now()->subDays($days);
                    $count = ActivityLog::where('created_at', '<', $date)->count();

                    if ($count === 0) {
                        Notification::make()
                            ->title('لا توجد سجلات مطابقة')
                            ->body("لم يتم العثور على أي سجلات أقدم من {$days} يوماً.")
                            ->info()
                            ->send();
                        return;
                    }

                    ActivityLog::where('created_at', '<', $date)->delete();

                    Notification::make()
                        ->title('تم تنظيف السجلات بنجاح')
                        ->body("تم حذف " . number_format($count) . " سجل أقدم من {$days} يوماً بنجاح.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
