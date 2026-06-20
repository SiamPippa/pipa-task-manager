<?php

use App\Enums\UserRole;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->integer('department_id')->nullable();
            $table->integer('designation_id')->nullable();
            $table->integer('reporting_manager_id')->nullable();
            $table->unsignedTinyInteger('role')->default(UserRole::GENERAL);
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
