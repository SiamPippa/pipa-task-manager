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
        Schema::create('time_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->unique()->constrained()->cascadeOnDelete();
            $table->integer('project_id');
            $table->integer('task_id');
            $table->integer('user_id');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->integer('total_minutes')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_logs');
    }
};
