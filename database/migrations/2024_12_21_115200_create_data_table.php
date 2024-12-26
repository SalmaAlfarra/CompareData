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
            $table->string('full_name', 255)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->integer('family_count')->nullable();
            $table->integer('wife_id')->nullable();
            $table->string('wife_name', 255)->nullable();
            $table->integer('male_members')->nullable();
            $table->integer('female_members')->nullable();
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