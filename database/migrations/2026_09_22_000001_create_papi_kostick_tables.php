<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // 1. PAPI Aspects (7 Dimensi Utama)
        Schema::create('papi_aspects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('english_name')->nullable();
            $table->tinyInteger('order_number')->default(1);
            $table->timestamps();
        });

        // 2. PAPI Factors (20 Aspek Peran / Role & Kebutuhan / Need)
        Schema::create('papi_factors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aspect_id')->constrained('papi_aspects')->cascadeOnDelete();
            $table->string('code', 2)->unique();
            $table->string('name');
            $table->string('english_name')->nullable();
            $table->enum('type', ['role', 'need'])->comment('role = Total Atas (45), need = Total Bawah (45)');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 3. PAPI Norms / Interpretasi Skor
        Schema::create('papi_norms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factor_id')->constrained('papi_factors')->cascadeOnDelete();
            $table->string('factor_code', 2)->index();
            $table->tinyInteger('min_score');
            $table->tinyInteger('max_score');
            $table->text('interpretation');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 4. PAPI Test Results (Hasil Pengerjaan Tes Kandidat / Karyawan)
        Schema::create('papi_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_attempt_id')->constrained('test_attempts')->cascadeOnDelete();
            $table->json('scores')->comment('Nilai 20 aspek PAPI (N, G, A, L, P, I, T, V, X, S, B, O, R, D, C, Z, E, K, F, W)');
            $table->tinyInteger('role_score')->default(0)->comment('Total Atas (G, L, I, T, V, S, R, D, C, E) - Normal = 45');
            $table->tinyInteger('need_score')->default(0)->comment('Total Bawah (N, A, P, X, B, O, Z, K, F, W) - Normal = 45');
            $table->boolean('is_valid')->default(false)->comment('Valid jika role_score == 45 dan need_score == 45');
            $table->json('interpretations')->nullable()->comment('Interpretasi per aspek');
            $table->json('raw_answers')->nullable()->comment('Jawaban pilihan a/b tiap nomor (1-90)');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('papi_test_results');
        Schema::dropIfExists('papi_norms');
        Schema::dropIfExists('papi_factors');
        Schema::dropIfExists('papi_aspects');
    }
};
