<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;

    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'codes';

    // تحديد الأعمدة القابلة للتعديل (Mass Assignment)
    protected $fillable = [
        'CD_TB_CD', 
        'CD_CD', 
        'CD_ARB_TR', 
        'CD_REGION_CD'
    ];
}
