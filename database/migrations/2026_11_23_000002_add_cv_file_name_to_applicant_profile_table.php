<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicant_profile', function (Blueprint $table) {
            if (! Schema::hasColumn('applicant_profile', 'cv_file_name')) {
                $table->string('cv_file_name')->nullable()->after('cv_file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applicant_profile', function (Blueprint $table) {
            if (Schema::hasColumn('applicant_profile', 'cv_file_name')) {
                $table->dropColumn('cv_file_name');
            }
        });
    }
};
