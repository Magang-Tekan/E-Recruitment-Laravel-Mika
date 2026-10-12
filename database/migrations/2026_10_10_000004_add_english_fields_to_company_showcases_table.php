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
        Schema::table('company_showcases', function (Blueprint $table) {
            $table->string('tag_en')->nullable()->after('tag');
            $table->string('title_en')->nullable()->after('title');
            $table->text('description_en')->nullable()->after('description');
            $table->string('badge_title_en')->nullable()->after('badge_title');
            $table->string('badge_sub_en')->nullable()->after('badge_sub');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_showcases', function (Blueprint $table) {
            $table->dropColumn(['tag_en', 'title_en', 'description_en', 'badge_title_en', 'badge_sub_en']);
        });
    }
};
