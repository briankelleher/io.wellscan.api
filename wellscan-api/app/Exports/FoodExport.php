<?php

namespace App\Exports;

use DB;
use App\Models\Food;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FoodExport implements FromQuery, WithMapping, WithHeadings
{
    use Exportable;

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

        if ( isset($this->her) ) {
            return Food::whereRaw('lower(rankings->>"$.swap.category") = ?', strtolower($this->her));
        }

        if ( isset($this->tag) ) {
            return Food::whereRaw('json_search(lower(rankings->>"$.tags"), "one", lower(?))', $this->tag);
        }

        return false;
    }

    public function map($food) : array {
        $export = [
            $food->upc,
            $food->name,
            isset($food->nutrition['nf_sugars']) ? $food->nutrition['nf_sugars'] : 0,
            isset($food->nutrition['nf_sodium']) ? $food->nutrition['nf_sodium'] : 0,
            isset($food->nutrition['nf_saturated_fat']) ? $food->nutrition['nf_saturated_fat'] : 0,
            (!isset($food->nutrition['nf_added_sugars']) || $food->nutrition['nf_added_sugars'] === 'N/A') ? 0 : $food->nutrition['nf_added_sugars'],
            isset($food->rankings['swap']['category']) ? $food->rankings['swap']['category'] : 'no-category',
            isset($food->rankings['swap']['rank']) ? $food->rankings['swap']['rank'] : 'unranked'
        ];

        if ( isset($food->rankings['tags']) ) {
            foreach ($food->rankings['tags'] as $tag) {
                array_push($export, $tag);
            }
        }
        
        return $export;
    }

    public function headings() : array {
        return [
            'UPC',
            'Name',
            'Sugar',
            'Sodium',
            'Saturated Fat',
            'Added Sugars',
            'SWAP/Her Category',
            'Ranking',
            'Tags'
        ];
    }
}
