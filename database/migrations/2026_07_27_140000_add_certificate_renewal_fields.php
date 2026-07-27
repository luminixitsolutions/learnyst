<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->unsignedSmallInteger('validity_years')->nullable()->after('status');
            $table->unsignedInteger('validity_days')->nullable()->after('validity_years');
            $table->decimal('renewal_price', 12, 2)->nullable()->after('validity_days');
            $table->boolean('requires_renewal_assessment')->default(false)->after('renewal_price');
        });

        Schema::table('certificates', function (Blueprint $table) {
            $table->enum('status', ['valid', 'expiring_soon', 'expired', 'renewal_due'])
                ->default('valid')
                ->after('issued_at');
            $table->timestamp('expires_at')->nullable()->after('status');
            $table->foreignId('renewed_from_id')->nullable()->after('expires_at')
                ->constrained('certificates')->nullOnDelete();
            $table->unsignedInteger('renewal_count')->default(0)->after('renewed_from_id');
            $table->timestamp('last_reminder_at')->nullable()->after('renewal_count');
            $table->unsignedTinyInteger('last_reminder_days')->nullable()->after('last_reminder_at');
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropForeign(['renewed_from_id']);
            $table->dropColumn([
                'status',
                'expires_at',
                'renewed_from_id',
                'renewal_count',
                'last_reminder_at',
                'last_reminder_days',
            ]);
        });

        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->dropColumn([
                'validity_years',
                'validity_days',
                'renewal_price',
                'requires_renewal_assessment',
            ]);
        });
    }
};
