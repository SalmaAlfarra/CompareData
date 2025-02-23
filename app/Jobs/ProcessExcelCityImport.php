<?php

namespace App\Jobs;

use App\Imports\TempsImport;
use App\Models\CityData;
use App\Models\Data;
use App\Models\MissingData;
use App\Models\Temp;
use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProcessExcelCityImport implements ShouldQueue
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
        set_time_limit(300);
        $batchSize = 1000;
        $processedRecords = collect();
        $missingDataRecords = collect();

        // Clear all data from Temp, CityData, and MissingData tables.
        Temp::truncate();
        Data::truncate();
        CityData::truncate();
        MissingData::truncate();
        Log::info("Cleared all data from Temp, CityData, and MissingData tables.");

        // Import the Excel file into the Temp table
        Excel::import(new TempsImport($this->uuid), storage_path('app/' . $this->filePath));

        // Process each chunk of data from Temp table
        Temp::where('xlxs_uuid', $this->uuid)->chunk($batchSize, function ($rows) use (&$processedRecords, &$missingDataRecords) {
            $ciIdNums = $rows->pluck('CI_ID_NUM')->toArray();
            $persons = Person::whereIn('CI_ID_NUM', $ciIdNums)->get();

            foreach ($rows as $row) {
                $person = $persons->firstWhere('CI_ID_NUM', $row->CI_ID_NUM);

                if (!$person) {
                    // dd($person);
                    // If the person is not found, add to MissingData
                    $missingDataRecords->push([
                        'CI_ID_NUM' => $row->CI_ID_NUM,
                        'Full_name' => $row->Full_name,
                        'Phone_number' => $row->Phone_number,
                        'Family_count' => $row->Family_count,
                        'Representative_name' => $row->Representative_name,
                        'Wife_id' => $row->Wife_id,
                        'Wife_name' => $row->Wife_name,
                        'Male_members' => $row->Male_members,
                        'Female_members' => $row->Female_members,
                        'Individuals_less_than_3_years' => $row->Individuals_less_than_3_years,
                        'Individuals_with_chronic_diseases' => $row->Individuals_with_chronic_diseases,
                        'Individuals_with_disabilities' => $row->Individuals_with_disabilities,
                        'Breadwinner' => $row->Breadwinner,
                        'Housing_condition' => $row->Housing_condition,
                        'Notes' => $row->Notes,
                        'xlxs_uuid' => $row->xlxs_uuid,
                    ]);
                    continue;
                }

                // Check if the data exists in CityData, if not add it to the CityData table
                $existingCityData = Person::where('CI_ID_NUM', $row->CI_ID_NUM)->first();
                // dd($existingCityData);
                if (!$existingCityData) {
                    $processedRecords->push([
                        'CI_ID_NUM' => $row->CI_ID_NUM,
                        'CI_FIRST_ARB' => $person->CI_FIRST_ARB,
                        'CI_FATHER_ARB' => $person->CI_FATHER_ARB,
                        'CI_GRAND_FATHER_ARB' => $person->CI_GRAND_FATHER_ARB,
                        'CI_FAMILY_ARB' => $person->CI_FAMILY_ARB,
                        'Phone_number' => $row->Phone_number,
                        'Family_count' => $row->Family_count,
                        'CITTTTY' => $person->CITTTTY, // Add city from Person table
                        'Representative_name' => $row->Representative_name,
                        'Wife_id' => $row->Wife_id,
                        'Wife_name' => $row->Wife_name,
                        'Status' => $row->Status,
                        'Reason_for_suspension' => $row->Reason_for_suspension,
                        'Male_members' => $row->Male_members,
                        'Female_members' => $row->Female_members,
                        'Individuals_less_than_3_years' => $row->Individuals_less_than_3_years,
                        'Individuals_with_chronic_diseases' => $row->Individuals_with_chronic_diseases,
                        'Individuals_with_disabilities' => $row->Individuals_with_disabilities,
                        'Breadwinner' => $row->Breadwinner,
                        'Housing_condition' => $row->Housing_condition,
                        'Notes' => $row->Notes,
                    ]);
                }
            }
        });

        // Insert missing data records into the MissingData table
        if ($missingDataRecords->isNotEmpty()) {
            MissingData::insert($missingDataRecords->toArray());
            Log::info("Inserted " . count($missingDataRecords) . " missing records.");
        }

        // Insert processed data records into the CityData table
        if ($processedRecords->isNotEmpty()) {
            CityData::upsert($processedRecords->toArray(), ['CI_ID_NUM'], [
                'CI_ID_NUM',
                'CI_FIRST_ARB',
                'CI_FATHER_ARB',
                'CI_GRAND_FATHER_ARB',
                'CI_FAMILY_ARB',
                'Phone_number',
                'Family_count',
                'CITTTTY',
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
                'Notes',
            ]);
            Log::info("Processed " . count($processedRecords) . " records.");
        }
    }
}
