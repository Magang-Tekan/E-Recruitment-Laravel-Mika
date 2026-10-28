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
        Schema::create('career_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('num', 10)->default('01');
            $table->string('badge');
            $table->string('badge_en')->nullable();
            $table->string('prefix');
            $table->string('prefix_en')->nullable();
            $table->string('accent');
            $table->string('accent_en')->nullable();
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->string('image')->nullable();
            $table->string('back_badge')->nullable();
            $table->string('back_badge_en')->nullable();
            $table->string('back_title');
            $table->string('back_title_en')->nullable();
            $table->json('highlights')->nullable();
            $table->json('highlights_en')->nullable();
            $table->string('cta_text');
            $table->string('cta_text_en')->nullable();
            $table->string('experience_level')->nullable();
            $table->string('custom_url')->nullable();
            $table->boolean('is_dark')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('career_tracks');
    }
};
