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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->time('office_start_time')->nullable();
            $table->time('office_end_time')->nullable();
            $table->integer('working_hours_per_day')->default(8);
            $table->boolean('allow_manual_time_log')->default(true);
            $table->boolean('require_daily_report')->default(true);
            $table->timestamps();

            $table->unique('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};
