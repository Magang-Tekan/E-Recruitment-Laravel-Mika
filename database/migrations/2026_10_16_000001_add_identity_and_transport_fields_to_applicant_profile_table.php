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
        // Pastikan kolom marital_status bertipe VARCHAR agar fleksibel menampung opsi baru (lajang, menikah, janda, duda)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE applicant_profile ALTER COLUMN marital_status TYPE VARCHAR(50) USING marital_status::text;");
        }

        Schema::table('applicant_profile', function (Blueprint $table) {
            if (DB::getDriverName() !== 'pgsql') {
                $table->string('marital_status', 50)->nullable()->change();
            }

            // Alamat KTP
            $table->text('ktp_address')->nullable()->after('address');

            // Suku & Kebangsaan
            $table->string('ethnicity', 50)->nullable()->after('religion');
            $table->string('nationality', 50)->nullable()->default('WNI')->after('ethnicity');

            // Tanggal Pernikahan
            $table->date('marriage_date')->nullable()->after('marital_status');

            // SIM (JSON fleksibel: default 2 baris, siap jika lebih dari 2)
            $table->json('driving_licenses')->nullable()->after('marriage_date');

            // Transportasi harian & Jarak ke tempat kerja (km)
            $table->string('daily_transportation', 100)->nullable()->after('driving_licenses');
            $table->decimal('work_distance_km', 6, 1)->nullable()->after('daily_transportation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicant_profile', function (Blueprint $table) {
            $table->dropColumn([
                'ktp_address',
                'ethnicity',
                'nationality',
                'marriage_date',
                'driving_licenses',
                'daily_transportation',
                'work_distance_km',
            ]);
        });
    }
};
