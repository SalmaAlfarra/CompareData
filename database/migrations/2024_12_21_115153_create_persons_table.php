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
        Schema::create('persons', function (Blueprint $table) {
            $table->integer('CI_ID_NUM')->primary();
            $table->string('CI_FIRST_ARB', 255)->nullable();
            $table->string('CI_FATHER_ARB', 255)->nullable();
            $table->string('CI_GRAND_FATHER_ARB', 255)->nullable();
            $table->string('CI_FAMILY_ARB', 255)->nullable();
            $table->string('CI_BIRTH_TB_CD', 255)->nullable();
            $table->string('CI_BIRTH_CD', 255)->nullable();
            $table->string('CI_BIRTH_DT', 255)->nullable();
            $table->string('CI_SEX_CD', 255)->nullable();
            $table->string('CI_PERSONAL_CD', 255)->nullable();
            $table->string('CI_DEAD_DT', 255)->nullable();
            $table->string('MOTHER_NAME1', 255)->nullable();
            $table->string('CITTTTY', 255)->nullable();
            $table->string('CITY', 255)->nullable();
            $table->string('STREET', 255)->nullable();
            $table->string('REGON', 255)->nullable();
            $table->string('HOUSE_NO', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persons');
    }
};