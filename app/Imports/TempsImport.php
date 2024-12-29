<?php

namespace App\Imports;

use App\Models\Temp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
        if (empty(trim($row[1]))) {
            Log::warning("Skipping Row #" . ($row[2]) . " due to missing national_id");
            return null;
        }

        return new Temp([
            'xlxs_uuid' => $this->uuid,
            // 'no' => trim($row[0]),
            'national_id' => trim($row[0]),
            'full_name' => trim($row[1]),
            'phone_number' => trim($row[2]),
            // 'alternative_phone_number' => ((int) trim($row[3])) ? trim($row[3]) : null,
            'family_count' => (int) trim($row[3]),
            // 'gathering_name' => trim($row[6]),
            'wife_id' => trim($row[4]),
            'wife_name' => trim($row[5]),
            'male_members' => (int) trim($row[6]),
            'female_members' => (int) trim($row[6]),

        ]);
    }

    public function startRow(): int
    {
        return 1;
    }

    public function uniqueBy(): string
    {
        return 'national_id';
    }
}
