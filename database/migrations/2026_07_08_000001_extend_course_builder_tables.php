<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            if (! Schema::hasColumn('course_sections', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('sort_order');
            }
        });

        if (Schema::hasColumn('course_lessons', 'lesson_type')) {
            DB::statement("ALTER TABLE course_lessons MODIFY lesson_type ENUM('video','audio','pdf','text','quiz','assignment','live_class','code','external_link') NOT NULL DEFAULT 'video'");
        }

        Schema::table('course_lessons', function (Blueprint $table) {
            if (! Schema::hasColumn('course_lessons', 'status')) {
                $table->enum('status', ['draft', 'published'])->default('draft')->after('lesson_type');
            }
            if (! Schema::hasColumn('course_lessons', 'drip_enabled')) {
                $table->boolean('drip_enabled')->default(false)->after('drip_date');
            }
            if (! Schema::hasColumn('course_lessons', 'completion_required')) {
                $table->boolean('completion_required')->default(false)->after('drip_enabled');
            }
            if (! Schema::hasColumn('course_lessons', 'allow_download')) {
                $table->boolean('allow_download')->default(false)->after('completion_required');
            }
            if (! Schema::hasColumn('course_lessons', 'external_url')) {
                $table->string('external_url')->nullable()->after('video_url');
            }
            if (! Schema::hasColumn('course_lessons', 'media_processing_status')) {
                $table->enum('media_processing_status', ['none', 'pending', 'processing', 'encryption', 'ready', 'failed'])->default('none')->after('file_path');
            }
            if (! Schema::hasColumn('course_lessons', 'settings')) {
                $table->json('settings')->nullable()->after('quiz_data');
            }
        });

        if (! Schema::hasTable('lesson_media')) {
            Schema::create('lesson_media', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_lesson_id')->constrained()->cascadeOnDelete();
                $table->enum('media_type', ['video', 'audio', 'pdf', 'image'])->default('video');
                $table->string('file_path')->nullable();
                $table->string('file_url')->nullable();
                $table->string('mime_type')->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->unsignedInteger('duration_seconds')->nullable();
                $table->enum('processing_status', ['pending', 'processing', 'encryption', 'ready', 'failed'])->default('pending');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('lesson_attachments')) {
            Schema::create('lesson_attachments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_lesson_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->string('file_path');
                $table->string('file_type')->nullable();
                $table->boolean('download_allowed')->default(true);
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('live_classes')) {
            Schema::create('live_classes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_lesson_id')->unique()->constrained()->cascadeOnDelete();
                $table->string('live_class_type')->default('super_live');
                $table->unsignedInteger('super_live_capacity')->nullable();
                $table->dateTime('starts_at')->nullable();
                $table->unsignedTinyInteger('duration_hours')->default(0);
                $table->unsignedTinyInteger('duration_minutes')->default(0);
                $table->string('recording_layout_mode')->nullable();
                $table->boolean('new_recording')->default(true);
                $table->boolean('enable_participant_list')->default(true);
                $table->boolean('chat_box')->default(true);
                $table->boolean('enable_qa')->default(true);
                $table->boolean('show_whiteboard')->default(false);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('course_settings')) {
            Schema::create('course_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->unique()->constrained()->cascadeOnDelete();
                $table->boolean('certificate_enabled')->default(false);
                $table->boolean('discussion_enabled')->default(false);
                $table->boolean('reviews_enabled')->default(true);
                $table->unsignedInteger('max_video_upload_mb')->default(512);
                $table->unsignedInteger('max_audio_upload_mb')->default(100);
                $table->unsignedInteger('max_pdf_upload_mb')->default(50);
                $table->json('extra')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('course_settings');
        Schema::dropIfExists('live_classes');
        Schema::dropIfExists('lesson_attachments');
        Schema::dropIfExists('lesson_media');

        Schema::table('course_lessons', function (Blueprint $table) {
            $table->dropColumn([
                'status', 'drip_enabled', 'completion_required', 'allow_download',
                'external_url', 'media_processing_status', 'settings',
            ]);
        });

        if (Schema::hasColumn('course_lessons', 'lesson_type')) {
            DB::statement("ALTER TABLE course_lessons MODIFY lesson_type ENUM('video','pdf','text','quiz','assignment','live_class') NOT NULL DEFAULT 'video'");
        }

        Schema::table('course_sections', function (Blueprint $table) {
            if (Schema::hasColumn('course_sections', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
