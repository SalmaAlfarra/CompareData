<?php

// Import necessary classes, in this case, we need the ExcelController that handles import/export operations for Excel files
use App\Http\Controllers\ExcelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
| These routes are loaded by the RouteServiceProvider, and all routes will
| be assigned to the "web" middleware group. Feel free to add any web routes
| that will handle operations like uploading or importing data.
|
*/

// Route to display the upload page
Route::get('/upload', [ExcelController::class, 'index'])->name('excel.upload');
// When the user visits the /upload route, they will be directed to the 'index' method in the ExcelController
// This method displays the page for uploading data

// Route to handle data import from an Excel file
Route::post('/import', [ExcelController::class, 'import'])->name('excel.import');
// When the user sends a POST request to the /import route, the 'import' method in the ExcelController is called
// This method handles importing data from the uploaded Excel file

// Route to handle data import from an Excel file
Route::post('/importCity', [ExcelController::class, 'importCity'])->name('excel.importCity');
// When the user sends a POST request to the /importCity route, the 'importCity' method in the ExcelController is called
// This method handles importing data from the uploaded Excel file

// Route to display the imported data
Route::get('/viewData', [ExcelController::class, 'viewData'])->name('excel.data');
// When the user visits the /viewData route, they will be directed to the 'viewData' method in the ExcelController
// This method displays the data that was imported from the Excel file

// Route to view missing data (data that contains missing values)
Route::get('/viewMissigData', [ExcelController::class, 'viewMissigData'])->name('excel.missigData');
// When the user visits the /viewMissigData route, they will be directed to the 'viewMissigData' method in the ExcelController
// This method displays the data that contains missing values

// Route to download the imported data as an Excel file
Route::get('/downloadData', [ExcelController::class, 'downloadDataExcel'])->name('excel.downloadData');
// When the user visits the /downloadData route, the 'downloadDataExcel' method in the ExcelController is called
// This method allows the user to download the imported data as an Excel file

// Route to download the missing data as an Excel file
Route::get('/downloadMissingData', [ExcelController::class, 'downloadMissingDataExcel'])->name('excel.downloadMissingData');
// When the user visits the /downloadMissingData route, the 'downloadMissingDataExcel' method in the ExcelController is called
// This method allows the user to download the missing data as an Excel file
