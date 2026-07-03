<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_copy_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source_title');
            $table->enum('product_type', ['course', 'mock-test', 'test-series']);
            $table->string('destination_title');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_copy_logs');
    }
};
