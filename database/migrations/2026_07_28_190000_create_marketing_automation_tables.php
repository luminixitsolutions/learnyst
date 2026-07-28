<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('trigger_key', 60);
            $table->json('trigger_config')->nullable();
            $table->json('actions');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('run_count')->default(0);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();

            $table->index(['created_by', 'trigger_key', 'is_active']);
        });

        Schema::create('automation_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_workflow_id')->constrained()->cascadeOnDelete();
            $table->string('trigger_key', 60);
            $table->nullableMorphs('subject');
            $table->enum('status', ['queued', 'running', 'completed', 'failed'])->default('queued');
            $table->json('context')->nullable();
            $table->json('result')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });

        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('headline')->nullable();
            $table->text('body')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('hero_image')->nullable();
            $table->json('blocks')->nullable();
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('cta_clicks')->default(0);
            $table->unsignedInteger('leads_captured')->default(0);
            $table->timestamps();
        });

        Schema::create('landing_page_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_page_id')->constrained()->cascadeOnDelete();
            $table->string('event_type', 40);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::table('webinars', function (Blueprint $table) {
            if (! Schema::hasColumn('webinars', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (! Schema::hasColumn('webinars', 'starts_at')) {
                $table->timestamp('starts_at')->nullable()->after('status');
            }
            if (! Schema::hasColumn('webinars', 'registration_enabled')) {
                $table->boolean('registration_enabled')->default(true)->after('starts_at');
            }
            if (! Schema::hasColumn('webinars', 'reminder_hours_before')) {
                $table->unsignedInteger('reminder_hours_before')->default(24)->after('registration_enabled');
            }
            if (! Schema::hasColumn('webinars', 'confirmation_message')) {
                $table->text('confirmation_message')->nullable()->after('reminder_hours_before');
            }
            if (! Schema::hasColumn('webinars', 'meeting_url')) {
                $table->string('meeting_url')->nullable()->after('confirmation_message');
            }
        });

        Schema::create('webinar_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webinar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->enum('status', ['registered', 'confirmed', 'attended', 'cancelled'])->default('registered');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['webinar_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webinar_registrations');
        Schema::dropIfExists('landing_page_events');
        Schema::dropIfExists('landing_pages');
        Schema::dropIfExists('automation_runs');
        Schema::dropIfExists('automation_workflows');

        Schema::table('webinars', function (Blueprint $table) {
            foreach (['description', 'starts_at', 'registration_enabled', 'reminder_hours_before', 'confirmation_message', 'meeting_url'] as $col) {
                if (Schema::hasColumn('webinars', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
