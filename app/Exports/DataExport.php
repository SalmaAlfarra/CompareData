<?php

namespace App\Exports;

use App\Models\Data;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataExport implements FromCollection, WithHeadings, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Fetch data and add a sequential number column
        $data = Data::all([
            'CI_ID_NUM',
            'full_name',
            'phone_number',
            'family_count',
            'wife_id',
            'wife_name',
            'male_members',
            'female_members'
        ]);

        $result = collect();
        $counter = 1;

        foreach ($data as $row) {
            $result->push([
                'number' => $counter++, // Incremental number
                'CI_ID_NUM' => $row->CI_ID_NUM,
                'CI_FIRST_ARB' => $row->CI_FIRST_ARB,
                'CI_FATHER_ARB' => $row->CI_FATHER_ARB,
                'CI_GRAND_FATHER_ARB' => $row->CI_GRAND_FATHER_ARB,
                'CI_FAMILY_ARB' => $row->CI_FAMILY_ARB,
                'phone_number' => $row->phone_number,
                'family_count' => $row->family_count,
                'wife_id' => $row->wife_id,
                'wife_name' => $row->wife_name,
                'male_members' => $row->male_members,
                'female_members' => $row->female_members,
            ]);
        }

        return $result;
    }

    /**
     * Define the headings for the exported file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'الرقم',
            'رقم الهوية',
            'الاسم الأول',
            'اسم الأب',
            'اسم الجد',
            'اسم العائلة',
            'رقم الجوال',
            'عدد أفراد الأسرة',
            'رقم هوية الزوجة',
            'اسم الزوجة',
            'عدد الأفراد الذكور',
            'عدد الأفراد الإناث',
        ];
    }

    /**
     * Apply styles to the worksheet.
     *
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        // Set direction to right-to-left
        $sheet->setRightToLeft(true);

        // Apply styles to the header row
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14, // Increase header font size
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500'], // Orange color
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText' => true, // Enable text wrapping
            ],
        ]);

        // Apply styles to all rows (content alignment)
        $sheet->getStyle('A2:I' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Set borders for all cells
        $sheet->getStyle('A1:I' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Auto-size columns to fit content (AutoSize)
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true); // Auto-size each column
        }

        // Set row height slightly larger for better readability
        $sheet->getDefaultRowDimension()->setRowHeight(20); // Adjust height globally
    }
}
