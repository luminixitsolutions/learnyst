<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->enum('item_type', ['ebook', 'journal', 'previous_paper', 'research', 'resource'])->default('ebook');
            $table->text('description')->nullable();
            $table->string('author')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->boolean('allow_download')->default(false);
            $table->enum('access_mode', ['public', 'enrolled', 'subscription', 'private'])->default('enrolled');
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ebook_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('resource_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('download_count')->default(0);
            $table->timestamps();

            $table->index(['created_by', 'item_type', 'status']);
        });

        Schema::table('resources', function (Blueprint $table) {
            if (! Schema::hasColumn('resources', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('resources', 'allow_download')) {
                $table->boolean('allow_download')->default(true)->after('status');
            }
        });

        Schema::table('ebooks', function (Blueprint $table) {
            if (! Schema::hasColumn('ebooks', 'file_path')) {
                $table->string('file_path')->nullable()->after('title');
            }
            if (! Schema::hasColumn('ebooks', 'cover_path')) {
                $table->string('cover_path')->nullable()->after('file_path');
            }
            if (! Schema::hasColumn('ebooks', 'description')) {
                $table->text('description')->nullable()->after('cover_path');
            }
            if (! Schema::hasColumn('ebooks', 'allow_download')) {
                $table->boolean('allow_download')->default(false)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_items');
    }
};
