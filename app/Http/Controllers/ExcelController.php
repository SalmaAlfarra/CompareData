<?php
// app/Http/Controllers/ExcelController.php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\ProcessExcelImport;
use App\Models\Data;

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
        return redirect()->route('excel.view')->with('success', 'تمت معالجة البيانات بنجاح! المعالجة تتم في الخلفية.');
    }

    // دالة لعرض بيانات المستفيدين
    public function viewData()
    {
        $data = Data::all();
        return view('excel.view', compact('data'));
    }

    public function downloadExcel()
    {
        return Excel::download(new DataExport, 'data.xlsx');
    }
}
