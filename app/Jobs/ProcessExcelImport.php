<?php

namespace App\Jobs;

use App\Models\Data;
use App\Models\Person;
use App\Models\Relation;
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

    // تمرير مسار الملف إلى الوظيفة
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    // المعالجة الفعلية للملف
    public function handle()
    {
        $batchSize = 1000; // حجم الدفعة (يمكن تعديله حسب الحاجة)

        // قراءة البيانات من ملف الإكسل
        $data = Excel::toArray([], storage_path('app/' . $this->filePath));

        // تقسيم المعالجة إلى دفعات من 1000 صف
        foreach (array_chunk($data[0], $batchSize) as $chunkIndex => $chunk) {
            foreach ($chunk as $rowIndex => $row) {
                try {
                    // استخلاص البيانات من الأعمدة
                    $CI_ID_NUM = trim($row[0]);
                    $full_name = $row[1];
                    $phone_number = $row[2];
                    $total_members = $row[3];
                    $male_members = $row[4];
                    $female_members = $row[5];
                    $wife_id = $row[6] ?? null;
                    $wife_name = $row[7] ?? null;

                    if (empty($CI_ID_NUM)) {
                        Log::warning("Skipping Row #" . ($rowIndex + 1) . " due to missing CI_ID_NUM");
                        continue;
                    }

                    // البحث عن الشخص في جدول الأشخاص
                    $person = Person::where('CI_ID_NUM', $CI_ID_NUM)->first();

                    if (!$person) {
                        Log::warning("Person not found for CI_ID_NUM: $CI_ID_NUM");
                        continue;
                    }

                    // تحديث الاسم الرباعي
                    $full_name = $person->CI_FIRST_ARB . ' ' . $person->CI_FATHER_ARB . ' ' . $person->CI_GRAND_FATHER_ARB . ' ' . $person->CI_FAMILY_ARB;

                    // إذا كان الشخص متزوجًا
                    if ($person->CI_PERSONAL_CD === 'متزوج') {
                        $relation = Relation::where('CF_ID_NUM', $CI_ID_NUM)->where('CF_RELATIVE_CD', 4)->first();

                        if ($relation) {
                            $wife_id = $relation->CF_ID_RELATIVE;
                            $wife = Person::where('CI_ID_NUM', $wife_id)->first();
                            if ($wife) {
                                $wife_name = $wife->CI_FIRST_ARB . ' ' . $wife->CI_FATHER_ARB . ' ' . $wife->CI_GRAND_FATHER_ARB . ' ' . $wife->CI_FAMILY_ARB;
                            }
                        }
                    }

                    // تحديث أو إنشاء سجل جديد في جدول الداتا
                    Data::updateOrCreate(
                        ['CI_ID_NUM' => $CI_ID_NUM],
                        [
                            'full_name' => $full_name,
                            'phone_number' => $phone_number,
                            'family_count' => $total_members,
                            'male_members' => $male_members,
                            'female_members' => $female_members,
                            'wife_id' => $wife_id,
                            'wife_name' => $wife_name,
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error("Error processing Chunk #" . ($chunkIndex + 1) . ", Row #" . ($rowIndex + 1) . ": " . $e->getMessage());
                }
            }

            // إضافة تأخير بين الدفعات لتخفيف الضغط على الخادم
            sleep(1);
        }

        // تحميل ملف Excel بعد الانتهاء من المعالجة
        $fileName = 'processed_data_' . time() . '.xlsx';
        Excel::store(new DataExport, $fileName, 'public');

        // إرسال رسالة بريدية أو تنبيه للمستخدم
        Log::info("Import process completed. Data processed successfully.");

        // حذف البيانات من جدول الداتا
        Data::truncate();  // هذا سيحذف جميع البيانات في جدول الداتا
    }
}