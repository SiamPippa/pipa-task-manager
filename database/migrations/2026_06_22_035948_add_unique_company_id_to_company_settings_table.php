<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateCompanyIds = DB::table('company_settings')
            ->select('company_id')
            ->groupBy('company_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('company_id');

        foreach ($duplicateCompanyIds as $companyId) {
            $keepId = DB::table('company_settings')
                ->where('company_id', $companyId)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->value('id');

            DB::table('company_settings')
                ->where('company_id', $companyId)
                ->where('id', '!=', $keepId)
                ->delete();
        }

        Schema::table('company_settings', function (Blueprint $table) {
            $table->unique('company_id');
        });
    }

    public function down(): void
    {
        Schema::table('company_settings', function (Blueprint $table) {
            $table->dropUnique(['company_id']);
        });
    }
};
