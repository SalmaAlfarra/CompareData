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

        $result = collect();
        $counter = 1;

        foreach ($data as $row) {
            $result->push([
                'number'                             => $counter++,
                'CI_ID_NUM'                          => $row->CI_ID_NUM,
                'CI_FIRST_ARB'                       => $row->CI_FIRST_ARB,
                'CI_FATHER_ARB'                      => $row->CI_FATHER_ARB,
                'CI_GRAND_FATHER_ARB'                => $row->CI_GRAND_FATHER_ARB,
                'CI_FAMILY_ARB'                      => $row->CI_FAMILY_ARB,
                'CITTTTY'                            => $row->CITTTTY,
                'Phone_number'                       => $row->Phone_number,
                'Family_count'                       => $row->Family_count,
                'Representative_name'                => $row->Representative_name,
                'Wife_id'                            => $row->Wife_id,
                'Wife_name'                          => $row->Wife_name,
                'Status'                             => $row->Status,
                'Reason_for_suspension'              => $row->Reason_for_suspension,
                'Male_members'                       => $row->Male_members,
                'Female_members'                     => $row->Female_members,
                'Individuals_less_than_3_years'      => $row->Individuals_less_than_3_years,
                'Individuals_with_chronic_diseases'  => $row->Individuals_with_chronic_diseases,
                'Individuals_with_disabilities'      => $row->Individuals_with_disabilities,
                'Breadwinner'                        => $row->Breadwinner,
                'Housing_condition'                  => $row->Housing_condition,
                'Notes'                              => $row->Notes
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
            'المدينة الأصلية',
            'رقم الجوال',
            'عدد أفراد الأسرة',
            'اسم المندوب',
            'رقم هوية الزوجة',
            'اسم الزوجة',
            'الحالة',
            'سبب الإيقاف',
            'عدد الأفراد الذكور',
            'عدد الأفراد الإناث',
            'عدد الأفراد أقل من 3 سنوات',
            'عدد الأفراد ذوي الأمراض المزمنة',
            'عدد الأفراد ذوي الإعاقة',
            'معيل الأسرة',
            'حالة المسكن',
            'الملاحظات'
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
        $sheet->getStyle('A1:V1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'size'  => 14, // Increase header font size
                'color' => ['rgb' => '000000'],
            ],
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFA500'], // Orange color
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'wrapText'   => true, // Enable text wrapping
            ],
        ]);

        // Apply styles to all rows (content alignment)
        $sheet->getStyle('A2:V' . $sheet->getHighestRow())->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
        ]);

        // Set borders for all cells
        $sheet->getStyle('A1:V' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);

        // Auto-size columns to fit content (AutoSize)
        foreach (range('A', 'V') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true); // Auto-size each column
        }

        // Set row height slightly larger for better readability
        $sheet->getDefaultRowDimension()->setRowHeight(20); // Adjust height globally
    }
}
