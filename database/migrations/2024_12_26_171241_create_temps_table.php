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
            $table->id();
            $table->string('no')->nullable();
            // رقم الهوية
            $table->string('national_id')
                ->index()
                ->unique();
            // الاسم رباعي
            $table->string('full_name');
            // رقم الجوال
            $table->string('phone_number');
            // الجوال البديل
            $table->string('alternative_phone_number')->nullable();
            // عدد الافراد
            $table->integer('family_count');
            // اسم التجمع
            $table->string('gathering_name');

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
