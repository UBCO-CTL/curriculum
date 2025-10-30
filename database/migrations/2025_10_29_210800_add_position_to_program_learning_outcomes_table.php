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
            if (! Schema::hasColumn('program_learning_outcomes', 'position')) {
                $table->unsignedInteger('position')->default(0)->after('plo_category_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_learning_outcomes', function (Blueprint $table) {
            if (Schema::hasColumn('program_learning_outcomes', 'position')) {
                $table->dropColumn('position');
            }
        });
    }
};
