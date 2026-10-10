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
            // Recruiter / User Interview Verification (Kompetensi & Kecocokan Tim)
            $table->string('interview_recruiter_approval')->default('pending')->after('admin_id');
            $table->text('interview_recruiter_notes')->nullable()->after('interview_recruiter_approval');
            $table->timestamp('interview_recruiter_approved_at')->nullable()->after('interview_recruiter_notes');
            $table->foreignId('interview_recruiter_id')->nullable()->after('interview_recruiter_approved_at')->constrained('users')->nullOnDelete();

            // HR / Admin Interview Verification (Gaji, Psikotes, Kuota, Kontrak)
            $table->string('interview_admin_approval')->default('pending')->after('interview_recruiter_id');
            $table->text('interview_admin_notes')->nullable()->after('interview_admin_approval');
            $table->timestamp('interview_admin_approved_at')->nullable()->after('interview_admin_notes');
            $table->foreignId('interview_admin_id')->nullable()->after('interview_admin_approved_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropForeign(['interview_recruiter_id']);
            $table->dropForeign(['interview_admin_id']);
            $table->dropColumn([
                'interview_recruiter_approval',
                'interview_recruiter_notes',
                'interview_recruiter_approved_at',
                'interview_recruiter_id',
                'interview_admin_approval',
                'interview_admin_notes',
                'interview_admin_approved_at',
                'interview_admin_id',
            ]);
        });
    }
};
