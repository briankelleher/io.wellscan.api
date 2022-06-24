<?php

namespace App\Imports;

use App\Models\Food;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Validation\Rule;

/**
 * This should be renamed for just mixed-dish, soups.  A more general import is in GeneralFoodImport.php.
 */

class FoodImport implements ToModel, WithUpserts, WithUpsertColumns, WithValidation, SkipsOnFailure
{

    use Importable, SkipsFailures;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $food = new Food();

        $tags = [];
        $fano = '';

        if ( isset($row[13]) ) {
            $fano = $row[13];
        }
        foreach ([14,15,16] as $i) {
            if ( isset($row[$i]) ) {
                array_push($tags, $row[$i]);
            }
        }

        $food->name = $row[1];
        $food->upc = $row[0];
        $food->nutrition_source = 'usda';
        $food->nutrition_method = 'import';
        $food->nutrition = array(
            'nf_sugars' => floatval($row[5]),
            'nf_sodium' => floatval($row[6]),
            'nf_saturated_fat' => floatval($row[7]),
            'nf_added_sugars' => $row[8] ? floatval($row[8]) : 'N/A'
        );
        $food->rankings = array(
            'swap' => array(
                'category' => 'mixed-dish',
                'rank' => $row[10]
            ),
            'fano' => $fano,
            'tags' => $tags
        );
        $food->status = '200';


        return $food;
    }

    public function uniqueBy() {
        return 'upc';
    }

    public function upsertColumns() {
        return [];
    }

    public function rules(): array {
        return [
            '0' => Rule::unique('food', 'upc')
        ];
    }
}
