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
        Schema::create('daily_work_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('report_date');
            $table->unsignedInteger('calls')->default(0);
            $table->unsignedInteger('instagram_dms')->default(0);
            $table->unsignedInteger('facebook_dms')->default(0);
            $table->unsignedInteger('linkedin_dms')->default(0);
            $table->unsignedInteger('whatsapp_dms')->default(0);
            $table->unsignedInteger('linkedin_posts')->default(0);
            $table->unsignedInteger('instagram_posts')->default(0);
            $table->unsignedInteger('facebook_group_actions')->default(0);
            $table->unsignedInteger('facebook_group_dms')->default(0);
            $table->unsignedInteger('linkedin_connections')->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'report_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_work_reports');
    }
};
