<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('program_learning_outcomes', function (Blueprint $table) {
            $table->integer('position')->nullable()->default(null)->after('plo_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('program_learning_outcomes', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};