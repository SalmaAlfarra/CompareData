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
            'no' => trim($row[0]),
            'national_id' => trim($row[1]),
            'full_name' => trim($row[2]),
            'phone_number' => trim($row[3]),
            'alternative_phone_number' => ((int) trim($row[4])) ? trim($row[4]) : null,
            'family_count' => trim($row[5]),
            'gathering_name' => trim($row[6]),
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }

    public function uniqueBy(): string
    {
        return 'national_id';
    }
}
