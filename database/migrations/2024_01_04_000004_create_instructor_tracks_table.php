<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 60);
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('content_security', ['encryption', 'no_encryption'])->default('encryption');
            $table->enum('status', ['draft', 'published', 'unpublished'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_tracks');
    }
};
