<?php
// app/Http/Controllers/ExcelController.php

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
    // عرض صفحة رفع ملف Excel
    public function index()
    {
        return view('excel.import');
    }

    // استيراد البيانات من ملف Excel
    public function import(Request $request)
    {
        // التحقق من صحة الملف
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        // حفظ الملف في المسار المؤقت
        $path = $request->file('file')->store('temp');


        // إرسال الوظيفة إلى Queue لمعالجة البيانات في الخلفية
        ProcessExcelImport::dispatch($path,Str::uuid());

        // إعادة توجيه المستخدم مع رسالة نجاح
        return redirect()->route('excel.data')->with('success', 'تمت معالجة البيانات بنجاح!.');
    }

    // دالة لعرض بيانات المستفيدين
    public function viewData()
    {
        $data = Data::paginate(100); // عدد العناصر في كل صفحة 100
        return view('excel.viewData', compact('data'));
    }

    // دالة لعرض بيانات المستفيدين المفقودين
    public function viewMissigData()
    {
        $data = MissingData::paginate(100); // عدد العناصر في كل صفحة 100
        return view('excel.viewMissingData', compact('data'));
    }

    public function downloadDataExcel()
    {
        return Excel::download(new DataExport, 'Data.xlsx');
    }

    public function downloadMissingDataExcel()
    {
        return Excel::download(new MissingDataExport, 'Missig Data.xlsx');
    }
}