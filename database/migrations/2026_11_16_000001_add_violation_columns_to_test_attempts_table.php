<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom pengawasan sesi ujian (proctoring) dipakai oleh ApplicantOnlineTest
 * dan EmployeeOnlineTest saat mencatat pelanggaran serta mengumpulkan jawaban
 * otomatis, tetapi belum pernah dibuatkan migrasinya. Tanpa kolom ini setiap
 * penyelesaian ujian gagal dengan "Unknown column 'violations_count'" (HTTP 500).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            if (!Schema::hasColumn('test_attempts', 'violations_count')) {
                $table->unsignedInteger('violations_count')->default(0);
            }

            if (!Schema::hasColumn('test_attempts', 'violation_logs')) {
                $table->json('violation_logs')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            if (Schema::hasColumn('test_attempts', 'violation_logs')) {
                $table->dropColumn('violation_logs');
            }

            if (Schema::hasColumn('test_attempts', 'violations_count')) {
                $table->dropColumn('violations_count');
            }
        });
    }
};
