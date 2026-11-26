<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->date('contract_start_date')->nullable()->after('employee_type')
                ->comment('Tanggal mulai bekerja');
            $table->date('contract_end_date')->nullable()->after('contract_start_date')
                ->comment('Tanggal berakhir kontrak. NULL = tidak ada batas (permanent)');
        });
    }

    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn(['contract_start_date', 'contract_end_date']);
        });
    }
};
