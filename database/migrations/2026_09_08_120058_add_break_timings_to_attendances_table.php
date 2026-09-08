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
        Schema::table('attendances', function (Blueprint $table) {
            $table->time('break_in')->nullable()->after('clock_out');
            $table->time('break_out')->nullable()->after('break_in');
            $table->unsignedSmallInteger('break_minutes')->default(0)->after('break_out');
            $table->json('breaks_history')->nullable()->after('break_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['break_in', 'break_out', 'break_minutes', 'breaks_history']);
        });
    }
};
