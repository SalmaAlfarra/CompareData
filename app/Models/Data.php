<?php

// app/Models/Data.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'data';

    // تحديد الأعمدة القابلة للتعديل (Mass Assignment)
    protected $fillable = [
        'CI_ID_NUM',
        'full_name',
        'phone_number',
        'family_count',
        'wife_id',
        'wife_name',
        'male_members',
        'female_members'
    ];

    // تعطيل استخدام الـ timestamps لأن الجدول لا يحتوي على أعمدة created_at و updated_at
    public $timestamps = false;
}
