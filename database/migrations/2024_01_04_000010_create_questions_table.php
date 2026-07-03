<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_pool_id')->nullable()->constrained('question_pools')->nullOnDelete();
            $table->text('question_text');
            $table->enum('question_type', ['mcq', 'true_false', 'essay', 'fill_blank'])->default('mcq');
            $table->json('options')->nullable();
            $table->text('correct_answer')->nullable();
            $table->enum('status', ['draft', 'published', 'unpublished'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
