<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['facebook_id', 'apple_id', 'linkedin_id'] as $col) {
                if (! Schema::hasColumn('users', $col)) {
                    $table->string($col)->nullable()->unique()->after('google_id');
                }
            }
            if (! Schema::hasColumn('users', 'two_factor_secret')) {
                $table->text('two_factor_secret')->nullable()->after('password');
            }
            if (! Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('two_factor_secret');
            }
            if (! Schema::hasColumn('users', 'two_factor_recovery_codes')) {
                $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_enabled');
            }
            if (! Schema::hasColumn('users', 'two_factor_confirmed_at')) {
                $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
            }
            if (! Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            }
        });

        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel', 20); // email, sms
            $table->string('destination');
            $table->string('purpose', 40); // login, verify_email, verify_phone, 2fa
            $table->string('code_hash');
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['destination', 'purpose', 'expires_at']);
        });

        Schema::create('login_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('device_id', 64)->index();
            $table->string('device_name')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('session_id')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->boolean('is_trusted')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'device_id']);
        });

        Schema::create('login_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_id', 64)->nullable();
            $table->enum('status', ['success', 'failed', 'blocked', '2fa_required'])->default('success');
            $table->string('provider', 40)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        Schema::create('ip_access_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('scope', ['company', 'platform'])->default('company');
            $table->enum('rule_type', ['allow', 'deny'])->default('allow');
            $table->string('ip_cidr', 64);
            $table->string('label')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('media_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_lesson_id')->nullable()->constrained()->nullOnDelete();
            $table->string('token', 80)->unique();
            $table->timestamp('expires_at');
            $table->unsignedInteger('max_seconds')->nullable();
            $table->unsignedInteger('watched_seconds')->default(0);
            $table->string('device_id', 64)->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });

        Schema::table('course_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('course_settings', 'drm_config')) {
                $table->json('drm_config')->nullable()->after('learner_config');
            }
        });

        Schema::table('scheduled_events', function (Blueprint $table) {
            if (! Schema::hasColumn('scheduled_events', 'meeting_id')) {
                $table->string('meeting_id')->nullable()->after('meeting_url');
            }
            if (! Schema::hasColumn('scheduled_events', 'meeting_passcode')) {
                $table->string('meeting_passcode')->nullable()->after('meeting_id');
            }
            if (! Schema::hasColumn('scheduled_events', 'recording_url')) {
                $table->string('recording_url')->nullable()->after('meeting_passcode');
            }
        });

        try {
            DB::statement("ALTER TABLE scheduled_events MODIFY platform ENUM('zoom','google_meet','youtube','microsoft_teams','other') NOT NULL DEFAULT 'zoom'");
        } catch (\Throwable) {
            // non-MySQL
        }

        Schema::table('communities', function (Blueprint $table) {
            if (! Schema::hasColumn('communities', 'telegram_invite_url')) {
                $table->string('telegram_invite_url')->nullable()->after('cover_image');
            }
            if (! Schema::hasColumn('communities', 'telegram_chat_id')) {
                $table->string('telegram_chat_id')->nullable()->after('telegram_invite_url');
            }
            if (! Schema::hasColumn('communities', 'telegram_push_enabled')) {
                $table->boolean('telegram_push_enabled')->default(false)->after('telegram_chat_id');
            }
        });

        Schema::create('community_announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('body');
            $table->boolean('pushed_to_telegram')->default(false);
            $table->timestamp('telegram_pushed_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_announcements');
        Schema::dropIfExists('media_access_tokens');
        Schema::dropIfExists('ip_access_rules');
        Schema::dropIfExists('login_histories');
        Schema::dropIfExists('login_devices');
        Schema::dropIfExists('otp_codes');

        Schema::table('users', function (Blueprint $table) {
            foreach ([
                'facebook_id', 'apple_id', 'linkedin_id', 'two_factor_secret', 'two_factor_enabled',
                'two_factor_recovery_codes', 'two_factor_confirmed_at', 'phone_verified_at',
            ] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
