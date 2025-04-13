<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbsensiSettingsTable extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->time('check_in_start')->default('07:00:00');
            $table->time('check_in_end')->default('09:00:00');
            $table->time('check_out_time')->default('17:00:00');
            $table->integer('radius_meters')->default(100);
            $table->time('late_tolerance')->default('08:00:00');
            $table->string('office_address')->nullable();
            $table->decimal('office_latitude', 10, 7)->nullable();
            $table->decimal('office_longitude', 10, 7)->nullable();
            $table->timestamps();
        }); 
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_settings');
    }
}
