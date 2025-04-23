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
use Illuminate\Support\Facades\Log;

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
        // تحقق من صحة الملف المرفوع
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        try {
            // حفظ الملف في مجلد مؤقت
            $path = $request->file('file')->store('temp');

            // تنفيذ عملية الاستيراد في الخلفية
            ProcessExcelImport::dispatch($path, Str::uuid());

            // إعادة التوجيه مع رسالة نجاح
            return redirect()->route('excel.data')->with('success', 'تم رفع ومعالجة البيانات بنجاح!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // أخطاء التحقق الخاصة بالإكسل
            $failures = $e->failures();

            // إرسال المستخدم إلى صفحة الأخطاء مع تفاصيل المشاكل
            return view('excel.importerror', [
                'message' => 'حدثت أخطاء في بعض الصفوف أو الأعمدة في ملف الإكسل. يرجى مراجعة التفاصيل.',
                'failures' => $failures,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // أخطاء قواعد البيانات (مثل مشاكل النوع أو المفاتيح الفريدة)
            Log::error('خطأ في قاعدة البيانات أثناء استيراد الإكسل: ' . $e->getMessage());

            return view('excel.importerror', [
                'message' => 'حدثت مشكلة في حفظ البيانات في قاعدة البيانات. يرجى التأكد من صحة أنواع البيانات وعدم وجود تكرارات.',
                'errorDetails' => $e->getMessage(),
            ]);
        } catch (\ErrorException $e) {
            // أخطاء PHP مثل نقص الأعمدة أو دوال غير معرفة
            Log::error('خطأ عام في التنفيذ: ' . $e->getMessage());

            return view('excel.importerror', [
                'message' => 'حدث خطأ أثناء قراءة أو تنفيذ الملف. هناك مشكلة في تنسيق الملف أو أسماء الأعمدة. يرجى التأكد من أن جميع الأعمدة المطلوبة موجودة في الملف.',
                'errorDetails' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            // أي خطأ عام غير متوقع
            Log::error('خطأ غير متوقع في استيراد الإكسل: ' . $e->getMessage());

            return view('excel.importerror', [
                'message' => 'حدث خطأ غير متوقع أثناء الاستيراد. الرجاء المحاولة لاحقاً أو التواصل مع الدعم الفني.',
                'errorDetails' => $e->getMessage(),
            ]);
        }
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