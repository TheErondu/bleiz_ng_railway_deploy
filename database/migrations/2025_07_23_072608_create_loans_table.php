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
            $table->decimal('principal', 15, 2);
            $table->decimal('interest_rate', 15, 2);
            $table->integer('tenure_months');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('repayment_cycle', ['weekly', 'monthly']);
            $table->enum('status', ['ongoing', 'completed', 'defaulted'])->default('ongoing');
            $table->timestamps();
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
