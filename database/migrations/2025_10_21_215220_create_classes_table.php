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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('mentor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('schedule_rule')->comment('RRULE-like string for recurring schedule');
            $table->integer('capacity')->default(30);
            $table->integer('duration_weeks')->default(12);
            $table->timestamps();

            // Indexes for performance
            $table->index(['mentor_id']);
            $table->index(['schedule_rule']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
