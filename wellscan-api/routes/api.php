<?php
use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('api')->get('foods', 'App\Http\Controllers\ApiController@getAllFoods');
// Route::get('foods/{upc}', 'ApiController@getFood');
Route::middleware('api')->post('foods', 'App\Http\Controllers\ApiController@createFood');

Route::middleware('api')->get('foods/lookup', 'App\Http\Controllers\ApiController@lookUpFood');

Route::middleware('api')->get('foods/usda', 'App\Http\Controllers\ApiController@getFromUSDA');


// Route::put('foods/{upc}', 'ApiController@updateFood');
// Route::delete('foods/{id}','ApiController@deleteFood');