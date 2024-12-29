<?php

namespace App\Jobs;

use App\Imports\TempsImport;
use App\Models\Person;
use App\Models\Temp;
use App\Models\Data; // التأكد من استيراد نموذج بيانات قاعدة البيانات
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProcessExcelImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $uuid;

    public function __construct($filePath, $uuid)
    {
        $this->filePath = $filePath;
        $this->uuid = $uuid;
    }

    public function handle()
    {
        ini_set('max_execution_time', 2700); // زيادة الحد الزمني للتنفيذ
        $batchSize = 50; // تقليل حجم الدفعة لتحسين الأداء
        $processedRecords = collect(); // تخزين البيانات المعالجة

        // استيراد البيانات من Excel
        Excel::import(new TempsImport($this->uuid), storage_path('app/' . $this->filePath));

        // تسجيل عدد السجلات قبل البدء
        $rowsCount = Temp::where('xlxs_uuid', $this->uuid)->count();
        Log::info("Total rows to process: " . $rowsCount);

        // معالجة البيانات على دفعات
        Temp::where('xlxs_uuid', $this->uuid)->orderBy('id')->chunkById($batchSize, function ($rows) use (&$processedRecords) {
            Log::info("Processing batch with " . count($rows) . " rows.");

            $persons = Person::with(['relatives' => function ($query) {
                $query->where('CF_RELATIVE_CD', 4); // تحديد الزوجات
            }])->whereIn('CI_ID_NUM', $rows->pluck('national_id')->toArray())->get();

            foreach ($persons as $person) {
                try {
                    $row = $rows->firstWhere('national_id', $person->CI_ID_NUM);

                    // إضافة السجلات إلى المجموعة المعالجة
                    $processedRecords->push([
                        'CI_ID_NUM' => $person->CI_ID_NUM,
                        'full_name' => $person->full_name,
                        'phone_number' => $row->phone_number ?? null,
                        'family_count' => $row->family_count ?? null,
                        'male_members' => null,
                        'female_members' => null,
                        'wife_id' => $person->relatives->isNotEmpty() ? $person->relatives->first()->CI_ID_NUM : null,
                        'wife_name' => $person->relatives->isNotEmpty() ? $person->relatives->first()->full_name : null,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Error processing person: " . $e->getMessage());
                }
            }

            // تخزين البيانات في قاعدة البيانات بعد كل دفعة
            if ($processedRecords->isNotEmpty()) {
                Data::upsert($processedRecords->toArray(), ['CI_ID_NUM'], [
                    'CI_ID_NUM', 'full_name', 'phone_number', 'family_count', 'male_members', 'female_members', 'wife_id', 'wife_name'
                ]);
                Log::info("Processed " . count($processedRecords) . " records.");
            }

            // تفريغ البيانات المخزنة مؤقتًا بعد كل دفعة لتقليل استهلاك الذاكرة
            $processedRecords = collect();
        });

        // تسجيل رسالة عند اكتمال المعالجة
        Log::info("Import process completed.");
    }
}
