<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_questions', 'key')) {
                $table->string('key')->nullable()->after('id');
            }
            if (!Schema::hasColumn('quiz_questions', 'step_label_ar')) {
                $table->string('step_label_ar')->nullable()->after('key');
            }
            if (!Schema::hasColumn('quiz_questions', 'step_label_en')) {
                $table->string('step_label_en')->nullable()->after('step_label_ar');
            }
            if (!Schema::hasColumn('quiz_questions', 'helper_ar')) {
                $table->text('helper_ar')->nullable()->after('description_en');
            }
            if (!Schema::hasColumn('quiz_questions', 'helper_en')) {
                $table->text('helper_en')->nullable()->after('helper_ar');
            }
        });

        Schema::table('quiz_options', function (Blueprint $table) {
            if (!Schema::hasColumn('quiz_options', 'code')) {
                $table->string('code')->nullable()->index()->after('quiz_question_id');
            }
        });

        Schema::table('assessments', function (Blueprint $table) {
            if (!Schema::hasColumn('assessments', 'primary_goal_code')) {
                $table->string('primary_goal_code')->nullable()->after('skin_type_id');
            }
            if (!Schema::hasColumn('assessments', 'secondary_goal_code')) {
                $table->string('secondary_goal_code')->nullable()->after('primary_goal_code');
            }
            if (!Schema::hasColumn('assessments', 'is_sensitive')) {
                $table->boolean('is_sensitive')->default(false)->after('secondary_goal_code');
            }
            if (!Schema::hasColumn('assessments', 'scenario_key')) {
                $table->string('scenario_key')->nullable()->after('is_sensitive');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $cols = array_filter(['key', 'step_label_ar', 'step_label_en', 'helper_ar', 'helper_en'], fn ($c) => Schema::hasColumn('quiz_questions', $c));
            if ($cols) {
                $table->dropColumn($cols);
            }
        });

        Schema::table('quiz_options', function (Blueprint $table) {
            if (Schema::hasColumn('quiz_options', 'code')) {
                $table->dropColumn('code');
            }
        });

        Schema::table('assessments', function (Blueprint $table) {
            $cols = array_filter(['primary_goal_code', 'secondary_goal_code', 'is_sensitive', 'scenario_key'], fn ($c) => Schema::hasColumn('assessments', $c));
            if ($cols) {
                $table->dropColumn($cols);
            }
        });
    }
};

