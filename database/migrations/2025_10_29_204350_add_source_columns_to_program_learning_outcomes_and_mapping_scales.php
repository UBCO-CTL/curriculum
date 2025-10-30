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
        Schema::table('program_learning_outcomes', function (Blueprint $table) {
            if (! Schema::hasColumn('program_learning_outcomes', 'source_pl_outcome_id')) {
                $table->unsignedBigInteger('source_pl_outcome_id')->nullable()->after('pl_outcome_id');
            }
        });

        Schema::table('mapping_scales', function (Blueprint $table) {
            if (! Schema::hasColumn('mapping_scales', 'source_map_scale_id')) {
                $table->unsignedBigInteger('source_map_scale_id')->nullable()->after('map_scale_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_learning_outcomes', function (Blueprint $table) {
            if (Schema::hasColumn('program_learning_outcomes', 'source_pl_outcome_id')) {
                $table->dropColumn('source_pl_outcome_id');
            }
        });

        Schema::table('mapping_scales', function (Blueprint $table) {
            if (Schema::hasColumn('mapping_scales', 'source_map_scale_id')) {
                $table->dropColumn('source_map_scale_id');
            }
        });
    }
};
