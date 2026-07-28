<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('balance', 12, 2)->default(0);
            $table->string('currency', 10)->default('INR');
            $table->boolean('is_frozen')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'created_by']);
        });

        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['credit', 'debit']);
            $table->string('source', 40)->default('manual');
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2)->default(0);
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');
            $table->nullableMorphs('reference');
            $table->string('referral_code', 50)->nullable();
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['created_by', 'type', 'status']);
            $table->index(['source', 'created_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('wallet_amount', 12, 2)->default(0)->after('discount');
        });

        // Expand payment_method enum to include wallet (MySQL).
        try {
            DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('razorpay', 'manual', 'free', 'wallet') NOT NULL DEFAULT 'manual'");
        } catch (\Throwable) {
            // SQLite / non-MySQL: column may already be a string in some envs.
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('wallet_amount');
        });

        try {
            DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('razorpay', 'manual', 'free') NOT NULL DEFAULT 'manual'");
        } catch (\Throwable) {
            // ignore
        }

        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
    }
};
