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
        if (! Schema::hasColumn('ai_comparison_logs', 'report_markdown')) {
            Schema::table('ai_comparison_logs', function (Blueprint $table) {
                $table->longText('report_markdown')->nullable()->after('model');
            });
        }

        if (! Schema::hasColumn('ai_comparison_logs', 'comparison_program_name')) {
            Schema::table('ai_comparison_logs', function (Blueprint $table) {
                $table->string('comparison_program_name')->nullable()->after('report_markdown');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('ai_comparison_logs', 'comparison_program_name')) {
            Schema::table('ai_comparison_logs', function (Blueprint $table) {
                $table->dropColumn('comparison_program_name');
            });
        }

        if (Schema::hasColumn('ai_comparison_logs', 'report_markdown')) {
            Schema::table('ai_comparison_logs', function (Blueprint $table) {
                $table->dropColumn('report_markdown');
            });
        }
    }
};
