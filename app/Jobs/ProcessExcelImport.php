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

        // Import the temporary data from the Excel file
        Excel::import(new TempsImport($this->uuid), storage_path('app/' . $this->filePath));

        // Process the Temp data in chunks
        Temp::where('xlxs_uuid', $this->uuid)->chunk($batchSize, function ($rows) use (&$processedRecords) {
            $persons = Person::with(['relatives' => function ($query) {
                $query->where('CF_RELATIVE_CD', 4); // Filter for wives
            }])->whereIn('CI_ID_NUM', $rows->pluck('CI_ID_NUM')->toArray())->get();

            /** @var Temp $row */
            foreach ($persons as $person) {
                try {
                    $row = $rows->firstWhere('CI_ID_NUM', $person->CI_ID_NUM);

                    $processedRecords->push([
                        'CI_ID_NUM' => $person->CI_ID_NUM,
                        'CI_FIRST_ARB' => $person->CI_FIRST_ARB,
                        'CI_FATHER_ARB' => $person->CI_FATHER_ARB,
                        'CI_GRAND_FATHER_ARB' => $person->CI_GRAND_FATHER_ARB,
                        'CI_FAMILY_ARB' => $person->CI_FAMILY_ARB,
                        'phone_number' => $row->phone_number,
                        'family_count' => $row->family_count,
                        'wife_id' => $person->relatives->isNotEmpty() ? $person->relatives->first()->CI_ID_NUM : null,
                        'wife_name' => $person->relatives->isNotEmpty() ? $person->relatives->first()->full_name : null,
                        'male_members' => null,
                        'female_members' => null,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Error processing person: " . $e->getMessage());
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
                'phone_number',
                'family_count',
                'wife_id',
                'wife_name',
                'male_members',
                'female_members'
            ]);
            Log::info("Processed " . count($processedRecords) . " records.");
        }


        // Export directly to Excel
        $fileName = 'processed_data_' . time() . '.xlsx';
        Excel::store(new class($processedRecords) implements FromCollection, WithHeadings {
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
