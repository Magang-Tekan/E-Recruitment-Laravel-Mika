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
        Schema::table('registration_steps', function (Blueprint $table) {
            $table->string('short_title_en', 100)->nullable()->after('short_title');
            $table->string('short_sub_en', 150)->nullable()->after('short_sub');
            $table->string('eyebrow_en', 180)->nullable()->after('eyebrow');
            $table->string('title_en', 200)->nullable()->after('title');
            $table->text('description_en')->nullable()->after('description');
            $table->json('highlights_en')->nullable()->after('highlights');
            $table->string('panel_title_en', 180)->nullable()->after('panel_title');
            $table->string('tips_title_en', 180)->nullable()->after('tips_title');
            $table->text('tips_body_en')->nullable()->after('tips_body');
            $table->text('footnote_en')->nullable()->after('footnote');
            $table->string('cta_label_en', 100)->nullable()->after('cta_label');
            $table->string('cta_label_guest_en', 100)->nullable()->after('cta_label_guest');
            $table->string('cta_label_admin_en', 100)->nullable()->after('cta_label_admin');
            $table->string('next_label_en', 100)->nullable()->after('next_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_steps', function (Blueprint $table) {
            $table->dropColumn([
                'short_title_en',
                'short_sub_en',
                'eyebrow_en',
                'title_en',
                'description_en',
                'highlights_en',
                'panel_title_en',
                'tips_title_en',
                'tips_body_en',
                'footnote_en',
                'cta_label_en',
                'cta_label_guest_en',
                'cta_label_admin_en',
                'next_label_en',
            ]);
        });
    }
};
