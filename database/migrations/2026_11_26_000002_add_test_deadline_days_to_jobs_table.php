<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->unsignedTinyInteger('test_deadline_days')->nullable()->after('deadline')
                ->comment('Batas waktu pengerjaan tes online per lowongan (hari kerja). NULL = ikut pengaturan global.');
        });

        if (Schema::hasTable('settings') && ! DB::table('settings')->where('key', 'default_test_deadline_days')->exists()) {
            DB::table('settings')->insert([
                'key'         => 'default_test_deadline_days',
                'value'       => '1',
                'description' => 'Batas waktu default pengerjaan tes online pelamar (hari kerja Senin-Jumat)',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('test_deadline_days');
        });
    }
};
