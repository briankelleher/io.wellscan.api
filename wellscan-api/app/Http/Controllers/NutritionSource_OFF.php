<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NutritionSource_OFF extends Controller
{
    public function __construct() {
        $this->source = "open-food-facts";
        $this->endpoint = "https://world.openfoodfacts.org/api/v0/product/";
    }

    public function getNutritionByUPC($request) {
        $url = $this->endpoint . $request->upc . ".json";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        $nf_sodium = 'sodium_serving';
        $nf_saturated_fat = 'saturated-fat_serving';
        $nf_sugars = 'sugars_serving';

        $response = json_decode($res);
        if(isset($response->product)) {
            $data['name'] = $response->product->product_name;
            $data['nutrition']['nf_saturated_fat'] = $response->product->nutriments->$nf_saturated_fat;
            $data['nutrition']['nf_sodium'] = $response->product->nutriments->$nf_sodium * 1000;
            $data['nutrition']['nf_sugars'] = $response->product->nutriments->$nf_sugars;
            $data['nutrition_source'] = $this->source;
            $data['nutrition_method'] = 'automated';
            $data['msg'] = "Found product in OpenFoodFacts";
            $data['status'] = 200;
        } else {
            $data['status'] = 404;
            $data['msg'] = "Product not found in OpenFoodFacts";
        }
        
        
        
        
        


        
        
        return $data;

        

    }
}
