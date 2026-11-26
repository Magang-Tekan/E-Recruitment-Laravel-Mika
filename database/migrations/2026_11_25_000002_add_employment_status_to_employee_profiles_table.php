<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->string('employment_status', 20)->default('active')->after('employee_type')->index()
                ->comment('active | contract_ended | inactive');
        });

        // Backfill: profil non-permanent yang kontraknya sudah lewat ditandai contract_ended.
        DB::table('employee_profiles')
            ->whereIn('employee_type', ['contract', 'internship', 'probation'])
            ->whereNotNull('contract_end_date')
            ->whereDate('contract_end_date', '<', now()->toDateString())
            ->update(['employment_status' => 'contract_ended']);
    }

    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropIndex(['employment_status']);
            $table->dropColumn('employment_status');
        });
    }
};
