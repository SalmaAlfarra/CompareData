<?php

// app/Models/Persons.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    // تحديد اسم الجدول في قاعدة البيانات
    protected $table = 'persons';

    // تحديد الأعمدة القابلة للتعديل (Mass Assignment)
    protected $fillable = [
        'CI_ID_NUM',
        'CI_FIRST_ARB',
        'CI_FATHER_ARB',
        'CI_GRAND_FATHER_ARB',
        'CI_FAMILY_ARB',
        'CI_BIRTH_TB_CD',
        'CI_BIRTH_CD',
        'CI_BIRTH_DT',
        'CI_SEX_CD',
        'CI_PERSONAL_CD',
        'CI_DEAD_DT',
        'MOTHER_NAME1',
        'CITTTTY',
        'CITY',
        'STREET',
        'REGON',
        'HOUSE_NO',
    ];

    // تعطيل استخدام الـ timestamps لأن الجدول لا يحتوي على أعمدة created_at و updated_at
    public $timestamps = false;

    public function relatives()
    {
        return $this->hasManyThrough(Person::class, Relation::class, 'CF_ID_NUM', 'CI_ID_NUM', 'CI_ID_NUM', 'CF_ID_RELATIVE');
    }

    public function getFullNameAttribute(): string
    {
        return $this->CI_FIRST_ARB . ' ' . $this->CI_FATHER_ARB . ' ' . $this->CI_GRAND_FATHER_ARB . ' ' . $this->CI_FAMILY_ARB;
    }
}