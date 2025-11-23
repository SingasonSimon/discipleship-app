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
        Schema::create('class_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('title');
            $table->text('content')->nullable(); // Rich text content
            $table->enum('content_type', ['outline', 'lesson', 'assignment', 'resource', 'homework', 'reading', 'video', 'document'])->default('lesson');
            $table->integer('week_number')->nullable()->comment('Which week of the class this content belongs to');
            $table->integer('order')->default(0)->comment('Order within the week');
            $table->text('additional_notes')->nullable();
            $table->json('attachments')->nullable()->comment('Array of file attachments or URLs');
            $table->boolean('is_published')->default(false)->comment('Whether content is visible to enrolled members');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes for performance
            $table->index(['class_id']);
            $table->index(['class_id', 'week_number']);
            $table->index(['content_type']);
            $table->index(['is_published']);
            $table->index(['created_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_contents');
    }
};
