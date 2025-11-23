<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained('class_sessions')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->enum('status', ['present', 'absent', 'excused'])->default('absent');
            $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->datetime('marked_at');
            $table->timestamps();

            // Unique constraint to prevent duplicate attendance records
            $table->unique(['class_session_id', 'member_id']);

            // Additional indexes for performance
            $table->index(['class_session_id']);
            $table->index(['member_id']);
            $table->index(['marked_by']);
            $table->index(['marked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
