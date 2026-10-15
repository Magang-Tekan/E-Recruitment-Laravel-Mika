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
        Schema::table('skills', function (Blueprint $table) {
            $table->string('category', 50)->default('general')->nullable()->after('profile_id');
            $table->string('proficiency', 30)->nullable()->after('name');
            $table->text('description')->nullable()->after('proficiency');
        });

        Schema::table('languages', function (Blueprint $table) {
            $table->string('oral', 20)->nullable()->after('name');
            $table->string('written', 20)->nullable()->after('oral');
            $table->string('conversation', 20)->nullable()->after('written');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn(['category', 'proficiency', 'description']);
        });

        Schema::table('languages', function (Blueprint $table) {
            $table->dropColumn(['oral', 'written', 'conversation']);
        });
    }
};
