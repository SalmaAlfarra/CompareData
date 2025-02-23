<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use App\Exports\MissingDataExport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\ProcessExcelImport;
use App\Models\Data;
use App\Models\MissingData;

class ExcelController extends Controller
{
    /**
     * Display the Excel file upload page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('excel.import');  // Displays the 'excel.import' view for uploading an Excel file.
    }

    /**
     * Import data from the uploaded Excel file.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        // Validate the uploaded file to ensure it's an Excel file (xlsx or xls)
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // Store the uploaded file in the 'temp' folder within the storage directory
        $path = $request->file('file')->store('temp');

        // Dispatch the job to process the Excel file in the background using the queue
        ProcessExcelImport::dispatch($path, Str::uuid());

        // Redirect back to the 'excel.data' route with a success message
        return redirect()->route('excel.data')->with('success', 'Data has been processed successfully!');
    }

    /**
     * View the imported data from the 'Data' model.
     *
     * @return \Illuminate\View\View
     */
    public function viewData()
    {
        // Paginate the 'Data' model results, displaying 100 records per page
        $data = Data::paginate(300);
        return view('excel.viewData', compact('data'));  // Pass the paginated data to the view.
    }

    /**
     * View the missing data from the 'MissingData' model.
     *
     * @return \Illuminate\View\View
     */
    public function viewMissigData()
    {
        // Paginate the 'MissingData' model results, displaying 100 records per page
        $data = MissingData::paginate(300);
        return view('excel.viewMissingData', compact('data'));  // Pass the paginated missing data to the view.
    }

    /**
     * Download the exported data in Excel format.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadDataExcel()
    {
        // Trigger download of the 'DataExport' Excel export with a filename 'Data.xlsx'
        return Excel::download(new DataExport, 'Data.xlsx');
    }

    /**
     * Download the exported missing data in Excel format.
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function downloadMissingDataExcel()
    {
        // Trigger download of the 'MissingDataExport' Excel export with a filename 'Missig Data.xlsx'
        return Excel::download(new MissingDataExport, 'Missing Data.xlsx');
    }
}