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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');

            // Loan basic details
            $table->decimal('principal', 15, 2); // Original loan amount
            $table->decimal('interest_rate', 5, 2); // Annual interest rate (e.g., 20.00%)
            $table->integer('tenure_months'); // Loan duration in months
            $table->date('start_date'); // When loan starts
            $table->date('end_date'); // When loan should be completed
            $table->enum('repayment_cycle', ['weekly', 'monthly', 'quarterly'])->default('monthly');

            // Loan status and financial tracking
            $table->enum('status', ['pending', 'ongoing', 'completed', 'defaulted'])->default('pending');
            $table->decimal('loan_balance', 15, 2)->default(0); // Remaining amount to be paid
            $table->decimal('repaid_principal', 15, 2)->default(0); // How much principal has been repaid
            $table->decimal('overdue_payment', 15, 2)->default(0); // Amount that is overdue
            $table->decimal('total_obligation', 15, 2)->default(0); // Total amount customer owes (principal + interest)
            $table->decimal('current_interest', 15, 2)->default(0); // Total interest for this loan

            // Additional tracking fields (optional but useful)
            $table->decimal('total_fees', 15, 2)->default(0); // Any additional fees
            $table->decimal('penalty_amount', 15, 2)->default(0); // Late payment penalties
            $table->integer('grace_period_days')->default(5); // Grace period for late payments
            $table->text('notes')->nullable(); // Any additional notes about the loan
            $table->json('loan_terms')->nullable(); // Store additional loan terms as JSON

            // Approval tracking
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();

            $table->timestamps();

            // Indexes for better query performance
            $table->index('status');
            $table->index('start_date');
            $table->index('end_date');
            $table->index(['customer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
