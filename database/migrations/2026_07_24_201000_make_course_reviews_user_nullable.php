<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('course_reviews') && Schema::hasColumn('course_reviews', 'user_id')) {
            Schema::table('course_reviews', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
        }

        if (Schema::hasTable('course_reviews') && ! Schema::hasColumn('course_reviews', 'reviewer_name')) {
            Schema::table('course_reviews', function (Blueprint $table) {
                $table->string('reviewer_name', 120)->nullable()->after('user_id');
                $table->string('reviewer_email')->nullable()->after('reviewer_name');
            });
        }
    }

    public function down(): void
    {
        //
    }
};
