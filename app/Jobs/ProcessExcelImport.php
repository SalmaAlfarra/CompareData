<?php

namespace App\Jobs;

use App\Imports\TempsImport;
use App\Models\Data;
use App\Models\Person;
use App\Models\Relation;
use App\Models\Temp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DataExport;  // تأكد من أنك قد أنشأت هذا الـExport

class ProcessExcelImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $uuid;

    // تمرير مسار الملف إلى الوظيفة
    public function __construct($filePath, $uuid)
    {
        $this->filePath = $filePath;
        $this->uuid = $uuid;
    }

    // المعالجة الفعلية للملف
    public function handle()
    {
        $batchSize = 500; // حجم الدفعة (يمكن تعديله حسب الحاجة)

        // قراءة البيانات من ملف الإكسل
        Excel::import(new TempsImport($this->uuid), storage_path('app/' . $this->filePath));

        $records = collect();

        // تقسيم المعالجة إلى دفعات من 500 صف
        Temp::where('xlxs_uuid', $this->uuid)->chunk($batchSize, function ($rows) use ($records) {
            // جلب الأشخاص مع الزوجات
            $persons = Person::with(['relatives' => function ($query) {
                $query->where('CF_RELATIVE_CD', 4);
            }])->whereIn('CI_ID_NUM', $rows->pluck('national_id')->toArray())->get();

            // dd($rows->pluck('national_id')->toArray());

            /** @var Temp $row */
            foreach ($persons as $person) {
                try {
                    $row = $rows->firstWhere('national_id', $person->CI_ID_NUM);
                    // استخلاص البيانات من الأعمدة
                    $CI_ID_NUM = $person->CI_ID_NUM;
                    $full_name = $person->full_name;
                    $phone_number = $row->phone_number;
                    $family_count = $row->family_count;
                    $male_members = null;
                    $female_members = null;
                    $wife_name =null;
                    $wife_id =null;

                    // إذا كان الشخص متزوجًا
                    if ($person->relatives->isNotEmpty()) {
                        $wife = $person->relatives->first();
                        if ($wife) {
                            $wife_id = $wife->CI_ID_NUM;
                            $wife_name = $wife->full_name;
                        }
                    }

                    $records->push([
                        'CI_ID_NUM' => $CI_ID_NUM,
                        'full_name' => $full_name,
                        'phone_number' => $phone_number,
                        'family_count' => $family_count,
                        'male_members' => $male_members,
                        'female_members' => $female_members,
                        'wife_id' => $wife_id ?? null,
                        'wife_name' => $wife_name,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Error processing, ". $e->getMessage());
                }
            }
        });

        Data::upsert($records->toArray(), ['CI_ID_NUM'],
            ["CI_ID_NUM","full_name","phone_number","family_count","male_members","female_members","wife_id","wife_name"]
        );

        // تحميل ملف Excel بعد الانتهاء من المعالجة
        $fileName = 'processed_data_' . time() . '.xlsx';
        Excel::store(new DataExport, $fileName, 'public');

        // إرسال رسالة بريدية أو تنبيه للمستخدم
        Log::info("Import process completed. Data processed successfully.");

        // حذف البيانات من جدول الداتا
//        Data::truncate();  // هذا سيحذف جميع البيانات في جدول الداتا
    }


}