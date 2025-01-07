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

Route::get('/view', [ExcelController::class, 'viewData'])->name('excel.view');

Route::get('/download', [ExcelController::class, 'downloadExcel'])->name('excel.download');