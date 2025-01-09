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

Route::get('/upload', [ExcelController::class, 'index'])->name('excel.upload');

Route::post('/import', [ExcelController::class, 'import'])->name('excel.import');

Route::get('/viewData', [ExcelController::class, 'viewData'])->name('excel.data');

Route::get('/viewMissigData', [ExcelController::class, 'viewMissigData'])->name('excel.missigData');

Route::get('/downloadData', [ExcelController::class, 'downloadDataExcel'])->name('excel.downloadData');

Route::get('/downloadMissingData', [ExcelController::class, 'downloadMissingDataExcel'])->name('excel.downloadMissingData');
