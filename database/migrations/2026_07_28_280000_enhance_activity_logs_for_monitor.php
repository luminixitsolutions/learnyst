<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('activity_logs', 'user_agent')) {
                $table->string('user_agent', 500)->nullable()->after('ip_address');
            }
            if (! Schema::hasColumn('activity_logs', 'company_id')) {
                $table->foreignId('company_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('companies')
                    ->nullOnDelete();
            }
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            try {
                $table->index(['action', 'created_at'], 'activity_logs_action_created_at_index');
            } catch (\Throwable) {
            }
            try {
                $table->index(['company_id', 'created_at'], 'activity_logs_company_id_created_at_index');
            } catch (\Throwable) {
            }
            try {
                $table->index('ip_address', 'activity_logs_ip_address_index');
            } catch (\Throwable) {
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (Schema::hasColumn('activity_logs', 'company_id')) {
                $table->dropConstrainedForeignId('company_id');
            }
            if (Schema::hasColumn('activity_logs', 'user_agent')) {
                $table->dropColumn('user_agent');
            }
        });
    }
};
