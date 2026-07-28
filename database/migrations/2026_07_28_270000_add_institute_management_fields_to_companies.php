<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_public');
            }
            if (! Schema::hasColumn('companies', 'subscription_package_id')) {
                $table->foreignId('subscription_package_id')
                    ->nullable()
                    ->after('owner_user_id')
                    ->constrained('subscription_packages')
                    ->nullOnDelete();
            }
            if (! Schema::hasColumn('companies', 'package_assigned_at')) {
                $table->timestamp('package_assigned_at')->nullable()->after('subscription_package_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'subscription_package_id')) {
                $table->dropConstrainedForeignId('subscription_package_id');
            }
            foreach (['is_active', 'package_assigned_at'] as $col) {
                if (Schema::hasColumn('companies', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
