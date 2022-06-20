<?php

namespace App\Exports;

use DB;
use App\Models\Food;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;

class FoodExport implements FromQuery
{
    use Exportable;

    public function forFano(string $fano) {
        $this->fano = $fano;
        return $this;
    }

    public function forTag(string $tag) {
        $this->tag = $tag;
        return $this;
    }

    public function forHer(string $her) {
        $this->her = $her;
        return $this;
    }

    public function query() {
        if ( isset($this->fano) ) {
            /* When traversing JSON like this, you receive a value in string quotes (and without if you are expecting integer and such.) */
            return Food::whereRaw('LOWER(rankings->>"$.fano") = ?', strtolower($this->fano));
        }

        if ( isset($this->her) ) {
            return Food::whereRaw('lower(rankings->>"$.swap.category") = ?', strtolower($this->her));
        }

        if ( isset($this->tag) ) {
            return Food::whereRaw('json_search(lower(rankings->>"$.tags"), "one", lower(?))', $this->tag);
        }

        return false;
    }
}
