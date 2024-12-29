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
        if (empty(trim($row[0]))) {
            Log::warning("Skipping  \"" . ($row[1]) . "\" due to missing national_id");
            return null;
        }

        return new Temp([
            'xlxs_uuid' => $this->uuid,
            'national_id' => trim($row[0]),
            'full_name' => trim($row[1]),
            'phone_number' => trim($row[2]),
            'family_count' => trim($row[3]),
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
