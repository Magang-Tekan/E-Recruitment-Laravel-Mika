<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            // Tahap 2: Recruiter Test Approval (Koreksi Uraian / Nilai Teknis >= KKM)
            $table->string('test_recruiter_approval')->default('pending')->after('interview_admin_id');
            $table->text('test_recruiter_notes')->nullable()->after('test_recruiter_approval');
            $table->timestamp('test_recruiter_approved_at')->nullable()->after('test_recruiter_notes');
            $table->foreignId('test_recruiter_id')->nullable()->after('test_recruiter_approved_at')->constrained('users')->nullOnDelete();

            // Tahap 2: HR / Admin Test Approval (Verifikasi Teknis + Analisis Psikotes DISC & PAPI)
            $table->string('test_admin_approval')->default('pending')->after('test_recruiter_id');
            $table->text('test_admin_notes')->nullable()->after('test_admin_approval');
            $table->timestamp('test_admin_approved_at')->nullable()->after('test_admin_notes');
            $table->foreignId('test_admin_id')->nullable()->after('test_admin_approved_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropForeign(['test_recruiter_id']);
            $table->dropForeign(['test_admin_id']);
            $table->dropColumn([
                'test_recruiter_approval',
                'test_recruiter_notes',
                'test_recruiter_approved_at',
                'test_recruiter_id',
                'test_admin_approval',
                'test_admin_notes',
                'test_admin_approved_at',
                'test_admin_id',
            ]);
        });
    }
};
