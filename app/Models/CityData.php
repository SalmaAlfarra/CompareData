<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityData extends Model
{
    use HasFactory;
    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'cityData';

    // تعطيل استخدام الـ timestamps لأن الجدول لا يحتوي على أعمدة created_at و updated_at
    public $timestamps = false;
}
