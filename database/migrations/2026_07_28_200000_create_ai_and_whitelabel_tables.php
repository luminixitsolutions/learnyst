<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('feature', 60);
            $table->string('title')->nullable();
            $table->text('prompt');
            $table->longText('output')->nullable();
            $table->enum('status', ['draft', 'approved', 'published', 'rejected'])->default('draft');
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['created_by', 'feature', 'status']);
        });

        Schema::create('ai_chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('role', ['user', 'assistant', 'system'])->default('user');
            $table->text('content');
            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'custom_domain')) {
                $table->string('custom_domain')->nullable()->after('website_url');
            }
            if (! Schema::hasColumn('companies', 'domain_verification_token')) {
                $table->string('domain_verification_token')->nullable()->after('custom_domain');
            }
            if (! Schema::hasColumn('companies', 'domain_verified_at')) {
                $table->timestamp('domain_verified_at')->nullable()->after('domain_verification_token');
            }
            if (! Schema::hasColumn('companies', 'favicon')) {
                $table->string('favicon')->nullable()->after('logo');
            }
            if (! Schema::hasColumn('companies', 'primary_color')) {
                $table->string('primary_color', 20)->nullable()->after('favicon');
            }
            if (! Schema::hasColumn('companies', 'secondary_color')) {
                $table->string('secondary_color', 20)->nullable()->after('primary_color');
            }
            if (! Schema::hasColumn('companies', 'theme_tokens')) {
                $table->json('theme_tokens')->nullable()->after('secondary_color');
            }
            if (! Schema::hasColumn('companies', 'email_from_name')) {
                $table->string('email_from_name')->nullable()->after('theme_tokens');
            }
            if (! Schema::hasColumn('companies', 'email_from_address')) {
                $table->string('email_from_address')->nullable()->after('email_from_name');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_chat_messages');
        Schema::dropIfExists('ai_generations');

        Schema::table('companies', function (Blueprint $table) {
            foreach ([
                'custom_domain', 'domain_verification_token', 'domain_verified_at', 'favicon',
                'primary_color', 'secondary_color', 'theme_tokens', 'email_from_name', 'email_from_address',
            ] as $col) {
                if (Schema::hasColumn('companies', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
