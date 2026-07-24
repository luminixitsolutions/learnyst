<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('course_settings', 'bookmarks_enabled')) {
                $table->boolean('bookmarks_enabled')->default(true)->after('reviews_enabled');
            }
            if (! Schema::hasColumn('course_settings', 'leaderboard_enabled')) {
                $table->boolean('leaderboard_enabled')->default(false)->after('bookmarks_enabled');
            }
            if (! Schema::hasColumn('course_settings', 'drip_mode')) {
                $table->string('drip_mode', 40)->default('immediate')->after('leaderboard_enabled');
            }
            if (! Schema::hasColumn('course_settings', 'learning_path_enabled')) {
                $table->boolean('learning_path_enabled')->default(false)->after('drip_mode');
            }
            if (! Schema::hasColumn('course_settings', 'sell_independently')) {
                $table->boolean('sell_independently')->default(true)->after('learning_path_enabled');
            }
            if (! Schema::hasColumn('course_settings', 'access_visibility')) {
                $table->string('access_visibility', 40)->default('public')->after('sell_independently');
            }
            if (! Schema::hasColumn('course_settings', 'selling_platforms')) {
                $table->json('selling_platforms')->nullable()->after('access_visibility');
            }
            if (! Schema::hasColumn('course_settings', 'permissions')) {
                $table->json('permissions')->nullable()->after('selling_platforms');
            }
            if (! Schema::hasColumn('course_settings', 'review_config')) {
                $table->json('review_config')->nullable()->after('permissions');
            }
            if (! Schema::hasColumn('course_settings', 'discussion_config')) {
                $table->json('discussion_config')->nullable()->after('review_config');
            }
            if (! Schema::hasColumn('course_settings', 'bookmark_config')) {
                $table->json('bookmark_config')->nullable()->after('discussion_config');
            }
            if (! Schema::hasColumn('course_settings', 'leaderboard_config')) {
                $table->json('leaderboard_config')->nullable()->after('bookmark_config');
            }
            if (! Schema::hasColumn('course_settings', 'certificate_config')) {
                $table->json('certificate_config')->nullable()->after('leaderboard_config');
            }
            if (! Schema::hasColumn('course_settings', 'drip_config')) {
                $table->json('drip_config')->nullable()->after('certificate_config');
            }
            if (! Schema::hasColumn('course_settings', 'learner_config')) {
                $table->json('learner_config')->nullable()->after('drip_config');
            }
            if (! Schema::hasColumn('course_settings', 'learning_path_config')) {
                $table->json('learning_path_config')->nullable()->after('learner_config');
            }
            if (! Schema::hasColumn('course_settings', 'android_pricing')) {
                $table->json('android_pricing')->nullable()->after('learning_path_config');
            }
            if (! Schema::hasColumn('course_settings', 'ios_pricing')) {
                $table->json('ios_pricing')->nullable()->after('android_pricing');
            }
            if (! Schema::hasColumn('course_settings', 'branding')) {
                $table->json('branding')->nullable()->after('ios_pricing');
            }
            if (! Schema::hasColumn('course_settings', 'seo')) {
                $table->json('seo')->nullable()->after('branding');
            }
            if (! Schema::hasColumn('course_settings', 'trash_retention_days')) {
                $table->unsignedInteger('trash_retention_days')->default(30)->after('seo');
            }
            if (! Schema::hasColumn('course_settings', 'deleted_by')) {
                $table->foreignId('deleted_by')->nullable()->after('trash_retention_days')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('course_settings', 'deletion_reason')) {
                $table->text('deletion_reason')->nullable()->after('deleted_by');
            }
            if (! Schema::hasColumn('course_settings', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('deletion_reason');
            }
            if (! Schema::hasColumn('course_settings', 'published_by')) {
                $table->foreignId('published_by')->nullable()->after('published_at')->constrained('users')->nullOnDelete();
            }
        });

        if (! Schema::hasTable('course_publication_histories')) {
            Schema::create('course_publication_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->string('from_status', 40)->nullable();
                $table->string('to_status', 40);
                $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->index(['course_id', 'created_at']);
            });
        }

        if (! Schema::hasTable('course_setting_audit_logs')) {
            Schema::create('course_setting_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->string('section', 60);
                $table->string('action', 60);
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->json('before')->nullable();
                $table->json('after')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
                $table->index(['course_id', 'section']);
            });
        }

        if (! Schema::hasTable('course_pricing_plans')) {
            Schema::create('course_pricing_plans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->string('slug')->nullable();
                $table->enum('plan_type', [
                    'free', 'one_time', 'limited_offer', 'subscription', 'installment', 'custom',
                ])->default('one_time');
                $table->enum('status', ['draft', 'published', 'unpublished', 'archived'])->default('draft');
                $table->string('currency', 10)->default('INR');
                $table->decimal('regular_price', 12, 2)->nullable();
                $table->decimal('offer_price', 12, 2)->nullable();
                $table->boolean('tax_inclusive')->default(false);
                $table->text('description')->nullable();
                $table->boolean('is_public')->default(true);
                $table->boolean('lifetime_access')->default(false);
                $table->unsignedInteger('validity_days')->nullable();
                $table->timestamp('access_expires_at')->nullable();
                $table->timestamp('purchase_starts_at')->nullable();
                $table->timestamp('purchase_ends_at')->nullable();
                $table->timestamp('offer_starts_at')->nullable();
                $table->timestamp('offer_ends_at')->nullable();
                $table->boolean('show_countdown')->default(false);
                $table->unsignedInteger('enrollment_limit')->nullable();
                $table->unsignedInteger('enrollment_count')->default(0);
                $table->unsignedInteger('sales_count')->default(0);
                $table->boolean('coupon_eligible')->default(true);
                $table->string('billing_frequency', 40)->nullable();
                $table->unsignedInteger('trial_days')->default(0);
                $table->decimal('setup_fee', 12, 2)->nullable();
                $table->unsignedInteger('billing_cycles')->nullable();
                $table->boolean('auto_renew')->default(true);
                $table->json('installment_config')->nullable();
                $table->json('refund_config')->nullable();
                $table->json('meta')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['course_id', 'status']);
            });
        }

        if (! Schema::hasTable('course_reviews')) {
            Schema::create('course_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->unsignedTinyInteger('rating');
                $table->text('review')->nullable();
                $table->boolean('is_anonymous')->default(false);
                $table->enum('status', ['pending', 'approved', 'rejected', 'hidden'])->default('pending');
                $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('moderated_at')->nullable();
                $table->text('moderation_note')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['course_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('course_certificate_criteria')) {
            Schema::create('course_certificate_criteria', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->string('criterion_type', 60);
                $table->string('logic', 10)->default('and');
                $table->boolean('is_mandatory')->default(true);
                $table->json('config')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('course_learner_removals')) {
            Schema::create('course_learner_removals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('enrollment_id')->nullable()->constrained('course_enrollments')->nullOnDelete();
                $table->foreignId('removed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('reason')->nullable();
                $table->json('snapshot')->nullable();
                $table->timestamp('restored_at')->nullable();
                $table->foreignId('restored_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->index(['course_id', 'user_id']);
            });
        }

        if (! Schema::hasTable('course_faqs')) {
            Schema::create('course_faqs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained()->cascadeOnDelete();
                $table->string('question');
                $table->text('answer');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_faqs');
        Schema::dropIfExists('course_learner_removals');
        Schema::dropIfExists('course_certificate_criteria');
        Schema::dropIfExists('course_reviews');
        Schema::dropIfExists('course_pricing_plans');
        Schema::dropIfExists('course_setting_audit_logs');
        Schema::dropIfExists('course_publication_histories');

        Schema::table('course_settings', function (Blueprint $table) {
            $columns = [
                'bookmarks_enabled', 'leaderboard_enabled', 'drip_mode', 'learning_path_enabled',
                'sell_independently', 'access_visibility', 'selling_platforms', 'permissions',
                'review_config', 'discussion_config', 'bookmark_config', 'leaderboard_config',
                'certificate_config', 'drip_config', 'learner_config', 'learning_path_config',
                'android_pricing', 'ios_pricing', 'branding', 'seo', 'trash_retention_days',
                'deletion_reason', 'published_at',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('course_settings', $column)) {
                    $table->dropColumn($column);
                }
            }
            if (Schema::hasColumn('course_settings', 'deleted_by')) {
                $table->dropConstrainedForeignId('deleted_by');
            }
            if (Schema::hasColumn('course_settings', 'published_by')) {
                $table->dropConstrainedForeignId('published_by');
            }
        });
    }
};
