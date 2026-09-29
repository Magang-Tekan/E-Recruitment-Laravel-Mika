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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_recruiter')->default(false)->after('role_id');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->foreignId('reviewer_id')->nullable()->after('position_id')->constrained('users')->nullOnDelete();
        });

        Schema::table('job_applications', function (Blueprint $table) {
            // Recruiter verification
            $table->string('recruiter_approval')->default('pending')->after('status');
            $table->text('recruiter_notes')->nullable()->after('recruiter_approval');
            $table->timestamp('recruiter_approved_at')->nullable()->after('recruiter_notes');
            $table->foreignId('recruiter_id')->nullable()->after('recruiter_approved_at')->constrained('users')->nullOnDelete();

            // Admin verification
            $table->string('admin_approval')->default('pending')->after('recruiter_id');
            $table->text('admin_notes')->nullable()->after('admin_approval');
            $table->timestamp('admin_approved_at')->nullable()->after('admin_notes');
            $table->foreignId('admin_id')->nullable()->after('admin_approved_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropForeign(['recruiter_id']);
            $table->dropForeign(['admin_id']);
            $table->dropColumn([
                'recruiter_approval',
                'recruiter_notes',
                'recruiter_approved_at',
                'recruiter_id',
                'admin_approval',
                'admin_notes',
                'admin_approved_at',
                'admin_id',
            ]);
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropForeign(['reviewer_id']);
            $table->dropColumn('reviewer_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_recruiter');
        });
    }
};
