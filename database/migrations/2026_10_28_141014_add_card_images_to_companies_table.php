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
        Schema::table('companies', function (Blueprint $table) {
            $table->string('card_img1')->nullable()->after('logo');
            $table->string('card_img2')->nullable()->after('card_img1');
            $table->string('card_img3')->nullable()->after('card_img2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['card_img1', 'card_img2', 'card_img3']);
        });
    }
};
