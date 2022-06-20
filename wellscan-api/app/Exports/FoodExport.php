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

    public function query() {
        if ( $this->fano ) {
            /* When traversing JSON like this, you receive a value in string quotes (and without if you are expecting integer and such.) */
            return Food::whereRaw('LOWER(rankings->"$.fano") = ?', '"' . strtolower($this->fano) . '"');
        }
        return false;
    }
}
