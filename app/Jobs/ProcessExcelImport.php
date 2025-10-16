<?php

namespace App\Jobs;

use App\Imports\TempsImport;
use App\Models\CityData;
use App\Models\Data;
use App\Models\MissingData;
use App\Models\Person;
use App\Models\Temp;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProcessExcelImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath; // Path of the uploaded file
    protected $uuid; // UUID associated with the file

    // Constructor to initialize file path and UUID
    public function __construct($filePath, $uuid)
    {
        $this->filePath  = $filePath;
        $this->uuid      = $uuid;
    }

    /**
     * التحقق من صحة رقم الجوال
     * يجب أن يبدأ بـ 059 أو 056 ويتكون من 10 أرقام
     */
    private function validatePhoneNumber($phoneNumber)
    {
        // إزالة أي مسافات أو رموز غير مرغوب فيها
        $cleanedNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // التحقق من أن الرقم يتكون من 10 أرقام ويبدأ بـ 059 أو 056
        return (strlen($cleanedNumber) == 9 && (strpos($cleanedNumber, '59') === 0 || strpos($cleanedNumber, '56') === 0));
    }

    /**
     * دالة لفلترة الأسماء والاحتفاظ بالحروف العربية فقط
     */
    private function filterName($name)
    {
        // إزالة أي شيء ليس حرفًا عربيًا (بما في ذلك الآ، ة) أو مسافة بيضاء
        // هذا يضمن بقاء الأسماء العربية النظيفة فقط
        $filteredName = preg_replace('/[^أ-يآة\s]/u', '', $name);

        // إزالة المسافات الزائدة في البداية والنهاية
        return trim($filteredName);
    }

    // Handle the job logic
    public function handle()
    {
        set_time_limit(300); // Increase the time limit to handle large files
        $batchSize           = 1000; // Batch size for processing data in chunks
        $processedRecords    = collect(); // Collection to store successfully processed records
        $missingDataRecords  = collect(); // Collection to store missing records

        // Truncate tables before inserting new data
        Temp::truncate();
        Data::truncate();
        CityData::truncate();
        MissingData::truncate();
        Log::info("All data from 'temps', 'data', 'city_data' and 'missingData' tables has been deleted.");

        // Import data from the provided Excel file using the TempsImport class
        Excel::import(new TempsImport($this->uuid), storage_path('app/' . $this->filePath));

        // Process the records in batches from the Temp table
        Temp::where('xlxs_uuid', $this->uuid)->chunk($batchSize, function ($rows) use (&$processedRecords, &$missingDataRecords) {
            // Extract CI_ID_NUMs from the rows for matching in the Person table
            $ciIdNums = $rows->pluck('CI_ID_NUM')->toArray();
            // Retrieve persons associated with the CI_ID_NUMs from the database
            $persons  = Person::with(['relatives' => function ($query) {
                $query->where('CF_RELATIVE_CD', 4); // Filter relatives where CF_RELATIVE_CD equals 4 (Wife)
            }])->whereIn('CI_ID_NUM', $ciIdNums)->get();

            // Iterate through each row in the batch
            foreach ($rows as $row) {
                // التحقق من صحة رقم الجوال
                $isValidPhone = $this->validatePhoneNumber($row->Phone_number);

                // Match the row with the person based on CI_ID_NUM
                $person = $persons->firstWhere('CI_ID_NUM', $row->CI_ID_NUM);

                // إذا كان رقم الجوال غير صالح أو لم يتم العثور على الشخص، أضفه إلى البيانات المفقودة
                if (!$person || !$isValidPhone) {
                    // تحديد سبب الإضافة إلى البيانات المفقودة
                    $reason = !$person ? "لم يتم العثور على الشخص" : "رقم الجوال غير صالح";

                    // دمج سبب الرفض مع الملاحظات الأصلية
                    $originalNotes = $row->Notes ?? '';
                    $rejectionNote = "سبب الرفض: " . $reason;
                    $finalNotes = trim($originalNotes);
                    if (!empty($finalNotes)) {
                        $finalNotes .= ' | ' . $rejectionNote;
                    } else {
                        $finalNotes = $rejectionNote;
                    }

                    // If no matching person is found, push the row to missing data
                    $missingDataRecords->push([
                        'CI_ID_NUM'                           => $row->CI_ID_NUM,
                        'Full_name'                           => $row->Full_name,
                        'Phone_number'                        => $row->Phone_number,
                        'Family_count'                        => $row->Family_count,
                        'Representative_name'                 => $row->Representative_name,
                        'Wife_id'                             => $row->Wife_id,
                        'Wife_name'                           => $row->Wife_name,
                        'Male_members'                        => $row->Male_members,
                        'Female_members'                      => $row->Female_members,
                        'Individuals_less_than_3_years'       => $row->Individuals_less_than_3_years,
                        'Individuals_with_chronic_diseases'   => $row->Individuals_with_chronic_diseases,
                        'Individuals_with_disabilities'       => $row->Individuals_with_disabilities,
                        'Breadwinner'                         => $row->Breadwinner,
                        'Housing_condition'                   => $row->Housing_condition,
                        'Notes'                               => $finalNotes, // استخدام الملاحظات المدمجة
                        'xlxs_uuid'                           => $row->xlxs_uuid,
                    ]);
                    continue; // Skip to next row
                }

                try {
                    // Initialize wife data
                    $wifeId = null;
                    $wifeName = null;

                    // Check if the person has associated relatives (Wife)
                    if ($person->relatives->isNotEmpty()) {
                        $wifeId = $person->relatives->first()->CI_ID_NUM; // Get the wife's CI_ID_NUM
                        $wifeName = $person->relatives->first()->full_name; // Get the wife's full name
                    } elseif (!empty($row->Wife_id)) {
                        // If no relatives found, but Wife_id is provided in the row, check the person with that ID
                        $wifePerson = Person::where('CI_ID_NUM', $row->Wife_id)->first();
                        if ($wifePerson) {
                            $wifeId = $row->Wife_id; // Set the Wife_id
                            $wifeName = $wifePerson->CI_FIRST_ARB . ' ' . $wifePerson->CI_FATHER_ARB . ' ' . $wifePerson->CI_FAMILY_ARB;
                        } else {
                            $wifeId = $row->Wife_id; // Set the Wife_id directly if no wife record found
                            $wifeName = $row->Wife_name; // Use the provided Wife_name in the row
                        }
                    } else {
                        // If no wife ID is found, use the data from the row itself
                        $wifeId = $row->Wife_id;
                        $wifeName = $row->Wife_name;
                    }

                    // فلترة الاسم قبل إضافته
                    $filteredFirstName = $this->filterName($person->CI_FIRST_ARB);
                    $filteredFatherName = $this->filterName($person->CI_FATHER_ARB);
                    $filteredGrandFatherName = $this->filterName($person->CI_GRAND_FATHER_ARB);
                    $filteredFamilyName = $this->filterName($person->CI_FAMILY_ARB);

                    // Add the successfully processed record to the collection
                    $processedRecords->push([
                        'CI_ID_NUM'                          => $row->CI_ID_NUM,
                        'CI_FIRST_ARB'                       => $filteredFirstName, // استخدام الاسم بعد الفلترة
                        'CI_FATHER_ARB'                      => $filteredFatherName, // استخدام الاسم بعد الفلترة
                        'CI_GRAND_FATHER_ARB'                => $filteredGrandFatherName, // استخدام الاسم بعد الفلترة
                        'CI_FAMILY_ARB'                      => $filteredFamilyName, // استخدام الاسم بعد الفلترة
                        'CITTTTY'                            => $person->CITY,
                        'Phone_number'                       => $row->Phone_number,
                        'Family_count'                       => $row->Family_count,
                        'Representative_name'                => $row->Representative_name,
                        'Wife_id'                            => $wifeId, // Use the updated Wife_id
                        'Wife_name'                          => $wifeName, // Use the updated Wife_name
                        'Status'                             => $row->Status,
                        'Reason_for_suspension'              => $row->Reason_for_suspension,
                        'Male_members'                       => $row->Male_members,
                        'Female_members'                     => $row->Female_members,
                        'Individuals_less_than_3_years'      => $row->Individuals_less_than_3_years,
                        'Individuals_with_chronic_diseases'  => $row->Individuals_with_chronic_diseases,
                        'Individuals_with_disabilities'      => $row->Individuals_with_disabilities,
                        'Breadwinner'                        => $row->Breadwinner,
                        'Housing_condition'                  => $row->Housing_condition,
                        'Notes'                              => $row->Notes,
                    ]);
                } catch (\Exception $e) {
                    // Log errors if there are any issues processing a row
                    Log::error("Error processing row: " . $e->getMessage());
                }
            }
        });

        // Insert the missing records into the 'missingData' table if any
        if ($missingDataRecords->isNotEmpty()) {
            DB::table('missingdata')->insert($missingDataRecords->toArray());
            Log::info("Inserted " . count($missingDataRecords) . " records into 'missingData' table.");
        }

        // Insert processed records into the 'data' table using upsert
        if ($processedRecords->isNotEmpty()) {
            Data::upsert($processedRecords->toArray(), ['CI_ID_NUM'], [
                'CI_ID_NUM',
                'CI_FIRST_ARB',
                'CI_FATHER_ARB',
                'CI_GRAND_FATHER_ARB',
                'CI_FAMILY_ARB',
                'CITTTTY',
                'Phone_number',
                'Family_count',
                'Representative_name',
                'Wife_id',
                'Wife_name',
                'Status',
                'Reason_for_suspension',
                'Male_members',
                'Female_members',
                'Individuals_less_than_3_years',
                'Individuals_with_chronic_diseases',
                'Individuals_with_disabilities',
                'Breadwinner',
                'Housing_condition',
                'Notes'
            ]);

            Log::info("Processed " . count($processedRecords) . " records.");
        }

        // After storing the records, we check and update the wife's status in the Data table
        $processedRecords->each(function ($record) {
            $wifeId = $record['Wife_id'];

            if (!is_null($wifeId)) {
                // Check if the wife is registered in the Data table
                $wife = Data::where('CI_ID_NUM', $wifeId)->first();

                if ($wife) {
                    // If the wife is registered, update the husband's status
                    Data::where('CI_ID_NUM', $record['CI_ID_NUM'])
                        ->update([
                            'Status' => 'غير فعال', // Set status as inactive
                            'Reason_for_suspension' => 'تم العثور عليه عند المندوب: ' . $record['Representative_name'] // Update reason with the representative's name
                        ]);
                } else {
                    // If the wife is not registered, update the husband's status to "Active - Not Repeated"
                    Data::where('CI_ID_NUM', $record['CI_ID_NUM'])
                        ->update([
                            'Status' => 'فعال', // Set status as active
                            'Reason_for_suspension' => 'غير مكرر' // Update reason as "Not Repeated"
                        ]);
                }
            }
        });

        // Export processed data to an Excel file (only if there are records to export)
        if ($processedRecords->isNotEmpty()) {
            $fileName = 'processed_data_' . time() . '.xlsx';
            Excel::store(new class($processedRecords) implements FromCollection, WithHeadings
            {
                protected $records;

                public function __construct($records)
                {
                    $this->records = $records;
                }

                public function collection()
                {
                    return $this->records;
                }

                public function headings(): array
                {
                    return array_keys($this->records->first());
                }
            }, $fileName, 'public');

            Log::info("Import process completed. Data exported to: " . $fileName);
        } else {
            Log::info("Import process completed. No valid records to export.");
        }

        // إرسال البيانات إلى السيرفر الثاني
        $this->sendDataToServer2();
    }

    /**
     * دالة جديدة لإرسال البيانات المعالجة إلى السيرفر الثاني.
     */
    private function sendDataToServer2()
    {
        try {
            Log::info('بدء إرسال البيانات إلى السيرفر الثاني...');
            $server2Url = config('app.server2_url');
            $apiKey = config('app.server2_api_key');

            // أضف هذا السجل للتأكد
            Log::info('مفتاح API الذي سيتم إرساله من السيرفر الأول: ' . $apiKey);

            if (empty($server2Url) || empty($apiKey)) {
                Log::error('رابط السيرفر الثاني أو مفتاح API غير مُعد في ملف .env.');
                return;
            }

            // تحويل البيانات إلى JSON للتأكد من صحتها
            $dataToSend = [
                'beneficiary_data' => Data::all()->toArray(),
            ];
            $jsonData = json_encode($dataToSend);

            // تسجيل حجم البيانات المرسلة
            Log::info('حجم البيانات المرسلة: ' . strlen($jsonData) . ' بايت');
            Log::info('عدد السجلات المرسلة: ' . count($dataToSend['beneficiary_data']));

            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($server2Url, $dataToSend);

            // التحقق من استجابة السيرفر الثاني
            if ($response->successful()) {
                Log::info('تم إرسال البيانات إلى السيرفر الثاني بنجاح. الرد: ' . $response->body());
            } else {
                Log::error('فشل إرسال البيانات إلى السيرفر الثاني. الحالة: ' . $response->status() . ' الرد: ' . $response->body());

                // تسجيل معلومات إضافية عن الطلب
                Log::error('عنوان الطلب: ' . $server2Url);
                Log::error('البيانات المرسلة: ' . substr($jsonData, 0, 500) . '...');
            }
        } catch (\Exception $e) {
            Log::error('حدث استثناء أثناء إرسال البيانات إلى السيرفر الثاني: ' . $e->getMessage());
            Log::error('تفاصيل الاستثناء: ' . $e->getTraceAsString());
        }
    }
}
