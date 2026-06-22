<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateGroups = DB::table('projects')
            ->select('company_id', 'department_id', DB::raw('LOWER(name) as normalized_name'))
            ->groupBy('company_id', 'department_id', DB::raw('LOWER(name)'))
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicateGroups as $group) {
            $keepId = DB::table('projects')
                ->where('company_id', $group->company_id)
                ->where('department_id', $group->department_id)
                ->whereRaw('LOWER(name) = ?', [$group->normalized_name])
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->value('id');

            DB::table('projects')
                ->where('company_id', $group->company_id)
                ->where('department_id', $group->department_id)
                ->whereRaw('LOWER(name) = ?', [$group->normalized_name])
                ->where('id', '!=', $keepId)
                ->delete();
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->unique(['company_id', 'department_id', 'name'], 'projects_company_department_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique('projects_company_department_name_unique');
        });
    }
};
