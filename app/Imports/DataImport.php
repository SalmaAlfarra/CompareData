<?php

namespace App\Imports;

use App\Models\Data;
use Maatwebsite\Excel\Concerns\ToModel;

class DataImport implements ToModel
{
    /**
     * This method is responsible for mapping a row of data from the Excel file to the `Data` model.
     *
     * @param array $row - The row data from the Excel file, typically as an associative array.
     *
     * @return \Illuminate\Database\Eloquent\Model|null - A new instance of the `Data` model populated with the row data.
     */
    public function model(array $row)
    {
        // Create and return a new instance of the `Data` model with the data from the current row.
        return new Data([
            'CI_ID_NUM'       => $row['CI_ID_NUM'],
            'full_name'       => $row['full_name'],
            'phone_number'    => $row['phone_number'],
            'total_members'   => $row['total_members'],
            'wife_id'         => $row['wife_id'],
            'wife_name'       => $row['wife_name'],
            'male_members'    => $row['male_members'],
            'female_members'  => $row['female_members'],
        ]);
    }
}