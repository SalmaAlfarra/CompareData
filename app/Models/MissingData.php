<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissingData extends Model
{
    use HasFactory;

    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'missingdata';

}
