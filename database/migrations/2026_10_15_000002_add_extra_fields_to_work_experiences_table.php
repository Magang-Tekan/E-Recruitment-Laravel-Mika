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
        Schema::table('work_experiences', function (Blueprint $table) {
            $table->string('business_field')->nullable()->after('company_name');
            $table->text('company_address')->nullable()->after('business_field');
            $table->string('supervisor_name')->nullable()->after('company_address');
            $table->string('initial_position')->nullable()->after('position');
            $table->decimal('initial_salary', 15, 2)->nullable()->after('currently_working');
            $table->decimal('final_salary', 15, 2)->nullable()->after('initial_salary');
            $table->text('benefits')->nullable()->after('final_salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_experiences', function (Blueprint $table) {
            $table->dropColumn([
                'business_field',
                'company_address',
                'supervisor_name',
                'initial_position',
                'initial_salary',
                'final_salary',
                'benefits',
            ]);
        });
    }
};
