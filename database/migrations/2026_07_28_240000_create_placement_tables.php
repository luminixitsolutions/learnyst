<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('placement_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('industry')->nullable();
            $table->string('website')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('about')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('placement_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('placement_company_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['job', 'internship'])->default('job');
            $table->string('location')->nullable();
            $table->string('employment_type', 40)->nullable();
            $table->decimal('stipend_or_salary', 12, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->date('closes_at')->nullable();
            $table->enum('status', ['draft', 'open', 'closed'])->default('open');
            $table->timestamps();
        });

        Schema::create('placement_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('placement_job_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['applied', 'shortlisted', 'interview', 'offered', 'rejected', 'hired'])->default('applied');
            $table->string('resume_path')->nullable();
            $table->text('cover_letter')->nullable();
            $table->json('resume_data')->nullable();
            $table->timestamp('interview_at')->nullable();
            $table->string('interview_mode', 40)->nullable();
            $table->text('interview_notes')->nullable();
            $table->timestamps();

            $table->unique(['placement_job_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placement_applications');
        Schema::dropIfExists('placement_jobs');
        Schema::dropIfExists('placement_companies');
    }
};
