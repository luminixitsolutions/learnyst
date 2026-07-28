<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('finance_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->enum('type', ['cash', 'bank', 'other'])->default('cash');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc')->nullable();
            $table->decimal('opening_balance', 14, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('finance_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('finance_account_id')->nullable()->constrained('finance_accounts')->nullOnDelete();
            $table->enum('entry_type', ['income', 'expense'])->index();
            $table->string('category', 80)->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 14, 2);
            $table->date('entry_date')->index();
            $table->string('payment_mode', 40)->nullable();
            $table->string('reference')->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('gst_invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->string('receipt_number')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['created_by', 'entry_type', 'entry_date']);
        });

        Schema::create('finance_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('finance_ledger_entry_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('receipt_number');
            $table->date('receipt_date');
            $table->string('payer_name')->nullable();
            $table->decimal('amount', 14, 2);
            $table->string('payment_mode', 40)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['created_by', 'receipt_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_receipts');
        Schema::dropIfExists('finance_ledger_entries');
        Schema::dropIfExists('finance_accounts');
    }
};
