<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('test_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('test_categories')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('question_banks', function (Blueprint $table) {
            $table->foreignId('sub_category_id')->nullable()->after('category_id')->constrained('test_sub_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('question_banks', function (Blueprint $table) {
            $table->dropForeign(['sub_category_id']);
            $table->dropColumn('sub_category_id');
        });

        Schema::dropIfExists('test_sub_categories');
    }
};
