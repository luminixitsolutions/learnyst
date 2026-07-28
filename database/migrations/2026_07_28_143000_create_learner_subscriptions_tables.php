<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->enum('plan_type', ['course', 'bundle', 'test_series', 'platform'])->default('course');
            $table->string('product_type')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly', 'custom'])->default('monthly');
            $table->unsignedInteger('billing_days')->default(30);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('setup_fee', 12, 2)->default(0);
            $table->string('currency', 10)->default('INR');
            $table->unsignedInteger('trial_days')->default(0);
            $table->boolean('auto_renew')->default(true);
            $table->boolean('is_active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['created_by', 'slug']);
            $table->index(['created_by', 'is_active']);
            $table->index(['product_type', 'product_id']);
        });

        Schema::create('learner_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_plan_id')->constrained('subscription_plans')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();

            if (Schema::hasTable('course_pricing_plans')) {
                $table->foreignId('course_pricing_plan_id')->nullable()->constrained('course_pricing_plans')->nullOnDelete();
            } else {
                $table->unsignedBigInteger('course_pricing_plan_id')->nullable();
            }

            $table->enum('status', ['pending', 'trialing', 'active', 'paused', 'cancelled', 'expired'])->default('pending');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('next_billing_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->decimal('amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['created_by', 'status']);
            $table->index('user_id');
            $table->index('ends_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learner_subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
