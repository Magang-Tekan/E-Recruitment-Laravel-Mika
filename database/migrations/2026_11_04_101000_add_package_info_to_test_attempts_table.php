<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->unsignedInteger('package_id')->nullable()->after('test_id');
            $table->unsignedInteger('duration_minutes')->nullable()->after('package_id');
            $table->json('question_ids')->nullable()->after('duration_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('test_attempts', function (Blueprint $table) {
            $table->dropColumn(['package_id', 'duration_minutes', 'question_ids']);
        });
    }
};

