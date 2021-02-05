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

        //return response()->json($food, 201);
      }

      public function lookUpFood($upc, $category=NULL) {
        
        $data = array();

        $data['status'] = 404;
        $sources['off'] = new NutritionSource_OFF();
        $sources['usda'] = new NutritionSource_USDA();
        //$sources['fatsecret'] = new NutritionSource_FatSecret();

        // for count(sources), while $data['status'] == 404;

        
        $data = $sources['off']->getNutritionByUPC($upc);

        if ($data['status'] !== 404): 
           // if we have a category, we can rank the food
            if(isset($category)) {
                $data['rankings']['swap']['category'] = $category;
                $data['rankings']['swap']['rank'] = $this->calculateSWAPRankByNutritionInfo($data['nutrition'], $category);
            } else {
                $data['rankings'] = array();
            }


            $data['upc'] = $upc;
            $data['status'] = 200;
            
            $food = Food::updateOrCreate(
              ['upc' => $data['upc']],
              [
                  'name' => $data['name'],
                  'nutrition' => $data['nutrition'],
                  'nutrition_source' => $data['nutrition_source'],
                  'rankings' => $data['rankings'],
                  'nutrition_method' => $data['nutrition_method'],
                  'status' => $data['status']
              ]
            );
            $food->save();
            
        endif;

        return $data;
      }


      public function calculateRank($upc, $category) {
        
   
        $food = new Food();
        $food = $food->where('upc',$upc)->limit(1)->get();
        $food = $food[0];
        
        
        
        $rank = $this->calculateSWAPRankByNutritionInfo($food->nutrition, $category);

        $data = [];
        $data['rankings']['swap']['rank'] = $rank;

        return $this->updateFood($upc, $data);
      }

      public function calculateRankFromNutrients($category, $sugars, $sodium, $satfat) {
       $nuts['nf_sugars'] = $sugars;
       $nuts['nf_sodium'] = $sodium;
       $nuts['nf_saturated_fat'] = $satfat;
       $nuts['nf_added_sugars'] = $sugars;

       $data['category'] = $category;
       $data['msg'] = "Rank calculated manually for {$category}.";
       $data['rank'] = $this->calculateSWAPRankByNutritionInfo($nuts, $category);

       return $data;
      }

      /** ~~~~~ Individual Nutrition Source Tests ~~~~~ */

      public function getFromUSDA($upc) {
        $source = new NutritionSource_USDA();
        $nuts = $source->getNutritionByUPC($upc);
        
        return $nuts;

      }

      public function getFromOFF($upc) {
        $source = new NutritionSource_OFF();
        $nuts = $source->getNutritionByUPC($upc);
        
        return $nuts;
      }

      /** ~~~~~ End Individual Nutrition Source Tests ~~~~~*/


      // last touched 13 November 2020
      public function calculateSWAPRankByNutritionInfo($nuts, $cat) {
        
       
        // $satfat = $nuts['nf_saturated_fat'];
        // $sodium = $nuts['nf_sodium'];
        // $sugars = $nuts['nf_sugars'];


          if ($cat == "fruits-vegetables" or $cat == "dairy") {
            $nuts['nf_added_sugars'] == 0;
          }
        

        $path = storage_path() . "/ranking-db/her.json"; // ie: /var/www/laravel/app/storage/ranking-db/*.json
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
        $food = Food::where('upc', '=', $upc)->get();

        return $food;
      }
  
      public function updateFood($upc, $data) {
        $food = Food::updateOrCreate(
          ['upc' => $upc],
          $data
        );
        $food->save();

        return $food;
      }

      public function prepareFoodForUpdate($upc, Request $request) {
        $food = Food::where('upc', '=', $upc)->get();

        $food->nutrition_source = $request['blame'];
        $food->nutrition_method = "manual";
        $food->nutrition = $request['nutrition'];
        $food->rankings = $request['rankings'];
        $food->name = $request['name'];

      }
  
      public function deleteFood($upc) {
        // logic to delete a Food record goes here
      }

}
