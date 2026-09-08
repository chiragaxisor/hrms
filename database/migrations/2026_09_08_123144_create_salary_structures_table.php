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
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->unique()->constrained('employees')->cascadeOnDelete();
            $table->decimal('ctc_annual', 12, 2)->default(0.00);
            $table->decimal('monthly_gross', 10, 2)->default(0.00);
            $table->decimal('basic_salary', 10, 2)->default(0.00);
            $table->decimal('hra', 10, 2)->default(0.00);
            $table->decimal('special_allowance', 10, 2)->default(0.00);
            $table->decimal('conveyance_allowance', 10, 2)->default(0.00);
            $table->decimal('medical_allowance', 10, 2)->default(0.00);
            $table->decimal('pf_deduction', 10, 2)->default(0.00);
            $table->decimal('esic_deduction', 10, 2)->default(0.00);
            $table->decimal('professional_tax', 10, 2)->default(200.00);
            $table->decimal('tds', 10, 2)->default(0.00);
            $table->date('effective_from')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_structures');
    }
};
