<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->enum('enrollment_type', ['course', 'batch', 'bundle'])->default('course')->after('id');
            $table->foreignId('batch_id')->nullable()->after('course_id')->constrained()->nullOnDelete();
            $table->foreignId('bundle_id')->nullable()->after('batch_id')->constrained()->nullOnDelete();
            $table->timestamp('access_starts_at')->nullable()->after('enrolled_at');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('description');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('module')->nullable()->after('slug');
            $table->string('action')->nullable()->after('module');
        });

        Schema::create('permission_user', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->primary(['permission_id', 'user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->json('social_links')->nullable()->after('bio');
        });

        Schema::create('sub_admin_scopes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('scope_type');
            $table->unsignedBigInteger('scope_id');
            $table->timestamps();
            $table->index(['scope_type', 'scope_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_admin_scopes');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('social_links');
        });
        Schema::dropIfExists('permission_user');
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['module', 'action']);
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropForeign(['batch_id']);
            $table->dropForeign(['bundle_id']);
            $table->dropColumn(['enrollment_type', 'batch_id', 'bundle_id', 'access_starts_at']);
        });
    }
};
