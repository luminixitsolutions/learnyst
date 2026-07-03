<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (! Schema::hasColumn('courses', 'subtitle')) {
                $table->string('subtitle')->nullable()->after('title');
            }
            if (! Schema::hasColumn('courses', 'intro_video_url')) {
                $table->string('intro_video_url')->nullable()->after('thumbnail');
            }
            if (! Schema::hasColumn('courses', 'sale_price')) {
                $table->decimal('sale_price', 12, 2)->nullable()->after('price');
            }
            if (! Schema::hasColumn('courses', 'validity_days')) {
                $table->unsignedInteger('validity_days')->nullable()->after('expiry_date');
            }
            if (! Schema::hasColumn('courses', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('meta');
            }
            if (! Schema::hasColumn('courses', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }
        });

        if (Schema::hasColumn('course_lessons', 'lesson_type')) {
            DB::statement("ALTER TABLE course_lessons MODIFY lesson_type ENUM('video','pdf','text','quiz','assignment','live_class') NOT NULL DEFAULT 'video'");
        }

        Schema::table('course_lessons', function (Blueprint $table) {
            if (! Schema::hasColumn('course_lessons', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('is_preview');
            }
            if (! Schema::hasColumn('course_lessons', 'drip_date')) {
                $table->date('drip_date')->nullable()->after('sort_order');
            }
        });

        Schema::table('course_sections', function (Blueprint $table) {
            if (! Schema::hasColumn('course_sections', 'drip_date')) {
                $table->date('drip_date')->nullable()->after('drip_days');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            if (! Schema::hasColumn('users', 'notes')) {
                $table->text('notes')->nullable()->after('address');
            }
            if (! Schema::hasColumn('users', 'expertise')) {
                $table->string('expertise')->nullable()->after('bio');
            }
        });

        Schema::table('course_enrollments', function (Blueprint $table) {
            if (! Schema::hasColumn('course_enrollments', 'access_type')) {
                $table->enum('access_type', ['free', 'trial', 'paid'])->default('paid')->after('status');
            }
            if (! Schema::hasColumn('course_enrollments', 'amount')) {
                $table->decimal('amount', 12, 2)->nullable()->after('access_type');
            }
            if (! Schema::hasColumn('course_enrollments', 'meta')) {
                $table->json('meta')->nullable()->after('amount');
            }
            if (! Schema::hasColumn('course_enrollments', 'show_custom_fields')) {
                $table->boolean('show_custom_fields')->default(false)->after('meta');
            }
        });

        Schema::table('batches', function (Blueprint $table) {
            if (! Schema::hasColumn('batches', 'is_free')) {
                $table->boolean('is_free')->default(false)->after('price');
            }
            if (! Schema::hasColumn('batches', 'quiz_type')) {
                $table->enum('quiz_type', ['online', 'offline'])->default('online')->after('template');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->nullOnDelete();
            }
            if (! Schema::hasColumn('categories', 'image')) {
                $table->string('image')->nullable()->after('icon');
            }
        });

        Schema::table('bundles', function (Blueprint $table) {
            if (! Schema::hasColumn('bundles', 'sale_price')) {
                $table->decimal('sale_price', 12, 2)->nullable()->after('price');
            }
            if (! Schema::hasColumn('bundles', 'validity_days')) {
                $table->unsignedInteger('validity_days')->nullable()->after('sale_price');
            }
        });

        Schema::table('resources', function (Blueprint $table) {
            if (! Schema::hasColumn('resources', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('status');
            }
        });

        Schema::table('checkout_consents', function (Blueprint $table) {
            if (! Schema::hasColumn('checkout_consents', 'show_on_checkout')) {
                $table->boolean('show_on_checkout')->default(true)->after('is_active');
            }
        });

        Schema::table('certificate_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('certificate_templates', 'certificate_title')) {
                $table->string('certificate_title')->nullable()->after('name');
            }
            if (! Schema::hasColumn('certificate_templates', 'layout_config')) {
                $table->json('layout_config')->nullable()->after('background_image');
            }
            if (! Schema::hasColumn('certificate_templates', 'signature_image')) {
                $table->string('signature_image')->nullable()->after('layout_config');
            }
            if (! Schema::hasColumn('certificate_templates', 'seal_image')) {
                $table->string('seal_image')->nullable()->after('signature_image');
            }
            if (! Schema::hasColumn('certificate_templates', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('is_default');
            }
        });

        Schema::table('scheduled_events', function (Blueprint $table) {
            if (! Schema::hasColumn('scheduled_events', 'instructor_id')) {
                $table->foreignId('instructor_id')->nullable()->after('course_id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('scheduled_events', 'platform')) {
                $table->enum('platform', ['zoom', 'google_meet', 'youtube', 'other'])->default('zoom')->after('meeting_url');
            }
            if (! Schema::hasColumn('scheduled_events', 'status')) {
                $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled')->after('platform');
            }
        });

        if (! Schema::hasTable('website_sections')) {
            Schema::create('website_sections', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('section_key')->unique();
                $table->string('heading')->nullable();
                $table->string('sub_heading')->nullable();
                $table->text('description')->nullable();
                $table->string('button_text')->nullable();
                $table->string('button_link')->nullable();
                $table->string('image')->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_visible')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('website_sections');

        Schema::table('scheduled_events', function (Blueprint $table) {
            if (Schema::hasColumn('scheduled_events', 'instructor_id')) {
                $table->dropForeign(['instructor_id']);
                $table->dropColumn(['instructor_id', 'platform', 'status']);
            }
        });

        Schema::table('certificate_templates', function (Blueprint $table) {
            $table->dropColumn(['certificate_title', 'layout_config', 'signature_image', 'seal_image', 'status']);
        });

        Schema::table('checkout_consents', function (Blueprint $table) {
            $table->dropColumn('show_on_checkout');
        });

        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn('published_at');
        });

        Schema::table('bundles', function (Blueprint $table) {
            $table->dropColumn(['sale_price', 'validity_days']);
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn(['parent_id', 'image']);
            }
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn(['is_free', 'quiz_type']);
        });

        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropColumn(['access_type', 'amount', 'meta', 'show_custom_fields']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['address', 'notes', 'expertise']);
        });

        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropColumn('drip_date');
        });

        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'drip_date']);
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['subtitle', 'intro_video_url', 'sale_price', 'validity_days', 'seo_title', 'seo_description']);
        });
    }
};
