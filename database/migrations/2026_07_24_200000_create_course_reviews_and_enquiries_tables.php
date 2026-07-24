<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('course_reviews')) {
            Schema::create('course_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('reviewer_name', 120)->nullable();
                $table->string('reviewer_email')->nullable();
                $table->unsignedTinyInteger('rating')->default(5);
                $table->text('review');
                $table->boolean('is_anonymous')->default(false);
                $table->string('status', 20)->default('pending'); // pending|approved|rejected
                $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('moderated_at')->nullable();
                $table->text('moderation_note')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['course_id', 'status']);
            });
        } else {
            Schema::table('course_reviews', function (Blueprint $table) {
                if (! Schema::hasColumn('course_reviews', 'reviewer_name')) {
                    $table->string('reviewer_name', 120)->nullable()->after('user_id');
                }
                if (! Schema::hasColumn('course_reviews', 'reviewer_email')) {
                    $table->string('reviewer_email')->nullable()->after('reviewer_name');
                }
            });
        }

        if (! Schema::hasTable('course_enquiries')) {
            Schema::create('course_enquiries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->string('name', 120);
                $table->string('email');
                $table->string('phone', 40)->nullable();
                $table->string('subject', 180)->nullable();
                $table->text('message');
                $table->string('status', 20)->default('new'); // new|read|replied
                $table->timestamps();

                $table->index(['course_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_enquiries');
        Schema::dropIfExists('course_reviews');
    }
};
