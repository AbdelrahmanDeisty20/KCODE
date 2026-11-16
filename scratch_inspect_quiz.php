<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$questions = App\Models\QuizQuestion::with('options')->orderBy('step_number')->get();
foreach ($questions as $q) {
    echo "Step: {$q->step_number} | ID: {$q->id} | Key: {$q->key} | Title: {$q->question_ar}\n";
    foreach ($q->options as $opt) {
        echo "   Opt ID: {$opt->id} | Code: {$opt->code} | mapped_id: {$opt->mapped_id} | Title: {$opt->option_ar}\n";
    }
}
