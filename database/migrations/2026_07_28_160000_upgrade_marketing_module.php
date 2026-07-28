<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('leads', 'assigned_to')) {
                $table->foreignId('assigned_to')->nullable()->after('course_id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('leads', 'converted_user_id')) {
                $table->foreignId('converted_user_id')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('leads', 'converted_at')) {
                $table->timestamp('converted_at')->nullable()->after('converted_user_id');
            }
            if (! Schema::hasColumn('leads', 'stage')) {
                $table->string('stage', 40)->default('new')->after('status');
            }
            if (! Schema::hasColumn('leads', 'meta')) {
                $table->json('meta')->nullable()->after('notes');
            }
        });

        Schema::table('campaigns', function (Blueprint $table) {
            if (! Schema::hasColumn('campaigns', 'segment_id')) {
                $table->foreignId('segment_id')->nullable()->after('created_by')->constrained('segments')->nullOnDelete();
            }
            if (! Schema::hasColumn('campaigns', 'subject')) {
                $table->string('subject')->nullable()->after('title');
            }
            if (! Schema::hasColumn('campaigns', 'audience_count')) {
                $table->unsignedInteger('audience_count')->default(0)->after('status');
            }
            if (! Schema::hasColumn('campaigns', 'sent_count')) {
                $table->unsignedInteger('sent_count')->default(0)->after('audience_count');
            }
            if (! Schema::hasColumn('campaigns', 'failed_count')) {
                $table->unsignedInteger('failed_count')->default(0)->after('sent_count');
            }
            if (! Schema::hasColumn('campaigns', 'meta')) {
                $table->json('meta')->nullable()->after('content');
            }
        });

        try {
            DB::statement("ALTER TABLE campaigns MODIFY channel ENUM('email','sms','whatsapp','both','email_sms','all') NOT NULL DEFAULT 'email'");
        } catch (\Throwable) {
            // ignore non-MySQL
        }

        Schema::table('coupons', function (Blueprint $table) {
            if (! Schema::hasColumn('coupons', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('coupons', 'per_user_limit')) {
                $table->unsignedInteger('per_user_limit')->nullable()->after('max_uses');
            }
            if (! Schema::hasColumn('coupons', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
        });

        if (! Schema::hasTable('coupon_course')) {
            Schema::create('coupon_course', function (Blueprint $table) {
                $table->id();
                $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->unique(['coupon_id', 'course_id']);
            });
        }

        if (! Schema::hasTable('campaign_sends')) {
            Schema::create('campaign_sends', function (Blueprint $table) {
                $table->id();
                $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
                $table->string('channel', 30)->default('email');
                $table->string('recipient', 255)->nullable();
                $table->enum('status', ['queued', 'sent', 'failed', 'skipped'])->default('queued');
                $table->string('provider_message_id')->nullable();
                $table->text('error')->nullable();
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();

                $table->index(['campaign_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_sends');
        Schema::dropIfExists('coupon_course');

        Schema::table('coupons', function (Blueprint $table) {
            foreach (['created_by', 'per_user_limit', 'description'] as $col) {
                if (Schema::hasColumn('coupons', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('campaigns', function (Blueprint $table) {
            foreach (['segment_id', 'subject', 'audience_count', 'sent_count', 'failed_count', 'meta'] as $col) {
                if (Schema::hasColumn('campaigns', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('leads', function (Blueprint $table) {
            foreach (['created_by', 'assigned_to', 'converted_user_id', 'converted_at', 'stage', 'meta'] as $col) {
                if (Schema::hasColumn('leads', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
