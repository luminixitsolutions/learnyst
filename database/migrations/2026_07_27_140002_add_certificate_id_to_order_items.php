<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('item_type', 32)->default('course')->after('order_id');
            $table->foreignId('certificate_id')->nullable()->after('course_id')
                ->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['certificate_id']);
            $table->dropColumn(['item_type', 'certificate_id']);
        });
    }
};
