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
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Illuminate\Validation\Rule;

/**
 * This should be renamed for just mixed-dish, soups.  A more general import is in GeneralFoodImport.php.
 */

class GeneralFoodImport implements ToModel, WithValidation, SkipsOnFailure, WithBatchInserts
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
        $startRow = 13;

        while (isset($row[$startRow])) {
            if ( $row[$startRow] ) {
                array_push($tags, $row[$startRow]);
            }
            $startRow++;
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
                'category' => $row[12],
                'rank' => $row[10]
            ),
            'tags' => $tags
        );
        $food->status = '200';


        return $food;
    }

    public function uniqueBy() {
        return 'upc';
    }

    public function rules(): array {
        return [
            '0' => Rule::unique('food', 'upc')
        ];
    }

    public function batchSize() : int {
        return 2000;
    }
}
