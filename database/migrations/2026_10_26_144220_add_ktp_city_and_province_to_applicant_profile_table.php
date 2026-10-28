<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applicant_profile', function (Blueprint $table) {
            $table->string('ktp_city', 100)->nullable()->after('ktp_address');
            $table->string('ktp_province', 100)->nullable()->after('ktp_city');
        });

        // Copy existing city & province into ktp_city & ktp_province for existing profiles
        DB::table('applicant_profile')
            ->whereNotNull('city')
            ->whereNull('ktp_city')
            ->update([
                'ktp_city' => DB::raw('city'),
                'ktp_province' => DB::raw('province'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicant_profile', function (Blueprint $table) {
            $table->dropColumn(['ktp_city', 'ktp_province']);
        });
    }
};
