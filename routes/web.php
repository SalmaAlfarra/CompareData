<?php

use App\Http\Controllers\ExcelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/upload', [ExcelController::class, 'index'])->name('excel.upload'); // عرض صفحة رفع ملف Excel

Route::post('/import', [ExcelController::class, 'import'])->name('excel.import'); // استيراد البيانات من ملف Excel

Route::get('/view', [ExcelController::class, 'viewData'])->name('excel.view'); // عرض بيانات المستفيدين

Route::get('download-excel', [ExcelController::class, 'downloadExcel'])->name('excel.download'); // تنزيل بيانات المستفيدين

// Route::get('/', function () {
//     return view('excel.view');
// });

// Route::get('/upload', function () {
//     return view('excel.import');
// })->name('excel.form');

// Route::post('/import', [ExcelController::class, 'import'])->name('excel.import');

// Route::get('/data', [ExcelController::class, 'viewData'])->name('data.view');