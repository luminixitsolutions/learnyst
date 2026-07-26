<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('certificate_templates', 'course_id')) {
                $table->foreignId('course_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
                $table->index('course_id');
            }
            if (! Schema::hasColumn('certificate_templates', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
            if (Schema::hasColumn('certificate_templates', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
            if (Schema::hasColumn('certificate_templates', 'course_id')) {
                $table->dropConstrainedForeignId('course_id');
            }
        });
    }
};
