<?php

namespace App\Imports;

use App\Models\Data;
use Maatwebsite\Excel\Concerns\ToModel;

class DataImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Data([
            'CI_ID_NUM' => $row['CI_ID_NUM'],
            'full_name' => $row['full_name'],
            'phone_number' => $row['phone_number'],
            'total_members' => $row['total_members'],
            'wife_id' => $row['wife_id'],
            'wife_name' => $row['wife_name'],
            'male_members' => $row['male_members'],
            'female_members' => $row['female_members'],
        ]);
    }
}