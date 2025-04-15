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
        Schema::create('temps', function (Blueprint $table) {
            $table->id();;
            $table->string('CI_ID_NUM')->index()->unique();
            $table->string('Full_name');
            $table->string('Phone_number')->nullable();
            $table->integer('Family_count')->nullable();
            $table->string('Representative_name', 255)->nullable();
            $table->string('Wife_id')->nullable();
            $table->string('Wife_name', 255)->nullable();
            $table->integer('Male_members')->nullable();
            $table->integer('Female_members')->nullable();
            $table->integer('Individuals_less_than_3_years')->nullable();
            $table->integer('Individuals_with_chronic_diseases')->nullable();
            $table->integer('Individuals_with_disabilities')->nullable();
            $table->string('Breadwinner', 255)->nullable();
            $table->string('Housing_condition', 255)->nullable();
            $table->string('Notes', 255)->nullable();
            $table->string('xlxs_uuid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temps');
    }
};
