<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_locations', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('address')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['company_id', 'name']);
            $table->unique(['company_id', 'code']);
        });

        Schema::create('project_managers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id')->nullable()->after('company_id');
            }

            if (! Schema::hasColumn('users', 'office_location_id')) {
                $table->unsignedBigInteger('office_location_id')->nullable()->after('reporting_manager_id');
            }
        });

        Schema::table('tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('tasks', 'priority')) {
                $table->string('priority')->default('medium')->after('status');
            }

            if (! Schema::hasColumn('tasks', 'due_date')) {
                $table->date('due_date')->nullable()->after('priority');
            }

            if (! Schema::hasColumn('tasks', 'qa_status')) {
                $table->string('qa_status')->nullable()->after('due_date');
            }

            if (! Schema::hasColumn('tasks', 'qa_comment')) {
                $table->text('qa_comment')->nullable()->after('qa_status');
            }
        });

        Schema::table('daily_reports', function (Blueprint $table) {
            $table->unique(['user_id', 'project_id', 'report_date'], 'daily_reports_user_project_date_unique');
        });

        $this->migrateLegacyRoles();

        foreach ([
            'users' => ['department_id', 'role'],
            'projects' => ['department_id'],
            'teams' => ['department_id'],
            'team_members' => ['department_id'],
        ] as $tableName => $columns) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns) {
                foreach ($columns as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        Schema::dropIfExists('departments');
        Schema::dropIfExists('user_roles');
    }

    public function down(): void
    {
        Schema::dropIfExists('project_managers');
        Schema::dropIfExists('office_locations');
    }

    private function migrateLegacyRoles(): void
    {
        if (! Schema::hasTable('user_roles') || ! Schema::hasTable('roles') || ! Schema::hasTable('model_has_roles')) {
            return;
        }

        $roleIdsByName = DB::table('roles')->pluck('id', 'name');
        $legacyMap = \App\Enums\UserRole::oldRoleMap();

        DB::table('user_roles')
            ->orderBy('id')
            ->get(['user_id', 'role'])
            ->each(function ($legacyRole) use ($legacyMap, $roleIdsByName) {
                $roleName = $legacyMap[(int) $legacyRole->role] ?? null;
                $roleId = $roleName ? ($roleIdsByName[$roleName] ?? null) : null;

                if (! $roleId) {
                    return;
                }

                DB::table('model_has_roles')->updateOrInsert([
                    'role_id' => $roleId,
                    'model_type' => \App\Models\User::class,
                    'model_id' => $legacyRole->user_id,
                ]);
            });
    }
};
