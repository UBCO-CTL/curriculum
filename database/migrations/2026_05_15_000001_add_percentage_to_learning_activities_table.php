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
        if (! Schema::hasColumn('learning_activities', 'percentage')) {
            Schema::table('learning_activities', function (Blueprint $table) {
                $table->unsignedTinyInteger('percentage')->nullable()->after('l_activities_pos');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('learning_activities', 'percentage')) {
            Schema::table('learning_activities', function (Blueprint $table) {
                $table->dropColumn('percentage');
            });
        }
    }
};
