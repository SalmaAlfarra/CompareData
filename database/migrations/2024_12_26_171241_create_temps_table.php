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
            // رقم الهوية
            $table->string('national_id')->index()->unique();
            // الاسم رباعي
            $table->string('full_name');
            // رقم الجوال
            $table->string('phone_number');
            // عدد الافراد
            $table->integer('family_count');
            //رقم هوية الزوجة
            $table->string('wife_id')->nullable();
            // اسم الزوجة رباعي
            $table->string('wife_name', 255)->nullable();
            //عدد الأفراد الذكور
            $table->integer('male_members')->nullable();
            // عدد الأفراد الإناث
            $table->integer('female_members')->nullable();

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