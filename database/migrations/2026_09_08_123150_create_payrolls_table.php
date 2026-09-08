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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedTinyInteger('month'); // 1 to 12
            $table->unsignedSmallInteger('year'); // e.g. 2026
            $table->string('payroll_code')->unique(); // PAY-202609-EMP0001

            // Attendance metrics
            $table->unsignedTinyInteger('total_month_days')->default(30);
            $table->unsignedTinyInteger('working_days')->default(26);
            $table->decimal('present_days', 4, 1)->default(0.0);
            $table->decimal('paid_leaves', 4, 1)->default(0.0);
            $table->unsignedTinyInteger('holidays_weekoffs')->default(0);
            $table->decimal('absent_days', 4, 1)->default(0.0);
            $table->decimal('payable_days', 4, 1)->default(0.0);

            // Earnings
            $table->decimal('gross_salary', 10, 2)->default(0.00);
            $table->decimal('earned_basic', 10, 2)->default(0.00);
            $table->decimal('earned_hra', 10, 2)->default(0.00);
            $table->decimal('earned_allowances', 10, 2)->default(0.00);
            $table->decimal('overtime_bonus', 10, 2)->default(0.00);
            $table->decimal('total_earnings', 10, 2)->default(0.00);

            // Deductions
            $table->decimal('pf_amount', 10, 2)->default(0.00);
            $table->decimal('esic_amount', 10, 2)->default(0.00);
            $table->decimal('prof_tax', 10, 2)->default(0.00);
            $table->decimal('tds_amount', 10, 2)->default(0.00);
            $table->decimal('unpaid_leave_deduction', 10, 2)->default(0.00);
            $table->decimal('other_deductions', 10, 2)->default(0.00);
            $table->decimal('total_deductions', 10, 2)->default(0.00);

            // Net & Payout
            $table->decimal('net_salary', 10, 2)->default(0.00);
            $table->string('payment_status')->default('Pending'); // Pending, Processing, Paid
            $table->string('payment_mode')->nullable(); // Bank Transfer, NEFT/RTGS, UPI, Cheque, Cash
            $table->string('transaction_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('remarks')->nullable();

            $table->timestamps();

            $table->unique(['employee_id', 'month', 'year']);
            $table->index(['month', 'year', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
