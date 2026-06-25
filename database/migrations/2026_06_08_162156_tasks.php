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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id');
            $table->unsignedBigInteger('project_module_id');
            $table->string('jira_task_no')->nullable();
            $table->string('title');
            $table->string('branch_name', 60);
            $table->string('type');
            $table->text('description')->nullable();
            $table->decimal('estimate_hours', 8, 2)->nullable();
            $table->string('status')->default('todo');
            $table->timestamps();

            $table->unique(['project_id', 'branch_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
