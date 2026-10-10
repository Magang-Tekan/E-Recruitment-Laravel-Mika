<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed initial default employee passkey from .env or fallback 'MIKA2026'
        $initialPasskey = env('EMPLOYEE_REGISTRATION_PASSKEY', 'MIKA2026');
        DB::table('settings')->insert([
            'key' => 'employee_registration_passkey',
            'value' => $initialPasskey,
            'description' => 'Master Passkey default untuk pendaftaran akun karyawan baru',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
