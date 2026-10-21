<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applicant_profile', function (Blueprint $table) {
            $table->string('religion', 50)->nullable()->after('province');
            $table->string('postal_code', 10)->nullable()->after('religion');
            $table->string('blood_type', 10)->nullable()->after('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicant_profile', function (Blueprint $table) {
            $table->dropColumn(['religion', 'postal_code', 'blood_type']);
        });
    }
};
