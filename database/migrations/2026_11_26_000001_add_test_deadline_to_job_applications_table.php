<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->timestamp('test_deadline_at')->nullable()->after('notes')
                ->comment('Batas akhir pengerjaan tes online sejak status Lolos Berkas (Reviewed)');
            $table->unsignedTinyInteger('test_deadline_extended_count')->default(0)->after('test_deadline_at')
                ->comment('Jumlah perpanjangan batas tes oleh Admin');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn(['test_deadline_at', 'test_deadline_extended_count']);
        });
    }
};
