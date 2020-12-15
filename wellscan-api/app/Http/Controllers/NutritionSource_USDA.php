<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NutritionSource_USDA extends Controller
{
    public function __construct() {
        $this->source = "usda";
        $this->foodEndpoint = "https://api.nal.usda.gov/fdc/v1/food/";
        $this->usda_key = "jhO9J3G0g57ZSa30SiwgHVifajFle4ppbAk25AD9";
        $this->fdcid_endpoint = "https://api.nal.usda.gov/fdc/v1/foods/search?api_key=".$this->usda_key."&query=";
    }

    public function getNutritionByUPC($request) {
        $fdcid = $this->getFDCIDByUPC($request);

        
        if($fdcid['status'] == 200)
            $food = $this->getFoodByFDCID($fdcid['fdcId']);
        else {
            $food = $fdcid;
        }

        return $food;
    }

    public function getFDCIDByUPC($request) {

        // because of the way the USDA API works, the first step is to 
        // search the database for the UPC, then take the first result
        // and get its FDCID

        $url = $this->fdcid_endpoint . $request->upc;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        
        $res = json_decode($res);

        

        
        if (isset($res->foods[0])) {
            $data['status'] = 200;
            $data['fdcId'] = $res->foods[0]->fdcId;
        } else {
            $data = array ("status" => 404, "msg" => "could not find food in the USDA DB");
        }


        return $data;
    }

    public function getFoodByFDCID($fdcid) {
        $url = $this->foodEndpoint . $fdcid . "?api_key=" .$this->usda_key;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);
        //echo $res;
        $res = json_decode($res);
        
        $data = array();
        $data['name'] = $res->description;
        $data['nutrition']['nf_saturated_fat'] = $res->labelNutrients->saturatedFat->value ?? 0;
        $data['nutrition']['nf_sodium'] = $res->labelNutrients->sodium->value ?? 0;
        $data['nutrition']['nf_sugars'] = $res->labelNutrients->sugars->value ?? 0;
        $data['nutrition_source'] = "usda";
        $data['nutrition_method'] = "automated";
        $data['status'] = 200;
        $data['msg'] = "Found product in USDA Database";

        



        return $data;
    }
}
