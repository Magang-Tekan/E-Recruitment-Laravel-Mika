<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applicant_profile', function (Blueprint $table) {
            $table->boolean('domicile_same_as_ktp')->default(false)->after('ktp_province');
        });

        // Data lama yang alamat & kotanya persis sama dengan KTP berasal dari centang "sama dengan KTP".
        DB::table('applicant_profile')
            ->whereNotNull('address')
            ->whereNotNull('ktp_address')
            ->whereColumn('address', 'ktp_address')
            ->whereColumn('city', 'ktp_city')
            ->update(['domicile_same_as_ktp' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicant_profile', function (Blueprint $table) {
            $table->dropColumn('domicile_same_as_ktp');
        });
    }
};
