<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\FoodExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Food;

class ExportController extends Controller {

    public function index() {
        $fanos = Food::selectRaw('rankings->>"$.fano" as fano')->distinct()->get();

        $hers = Food::selectRaw('rankings->>"$.swap.category" as her')->distinct()->get();

        return view('export', [
            'fanos' => $fanos,
            'hers' => $hers
        ]);
    }

    public function exportFano(Request $request) {
        $fano = $request->fano;

        return (new FoodExport)->forFano($fano)->download('food_fano.xlsx');
    }

    public function exportHer(Request $request) {
        $her = $request->her;

        return (new FoodExport)->forHer($her)->download('food_her.xlsx');
    }

    public function exportTag(Request $request) {
        $tag = $request->tag;

        return (new FoodExport)->forTag($tag)->download('food_tag.xlsx');
    }

}