<?php

namespace App\Http\Controllers;

use App\Imports\FoodImport;
use App\Imports\GeneralFoodImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Food;
use App\Http\Controllers\Operators;

class ImportController extends Controller {

    public function index() {
        return view('importops');
    }

    public function import() {
        Excel::import(new FoodImport, 'importsheets/soups_with_tags.xlsx', 'local');

        return redirect('/import-ops')->with('success', 'Soups successfully imported.');
    }

    public function importDairy(Request $request) {
        Excel::import(new GeneralFoodImport, 'importsheets/dairy.xlsx', 'local');

        return redirect('/import-ops')->with('success', 'Dairy successfully imported.');
    }

    public function importBroth(Request $request) {
        Excel::import(new GeneralFoodImport, 'importsheets/broth_stock.xlsx', 'local');

        return redirect('/import-ops')->with('success', 'Broth successfully imported.');
    }

    public function modifyExistingSoups() {
        $foods = Food::where('nutrition_method', 'import')
            ->where('rankings->fano', 'Soup')
            ->get();

        // Go through, unset fano, set Soup as first tag
        for ($i=0; $i < count($foods); $i++) { 
            $food = $foods[$i];
            $rankings = $food->rankings;
            array_unshift($rankings['tags'], 'Soup');
            unset($rankings['fano']);
            $food->update([
                'rankings' => $rankings
            ]);
            $food->save();
        }

        return view('importops');
    }

}