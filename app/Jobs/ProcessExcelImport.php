<?php

namespace App\Jobs;

// ini_set('memory_limit', '512M');
// ini_set('max_execution_time', '300');

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
        Log::info("All data from 'temps' and 'data' tables has been deleted.");

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
                // Match the row with the person based on CI_ID_NUM
                $person = $persons->firstWhere('CI_ID_NUM', $row->CI_ID_NUM);
                if (!$person) {
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
                        'Notes'                               => $row->Notes,
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

                    // Add the successfully processed record to the collection
                    $processedRecords->push([
                        'CI_ID_NUM'                          => $row->CI_ID_NUM,
                        'CI_FIRST_ARB'                       => $person->CI_FIRST_ARB,
                        'CI_FATHER_ARB'                      => $person->CI_FATHER_ARB,
                        'CI_GRAND_FATHER_ARB'                => $person->CI_GRAND_FATHER_ARB,
                        'CI_FAMILY_ARB'                      => $person->CI_FAMILY_ARB,
                        'CITTTTY'                               => $person->CITY,
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
            DB::table('missingData')->insert($missingDataRecords->toArray());
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
        // Export processed data to an Excel file
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
