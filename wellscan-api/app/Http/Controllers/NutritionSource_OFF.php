<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NutritionSource_OFF extends Controller
{
    public function __construct() {
        $this->source = "open-food-facts";
        $this->endpoint = "https://world.openfoodfacts.org/api/v0/product/";
    }

    public function getNutritionByUPC($upc) {
        $url = $this->endpoint . $upc . ".json";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $res = curl_exec($ch);
        if ($res === FALSE) {
            die("Curl failed: " . curL_error($ch));
        }
        curl_close($ch);


        $nf_sodium = 'sodium_serving';
        $nf_saturated_fat = 'saturated-fat_serving';
        $nf_sugars = 'sugars_serving';
        $nf_added_sugar = 'sugars_added';

        $response = json_decode($res);
        if(isset($response->product)) {
            $sod_val = $response->product->nutriments->$nf_sodium ?? 0;
            $data['name'] = $response->product->product_name;
            $data['nutrition']['nf_saturated_fat'] = $response->product->nutriments->$nf_saturated_fat ?? 0;
            $data['nutrition']['nf_sodium'] = $sod_val * 1000;
            $data['nutrition']['nf_sugars'] = $response->product->nutriments->$nf_sugars ?? 0;
            if(isset($response->product->nutriments->$nf_added_sugar)) {
                $data['nutrition']['nf_added_sugars'] = $response->product->nutriments->$nf_added_sugar;
            } else {
                $data['nutrition']['nf_added_sugars'] = "NA";
            }
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
