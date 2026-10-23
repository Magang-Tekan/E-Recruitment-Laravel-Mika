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
        Schema::table('applicant_profile', function (Blueprint $table) {
            $table->json('health_answers')->nullable()->after('cv_file_path');
            $table->json('other_answers')->nullable()->after('health_answers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicant_profile', function (Blueprint $table) {
            $table->dropColumn(['health_answers', 'other_answers']);
        });
    }
};
