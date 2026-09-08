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
            $table->string('clock_in_photo')->nullable()->after('clock_in');
            $table->string('clock_out_photo')->nullable()->after('clock_out');
            $table->string('break_in_photo')->nullable()->after('break_in');
            $table->string('break_out_photo')->nullable()->after('break_out');
            $table->decimal('latitude', 10, 7)->nullable()->after('ip_address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('location_address')->nullable()->after('longitude');
            $table->decimal('distance_meters', 8, 2)->nullable()->after('location_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'clock_in_photo',
                'clock_out_photo',
                'break_in_photo',
                'break_out_photo',
                'latitude',
                'longitude',
                'location_address',
                'distance_meters',
            ]);
        });
    }
};
