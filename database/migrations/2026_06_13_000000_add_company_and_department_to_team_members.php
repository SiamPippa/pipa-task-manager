<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasCompanyId = Schema::hasColumn('team_members', 'company_id');
        $hasDepartmentId = Schema::hasColumn('team_members', 'department_id');

        if ($hasCompanyId && $hasDepartmentId) {
            return;
        }

        if (! $hasCompanyId) {
            Schema::table('team_members', function (Blueprint $table) {
                $table->integer('company_id')->after('user_id');
            });
        }

        if (! $hasDepartmentId) {
            Schema::table('team_members', function (Blueprint $table) {
                $table->integer('department_id')->nullable()->after('company_id');
            });
        }

        DB::table('team_members')
            ->join('users', 'users.id', '=', 'team_members.user_id')
            ->update([
                'team_members.company_id' => DB::raw('users.company_id'),
                'team_members.department_id' => DB::raw('users.department_id'),
            ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('team_members', 'department_id')) {
            Schema::table('team_members', function (Blueprint $table) {
                $table->dropColumn('department_id');
            });
        }

        if (Schema::hasColumn('team_members', 'company_id')) {
            Schema::table('team_members', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }
    }
};
