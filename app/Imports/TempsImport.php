<?php

namespace App\Imports;

ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');

// Required models and libraries
use App\Models\Temp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class TempsImport implements ToModel, WithStartRow, WithUpserts
{
    // Unique identifier for the Excel import
    public string $uuid;

    /**
     * Constructor to initialize the class with a UUID.
     *
     * @param string $uuid
     */
    public function __construct($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * Map a single row from the Excel file to the `Temp` model.
     *
     * @param array $row
     * @return Model|Temp|null
     */
    public function model(array $row): Model|Temp|null
    {
        // Check if the primary identifier (CI_ID_NUM) is empty.
        if (empty(trim($row[0]))) {
            Log::warning("Skipping \"" . ($row[1]) . "\" due to missing CI_ID_NUM"); // Log a warning for missing CI_ID_NUM.
            return null; // Skip this row.
        }

        // Create a new `Temp` model instance with mapped data from the Excel row.
        return new Temp([
            'xlxs_uuid'                           => $this->uuid,
            'CI_ID_NUM'                           => trim($row[0]),
            'Full_name'                           => trim($row[1]),
            'Phone_number'                        => trim($row[2]),
            'Family_count'                        => (int) trim($row[3]),
            'Representative_name'                 => trim($row[4]),
            'Wife_id'                             => trim($row[5]),
            'Wife_name'                           => trim($row[6]),
            'Male_members'                        => (int) trim($row[7]),
            'Female_members'                      => (int) trim($row[8]),
            'Individuals_less_than_3_years'       => (int) trim($row[9]),
            'Individuals_with_chronic_diseases'   => (int) trim($row[10]),
            'Individuals_with_disabilities'       => (int) trim($row[11]),
            'Breadwinner'                         => trim($row[12]),
            'Housing_condition'                   => trim($row[13]),
            'Notes'                               => trim($row[14]),
        ]);
    }

    /**
     * Define the starting row for the Excel import.
     *
     * @return int
     */
    public function startRow(): int
    {
        return 2; // Start importing from the second row (ignoring headers).
    }

    /**
     * Specify the unique key for upserts (to avoid duplicates).
     *
     * @return string
     */
    public function uniqueBy(): string
    {
        return 'CI_ID_NUM'; // Use CI_ID_NUM as the unique identifier for upserts.
    }
}