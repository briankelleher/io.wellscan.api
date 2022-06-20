<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\FoodExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Food;

class ExportController extends Controller {

    public function index() {
        $fanos = Food::selectRaw('rankings->"$.fano" as fano')->distinct()->get();

        return view('export', [
            'fanos' => $fanos
        ]);
    }

    public function exportFano(Request $request) {
        $fano = $request->fano;

        return (new FoodExport)->forFano($fano)->download('food.xlsx');
    }

}