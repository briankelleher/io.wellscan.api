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

    public function complex(array $hers, array $tags) {
        $this->hers = array_map(function($h) {
            return strtolower($h);
        }, $hers);
        $this->tags = array_map(function($t) {
            return strtolower($t);
        }, $tags);
        $this->complex = true;
        return $this;
    }

    public function query() {
        /**
         * For the next dev:
         * -> will give value in quotes
         * ->> will give value unwrapped
         */
        if ( isset($this->complex) && isset($this->hers) && isset($this->tags) && $this->complex ) {
            $q = Food::whereRaw('lower(rankings->>"$.swap.category") in (?)', $this->hers)
            ->where(function($query) {
                $tag_combo_query = '';
                $tag_combo_query_count = 0;
                foreach ($this->tags as $t) {
                    if ( $t ) {
                        if ( $tag_combo_query_count < 1 ) {
                            $query->whereRaw("json_search(lower(rankings->>'$.tags'), 'one', ?)", $t);
                        } else {
                            $query->orWhereRaw("json_search(lower(rankings->>'$.tags'), 'one', ?)", $t);
                        }
                        $tag_combo_query_count++;
                    }
                }
            });
            return $q;
        }

        if ( isset($this->fano) ) {
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
