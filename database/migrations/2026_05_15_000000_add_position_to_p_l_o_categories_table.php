<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('p_l_o_categories', function (Blueprint $table) {
            $table->unsignedInteger('position')->nullable()->after('program_id');
        });

        DB::table('p_l_o_categories')
            ->select('program_id')
            ->distinct()
            ->orderBy('program_id')
            ->chunk(100, function ($programs) {
                foreach ($programs as $program) {
                    DB::table('p_l_o_categories')
                        ->where('program_id', $program->program_id)
                        ->orderBy('plo_category_id')
                        ->pluck('plo_category_id')
                        ->each(function ($categoryId, $index) {
                            DB::table('p_l_o_categories')
                                ->where('plo_category_id', $categoryId)
                                ->update(['position' => $index + 1]);
                        });
                }
            });
    }

    public function down(): void
    {
        Schema::table('p_l_o_categories', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
