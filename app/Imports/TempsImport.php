<?php

namespace App\Imports;

use App\Models\Temp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class TempsImport implements ToModel, withStartRow, WithUpserts
{
    public string $uuid;

    public function __construct($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @param array $row
     *
     * @return Model|Temp|null
     */
    public function model(array $row): Model|Temp|null
    {
        if (empty(trim($row[0]))) {
            Log::warning("Skipping  \"" . ($row[1]) . "\" due to missing CI_ID_NUM");
            return null;
        }

        return new Temp([
            'xlxs_uuid' => $this->uuid,
            'CI_ID_NUM' => trim($row[0]),
            'Full_name' => trim($row[1]),
            'Phone_number' => trim($row[2]),
            'Family_count' => (int) trim($row[3]),
            'Wife_id' => trim($row[4]),
            'Wife_name' => trim($row[5]),
            'Male_members' => (int) trim($row[6]),
            'Female_members' => (int) trim($row[7]),
            'Individuals_less_than_3_years' => (int) trim($row[8]),
            'Individuals_with_chronic_diseases' => (int) trim($row[9]),
            'Individuals_with_disabilities' => (int) trim($row[10]),
            'Breadwinner' => trim($row[11]),
            'Housing_condition' => trim($row[12]),
            'Notes' => trim($row[13]),
        ]);
    }

    public function startRow(): int
    {
        return 2; // تعديل الرقم ليبدأ من الصف الثاني
    }

    public function uniqueBy(): string
    {
        return 'CI_ID_NUM';
    }
}
