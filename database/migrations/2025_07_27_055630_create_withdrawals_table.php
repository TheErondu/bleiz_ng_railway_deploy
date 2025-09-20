<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained('users')->onDelete('cascade');

            // Withdrawal details
            $table->decimal('amount', 15, 2); // Amount to withdraw
            $table->enum('status', ['pending', 'approved', 'rejected', 'processing', 'completed'])->default('pending');
            $table->text('note')->nullable(); // Reason for withdrawal or admin notes

            // Processing information
            $table->string('withdrawal_method')->default('bank_transfer'); // bank_transfer, cash, check
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('reference_number')->nullable(); // Bank transaction reference

            // Approval workflow
            $table->foreignId('requested_by')->nullable()->constrained('users'); // Usually same as investor_id
            $table->foreignId('approved_by')->nullable()->constrained('users'); // Admin who approved
            $table->foreignId('processed_by')->nullable()->constrained('users'); // Admin who processed payment

            // Important timestamps
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            // Financial tracking
            $table->decimal('processing_fee', 15, 2)->default(0); // Any withdrawal fees
            $table->decimal('net_amount', 15, 2)->nullable(); // Amount after fees (amount - processing_fee)

            // Additional metadata
            $table->json('metadata')->nullable(); // Store additional data like payment gateway response
            $table->text('rejection_reason')->nullable(); // If rejected, why?
            $table->text('admin_notes')->nullable(); // Internal admin notes

            $table->timestamps();

            // Indexes for better performance
            $table->index('status');
            $table->index('investor_id');
            $table->index('approved_at');
            $table->index(['investor_id', 'status']);
            $table->index('reference_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
