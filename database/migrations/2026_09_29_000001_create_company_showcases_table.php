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
        Schema::create('company_showcases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('tag')->nullable(); // e.g. "Acara & Kolaborasi"
            $table->string('title'); // e.g. "Bimbingan Teknis ASPADIN 2026: Sinergi Kompetensi"
            $table->text('description')->nullable();
            $table->string('img1'); // Foto utama / poster
            $table->string('img2')->nullable(); // Foto aktivitas / interaksi
            $table->string('img3')->nullable(); // Foto pameran / panggung
            $table->string('badge_title')->nullable()->default('EVENT');
            $table->string('badge_sub')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_showcases');
    }
};
