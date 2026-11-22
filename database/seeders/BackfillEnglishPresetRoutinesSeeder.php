<?php

namespace Database\Seeders;

use App\Models\PresetRoutine;
use Database\Seeders\Support\PresetRoutineEnglishBuilder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BackfillEnglishPresetRoutinesSeeder extends Seeder
{
    /**
     * Non-destructive, idempotent English backfill for existing PresetRoutine records.
     *
     * Updates only empty or Arabic-contaminated English fields, preserving routine IDs,
     * Arabic copy, pricing, sorting, and any custom English copy saved via the Dashboard.
     */
    public function run(): void
    {
        $updatedCount = 0;

        DB::transaction(function () use (&$updatedCount) {
            PresetRoutine::with(['items.product'])
                ->orderBy('id')
                ->chunkById(50, function ($routines) use (&$updatedCount) {
                    foreach ($routines as $routine) {
                        if (PresetRoutineEnglishBuilder::backfillRoutine($routine)) {
                            $updatedCount++;
                        }
                    }
                });
        });

        $this->command?->info("BackfillEnglishPresetRoutinesSeeder updated {$updatedCount} routine(s).");
    }
}
