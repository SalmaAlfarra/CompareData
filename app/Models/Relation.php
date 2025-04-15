<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    use HasFactory;

    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'relations';

    // تحديد الأعمدة القابلة للتعديل (Mass Assignment)
    protected $fillable = [
        'CF_ID_NUM', 
        'CF_RELATIVE_CD', 
        'CF_ID_RELATIVE'
    ];
}
