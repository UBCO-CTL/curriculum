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
        Schema::create('course_clone_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs', 'program_id')->cascadeOnDelete();
            $table->foreignId('original_course_id')->constrained('courses', 'course_id')->cascadeOnDelete();
            $table->foreignId('original_program_id')->constrained('programs', 'program_id')->cascadeOnDelete();
            $table->foreignId('requested_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 32)->default('pending');
            $table->string('token_hash', 128)->unique();
            $table->foreignId('responded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cloned_course_id')->nullable()->constrained('courses', 'course_id')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('last_notified_at')->nullable();
            $table->integer('course_required')->nullable();
            $table->integer('instructor_assigned')->nullable();
            $table->integer('map_status')->default(0);
            $table->string('note', 191)->nullable();
            $table->timestamps();

            $table->index(['program_id', 'status']);
            $table->index(['original_course_id', 'status']);
            $table->index(['original_program_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_clone_requests');
    }
};
