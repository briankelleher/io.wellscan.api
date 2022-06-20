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

    Route::get('export', [ExportController::class, 'index']);
    Route::post('export/fano/', [ExportController::class, 'exportFano'])->name('export-fano');
});

