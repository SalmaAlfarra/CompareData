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
        Schema::create('comparedata', function (Blueprint $table) {
            $table->string('CI_ID_NUM');
            $table->string('fullname');
            $table->string('phone_number');
            $table->integer('family_count');
            $table->integer('male_count');
            $table->integer('female_count');
            $table->string('wife_id');
            $table->string('wife_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comparedata');
    }
};