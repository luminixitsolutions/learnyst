<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_lesson_id')->constrained('course_lessons')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->string('status', 30)->default('submitted'); // submitted | graded | resubmit
            $table->decimal('score', 8, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->boolean('allow_resubmit')->default(false);
            $table->timestamps();

            $table->index(['course_lesson_id', 'status']);
            $table->index('user_id');
        });

        Schema::table('discussions', function (Blueprint $table) {
            if (! Schema::hasColumn('discussions', 'is_resolved')) {
                $table->boolean('is_resolved')->default(false)->after('is_reported');
            }
            if (! Schema::hasColumn('discussions', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable()->after('is_resolved');
            }
            if (! Schema::hasColumn('discussions', 'resolved_by')) {
                $table->foreignId('resolved_by')->nullable()->after('resolved_at')->constrained('users')->nullOnDelete();
            }
        });

        Schema::create('parent_learner_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('learner_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20)->default('active'); // active | inactive
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['parent_user_id', 'learner_user_id']);
            $table->index('learner_user_id');
        });

        Schema::create('company_website_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('page_type', 40)->default('custom'); // home | about | contact | faq | testimonials | faculty | gallery | blog | custom
            $table->string('status', 20)->default('draft'); // draft | published
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->boolean('show_in_nav')->default(true);
            $table->unsignedInteger('nav_sort')->default(0);
            $table->timestamps();

            $table->unique(['company_id', 'slug']);
            $table->index(['company_id', 'status']);
        });

        Schema::create('company_website_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_website_page_id')->constrained('company_website_pages')->cascadeOnDelete();
            $table->string('block_type', 40); // hero | text | cta | testimonials | faculty | faq | gallery | form | newsletter | pricing | courses
            $table->string('title')->nullable();
            $table->json('content')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['company_website_page_id', 'sort_order']);
        });

        Schema::create('company_website_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('location', 20)->default('header'); // header | footer
            $table->string('label');
            $table->string('url')->nullable();
            $table->foreignId('page_id')->nullable()->constrained('company_website_pages')->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->index(['company_id', 'location']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_website_menus');
        Schema::dropIfExists('company_website_blocks');
        Schema::dropIfExists('company_website_pages');
        Schema::dropIfExists('parent_learner_links');
        Schema::dropIfExists('assignment_submissions');

        Schema::table('discussions', function (Blueprint $table) {
            if (Schema::hasColumn('discussions', 'resolved_by')) {
                $table->dropConstrainedForeignId('resolved_by');
            }
            foreach (['is_resolved', 'resolved_at'] as $col) {
                if (Schema::hasColumn('discussions', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
