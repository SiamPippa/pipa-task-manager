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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('project_id');
            $table->integer('task_id');
            $table->date('report_date');
            $table->text('summary')->nullable();
            $table->text('blocker')->nullable();
            $table->text('tomorrow_plan')->nullable();
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
