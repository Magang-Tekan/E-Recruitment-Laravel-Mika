<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicant_job_preferences', function (Blueprint $table) {
            // Minat Lowongan 1
            $table->unsignedBigInteger('pref1_company_id')->nullable()->after('interested_field_1');
            $table->unsignedBigInteger('pref1_department_id')->nullable()->after('pref1_company_id');
            $table->unsignedBigInteger('pref1_position_id')->nullable()->after('pref1_department_id');

            // Minat Lowongan 2
            $table->unsignedBigInteger('pref2_company_id')->nullable()->after('interested_field_2');
            $table->unsignedBigInteger('pref2_department_id')->nullable()->after('pref2_company_id');
            $table->unsignedBigInteger('pref2_position_id')->nullable()->after('pref2_department_id');

            // Minat Lowongan 3
            $table->unsignedBigInteger('pref3_company_id')->nullable()->after('interested_field_3');
            $table->unsignedBigInteger('pref3_department_id')->nullable()->after('pref3_company_id');
            $table->unsignedBigInteger('pref3_position_id')->nullable()->after('pref3_department_id');

            // Foreign keys (soft — no constraint agar tetap fleksibel jika data master dihapus)
            $table->foreign('pref1_company_id')->references('id')->on('companies')->nullOnDelete();
            $table->foreign('pref1_department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('pref1_position_id')->references('id')->on('positions')->nullOnDelete();

            $table->foreign('pref2_company_id')->references('id')->on('companies')->nullOnDelete();
            $table->foreign('pref2_department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('pref2_position_id')->references('id')->on('positions')->nullOnDelete();

            $table->foreign('pref3_company_id')->references('id')->on('companies')->nullOnDelete();
            $table->foreign('pref3_department_id')->references('id')->on('departments')->nullOnDelete();
            $table->foreign('pref3_position_id')->references('id')->on('positions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('applicant_job_preferences', function (Blueprint $table) {
            $table->dropForeign(['pref1_company_id', 'pref1_department_id', 'pref1_position_id']);
            $table->dropForeign(['pref2_company_id', 'pref2_department_id', 'pref2_position_id']);
            $table->dropForeign(['pref3_company_id', 'pref3_department_id', 'pref3_position_id']);

            $table->dropColumn([
                'pref1_company_id', 'pref1_department_id', 'pref1_position_id',
                'pref2_company_id', 'pref2_department_id', 'pref2_position_id',
                'pref3_company_id', 'pref3_department_id', 'pref3_position_id',
            ]);
        });
    }
};
