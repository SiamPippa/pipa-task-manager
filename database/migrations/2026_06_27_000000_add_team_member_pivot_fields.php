<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->boolean('is_team_lead')->default(false)->after('company_id');
            $table->boolean('status')->default(true)->after('is_team_lead');
        });

        DB::table('teams')
            ->select(['id', 'team_lead_id'])
            ->orderBy('id')
            ->get()
            ->each(function ($team) {
                DB::table('team_members')
                    ->where('team_id', $team->id)
                    ->where('user_id', $team->team_lead_id)
                    ->update(['is_team_lead' => true]);
            });
    }

    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            $table->dropColumn(['is_team_lead', 'status']);
        });
    }
};
