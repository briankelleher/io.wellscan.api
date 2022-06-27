<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['importbasic'])->group(function() {
    Route::get('import-ops', [ImportController::class, 'index']);
    Route::post('import', [ImportController::class, 'import'])->name('import-soups');
    Route::post('import-dairy', [ImportController::class, 'importDairy'])->name('import-dairy');
    Route::post('import-broth', [ImportController::class, 'importBroth'])->name('import-broth');

    Route::get('export', [ExportController::class, 'index']);
    Route::post('export/her/', [ExportController::class, 'exportHer'])->name('export-her');
    Route::post('export/tag/', [ExportController::class, 'exportTag'])->name('export-tag');
    Route::post('export/complex/', [ExportController::class, 'exportComplexQuery'])->name('export-complex');
});

