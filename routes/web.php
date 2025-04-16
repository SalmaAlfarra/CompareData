<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\ExcelController;
use Illuminate\Support\Facades\Route;

// 🟢 الصفحة الرئيسية: توجه المستخدم حسب حالة تسجيل الدخول
Route::get('/', function () {
    if (auth('admin')->check()) {
        return redirect()->route('excel.upload'); // أو أي صفحة داخل النظام بعد تسجيل الدخول
    }
    return redirect()->route('admin.login');
});

// 🟡 واجهات تسجيل الدخول والخروج محمية بـ guest
Route::middleware('guest:admin')->group(function () {
    Route::get('admin/login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('admin/login', [AuthController::class, 'login']);
});

// 🔴 تسجيل الخروج من داخل السيشن
Route::post('admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// 🔵 مجموعة الراوت المحمية بـ Middleware "admin"
Route::middleware('admin')->group(function () {
    Route::get('/upload', [ExcelController::class, 'index'])->name('excel.upload');
    Route::post('/import', [ExcelController::class, 'import'])->name('excel.import');
    Route::post('/importCity', [ExcelController::class, 'importCity'])->name('excel.importCity');
    Route::get('/viewData', [ExcelController::class, 'viewData'])->name('excel.data');
    Route::get('/viewMissigData', [ExcelController::class, 'viewMissigData'])->name('excel.missigData');
    Route::get('/downloadData', [ExcelController::class, 'downloadDataExcel'])->name('excel.downloadData');
    Route::get('/downloadMissingData', [ExcelController::class, 'downloadMissingDataExcel'])->name('excel.downloadMissingData');
});
