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

Route::get('foods/{upc}', 'App\Http\Controllers\ApiController@getFood');

Route::middleware('api')->post('foods', 'App\Http\Controllers\ApiController@createFood');

Route::get('foods/lookup/{upc}', 'App\Http\Controllers\ApiController@lookUpFood');

Route::get('foods/lookup/{upc}/{category}', 'App\Http\Controllers\ApiController@lookUpFood');

Route::get('foods/usda/{upc}', 'App\Http\Controllers\ApiController@getFromUSDA');
Route::get('foods/off/{upc}', 'App\Http\Controllers\ApiController@getFromOFF');

Route::get('foods/calculateRank/{upc}/{category}', 'App\Http\Controllers\ApiController@calculateRank');
Route::get('foods/rankFromNuts/{category}/{satfat}/{sodium}/{added_sugars}/{sugars}', 'App\Http\Controllers\ApiController@calculateRankFromNutrients');


// Route::put('foods/{upc}', 'App\Http\Controllers\ApiController@updateFood');

// Route::delete('foods/{id}','ApiController@deleteFood');

/**
 * HER RANKING NOTE
 * Use the added sugar value when available on the Nutrition Facts Label. 
 * If it is not available, use the total sugar value. 
 * 
 * The thresholds are the same for all categories except fruits and 
 * vegetables and dairy. 
 * 
 * For both fruits and vegetables and dairy, 
 * total sugar thresholds are ≤ 12 grams for the “choose often” tier, 
 * 
 * 13 to 23 grams for the “choose sometimes tier,” 
 * and ≥24 grams for the “choose rarely” tier.
 * 
 * 
 */