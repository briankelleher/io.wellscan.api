<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Food;
use App\Http\Controllers\NutritionSource_OFF;
use App\Http\Controllers\NutritionSource_USDA;

use App\Http\Controllers\Operators;

class ApiController extends Controller
{
      public function getAllFoods() {
        $foods = Food::get()->toJson(JSON_PRETTY_PRINT);
        return response($foods, 200);
      }
  
      public function createFood(Request $request) {
        $food = new Food;
        $food->name = $request->name;
        $food->upc = $request->upc;
        $food->nutrition_source = $request->nutrition_source;
        $food->nutrition_method = $request->nutrition_method;
        $food->nutrition = $request->nutrition;
        $food->rankings = $request->rankings;
        $food->status = "200";
        $food->save();

        return response()->json($food, 201);
      }

      public function lookUpFood(Request $request) {
        $data = array();

        $data['status'] = 404;
        $sources['off'] = new NutritionSource_OFF();
        $sources['usda'] = new NutritionSource_USDA();
        $sources['fatsecret'] = new NutritionSource_FatSecret();

        // for count(sources), while $data['status'] == 404;

        
        $data = $sources['off']->getNutritionByUPC($request);

        if ($data['status'] !== 404): 
            if(isset($request->category)) {
                $data['rankings']['swap']['category'] = $request->category;
                $data['rankings']['swap']['rank'] = $this->calculateSWAPRankByNutritionInfo($data['nutrition'], $request->category);
            }
            $data['upc'] = $request->upc;
            $data['status'] = 200;
        endif;

        return $data;
      }


      public function getFromUSDA(Request $request) {
        $source = new NutritionSource_USDA();
        $nuts = $source->getNutritionByUPC($request);

        return $nuts;

      }


      // last touched 13 November 2020
      public function calculateSWAPRankByNutritionInfo($nuts, $cat) {

       
        $satfat = $nuts['nf_saturated_fat'];
        $sodium = $nuts['nf_sodium'];
        $sugars = $nuts['nf_sugars'];

        $path = storage_path() . "/ranking-db/swap.json"; // ie: /var/www/laravel/app/storage/ranking-db/swap.json
        $db = json_decode(file_get_contents($path), true); 
        $rank = $db['default-rank'];

        $tests = $db['categories'][$cat];
        
        $pass = false;
       
        foreach($tests as $test) {
            if (!$pass): //if we have to keep checking
                
                $requirements = $test['requirements'];
                $name = $test['rank'];
                $pass = true;
                
                $i = 0;
                while ($i < count($requirements) && $pass) {

                    $operator = $requirements[$i]['operator'];
                    $property = $requirements[$i]['property'];
                    $cutoff = $requirements[$i]['value'];

                    $pass = Operators::$operator($nuts[$property], $cutoff);

                    $i++;
                }
            endif;

            if ($pass) {
                $rank = $name;
            }
        }

        return $rank;

      }
  
      public function getFood($upc) {
        // logic to get a Food by UPC goes here
      }
  
      public function updateFood(Request $request, $upc) {
        // logic to update a Food record goes here
      }
  
      public function deleteFood($upc) {
        // logic to delete a Food record goes here
      }
}
