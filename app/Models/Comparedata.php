<?php

// app/Models/Comparedata.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comparedata extends Model
{
    use HasFactory;

    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'comparedata';

    // تحديد الأعمدة القابلة للتعديل (Mass Assignment)
    protected $fillable = [
        'CI_ID_NUM',
        'fullname',
        'phone_number',
        'family_count',
        'wife_id',
        'wife_name',
        'male_count',
        'female_count'
    ];

    // تعطيل استخدام الـ timestamps لأن الجدول لا يحتوي على أعمدة created_at و updated_at
    public $timestamps = false;
}