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
        Schema::create('ai_comparison_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('comparison_program_id');
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->string('model')->nullable();
            $table->longText('report_markdown')->nullable();
            $table->string('comparison_program_name')->nullable();
            $table->timestamps();

            $table->foreign('program_id')
                ->references('program_id')
                ->on('programs')
                ->cascadeOnDelete();

            $table->foreign('comparison_program_id')
                ->references('program_id')
                ->on('programs')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_comparison_logs');
    }
};

