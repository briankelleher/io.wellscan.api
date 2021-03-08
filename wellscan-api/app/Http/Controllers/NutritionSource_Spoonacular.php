<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NutritionSource_Spoonacular extends Controller
{
    public function __construct() {
        $this->source = "spoonacular";
        $this->endpoint = "https://api.spoonacular.com/food/products/upc/";
    }

    public function getNutritionByUPC($upc) {
        $url = $this->endpoint . $upc . "?apiKey=".env('SPOONACULAR_KEY');

  
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($res);

        

        

        if(isset($res->nutrition->nutrients)) {
            $nuts = $res->nutrition->nutrients;

            $data = array();

            foreach($nuts as $nut) {
                $data[$nut->name] = $nut->amount;
            }

        
            $data['name'] = $res->title;
            $data['upc'] = $upc;
            $data['nutrition']['nf_sodium'] = $data['Sodium'];
            $data['nutrition']['nf_saturated_fat'] = $data['Saturated Fat'];
            $data['nutrition']['nf_sugars'] = $data['Sugar'];

            $data['nutrition']['nf_added_sugars'] = "NA";
            
            $data['nutrition_source'] = $this->source;
            $data['nutrition_method'] = 'automated';
            $data['msg'] = "Found product in Spoonacular";
            $data['status'] = 200;
        } else {
            $data['status'] = 404;
            $data['msg'] = "Product not found in Spoonacular";
        }
      

        
        return $data;

    }
}
