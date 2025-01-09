<?php

namespace App\Jobs;

use App\Imports\TempsImport;
use App\Models\Data;
use App\Models\Person;
use App\Models\Temp;
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
        set_time_limit(300); // 5 minutes
        $batchSize = 1000; // Batch size for processing
        $processedRecords = collect(); // Collection to store processed data

        // حذف جميع البيانات من جدول temps و data
        Temp::truncate();
        Data::truncate();
        Log::info("All data from 'temps' and 'data' tables has been deleted.");

        // Import the temporary data from the Excel file
        Excel::import(new TempsImport($this->uuid), storage_path('app/' . $this->filePath));

        // Process the Temp data in chunks
        Temp::where('xlxs_uuid', $this->uuid)->chunk($batchSize, function ($rows) use (&$processedRecords) {
            // Get CI_ID_NUMs from Temp rows
            $ciIdNums = $rows->pluck('CI_ID_NUM')->toArray();

            // Fetch persons matching the CI_ID_NUMs from Temp
            $persons = Person::with(['relatives' => function ($query) {
                $query->where('CF_RELATIVE_CD', 4); // Filter for wives
            }])->whereIn('CI_ID_NUM', $ciIdNums)->get();

            /** @var Temp $row */
            foreach ($rows as $row) {
                // Skip if CI_ID_NUM is not found in Person table
                $person = $persons->firstWhere('CI_ID_NUM', $row->CI_ID_NUM);
                if (!$person) {
                    continue;
                }

                try {
                    $wifeId = null;
                    $wifeName = null;

                    if ($person->relatives->isNotEmpty()) {
                        // إذا تم العثور على الزوجة في جدول relatives
                        $wifeId = $person->relatives->first()->CI_ID_NUM;
                        $wifeName = $person->relatives->first()->full_name;
                    } elseif (!empty($row->Wife_id)) {
                        // إذا كان هناك قيمة في عمود Wife_id، ابحث عنها في جدول Person
                        $wifePerson = Person::where('CI_ID_NUM', $row->Wife_id)->first();
                        if ($wifePerson) {
                            $wifeId = $row->Wife_id;
                            $wifeName = $wifePerson->CI_FIRST_ARB . ' ' . $wifePerson->CI_FATHER_ARB . ' ' . $wifePerson->CI_FAMILY_ARB;
                        } else {
                            // إذا لم يتم العثور عليها في جدول Person، خذ البيانات من الملف مباشرة
                            $wifeId = $row->Wife_id;
                            $wifeName = $row->Wife_name;
                        }
                    } else {
                        // إذا لم يتم العثور على الزوجة في أي مكان
                        $wifeId = $row->Wife_id;
                        $wifeName = $row->Wife_name;
                    }

                    $processedRecords->push([
                        'CI_ID_NUM' => $row->CI_ID_NUM,
                        'CI_FIRST_ARB' => $person->CI_FIRST_ARB,
                        'CI_FATHER_ARB' => $person->CI_FATHER_ARB,
                        'CI_GRAND_FATHER_ARB' => $person->CI_GRAND_FATHER_ARB,
                        'CI_FAMILY_ARB' => $person->CI_FAMILY_ARB,
                        'Phone_number' => $row->Phone_number,
                        'Family_count' => $row->Family_count,
                        'Wife_id' => $wifeId,
                        'Wife_name' => $wifeName,
                        'Male_members' => $row->Male_members,
                        'Female_members' => $row->Female_members,
                        'Individuals_less_than_3_years' => $row->Individuals_less_than_3_years,
                        'Individuals_with_chronic_diseases' => $row->Individuals_with_chronic_diseases,
                        'Individuals_with_disabilities' => $row->Individuals_with_disabilities,
                        'Breadwinner' => $row->Breadwinner,
                        'Housing_condition' => $row->Housing_condition,
                        'Notes' => $row->Notes,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Error processing row: " . $e->getMessage());
                }
            }
        });

        if ($processedRecords->isNotEmpty()) {
            Data::upsert($processedRecords->toArray(), ['CI_ID_NUM'], [
                'CI_ID_NUM',
                'CI_FIRST_ARB',
                'CI_FATHER_ARB',
                'CI_GRAND_FATHER_ARB',
                'CI_FAMILY_ARB',
                'Phone_number',
                'Family_count',
                'Wife_id',
                'Wife_name',
                'Male_members',
                'Female_members',
                'Individuals_less_than_3_years',
                'Individuals_with_chronic_diseases',
                'Individuals_with_disabilities',
                'Breadwinner',
                'Housing_condition',
                'Notes',
            ]);
            Log::info("Processed " . count($processedRecords) . " records.");
        }

        // Export directly to Excel
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
    }
}