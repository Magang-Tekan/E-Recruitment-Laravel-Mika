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
        Schema::create('registration_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('short_title', 100);
            $table->string('short_sub', 150)->nullable();
            $table->string('eyebrow', 180)->nullable();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->json('highlights')->nullable();
            $table->string('panel_title', 180)->nullable();
            $table->string('tips_title', 180)->nullable();
            $table->text('tips_body')->nullable();
            $table->text('footnote')->nullable();
            $table->string('icon', 30)->default('shield');
            $table->string('cta_action', 30)->default('register');
            $table->string('cta_action_guest', 30)->nullable();
            $table->string('cta_label', 100)->nullable();
            $table->string('cta_label_guest', 100)->nullable();
            $table->string('cta_label_admin', 100)->nullable();
            $table->string('cta_url', 255)->nullable();
            $table->string('next_label', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registration_steps');
    }
};
