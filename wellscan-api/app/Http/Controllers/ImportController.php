<?php

namespace App\Http\Controllers;

use App\Imports\FoodImport;
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

        return redirect('/')->with('success', 'Food successfully imported.');
    }

}