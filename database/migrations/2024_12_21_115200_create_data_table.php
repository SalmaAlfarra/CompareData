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
        Schema::create('data', function (Blueprint $table) {
            $table->integer('CI_ID_NUM')->primary();
            $table->string('CI_FIRST_ARB', 255)->nullable();
            $table->string('CI_FATHER_ARB', 255)->nullable();
            $table->string('CI_GRAND_FATHER_ARB', 255)->nullable();
            $table->string('CI_FAMILY_ARB', 255)->nullable();
            $table->string('CITTTTY', 255)->nullable(); // المحافظة الأصلية
            $table->string('Phone_number', 20)->nullable();
            $table->integer('Family_count')->nullable();
            $table->string('Representative_name', 255)->nullable();
            $table->integer('Wife_id')->nullable();
            $table->string('Wife_name', 255)->nullable();
            $table->string('Status', 50)->nullable();
            $table->string('Reason_for_suspension', 255)->nullable();
            $table->integer('Male_members')->nullable();
            $table->integer('Female_members')->nullable();
            $table->integer('Individuals_less_than_3_years')->nullable();
            $table->integer('Individuals_with_chronic_diseases')->nullable();
            $table->integer('Individuals_with_disabilities')->nullable();
            $table->string('Breadwinner', 255)->nullable();
            $table->string('Housing_condition', 255)->nullable();
            $table->string('Notes', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data');
    }
};