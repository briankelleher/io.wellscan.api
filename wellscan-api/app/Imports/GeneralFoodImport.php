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

        $upc = $row[0];
        $name = $row[1];
        $nutrition_source = 'usda';
        $nutrition_method = 'import';
        $sugars = floatval($row[5]);
        $sodium = floatval($row[6]);
        $saturated_fat = floatval($row[7]);
        $added_sugars = $row[8] ? floatval($row[8]) : 'N/A';
        $swap_category = $row[12];
        $swap_rank = $row[10];
        $status = '200';

        // Ignore header.  I would use the WithHeaderRow concern, but it might not always have one.
        if ( strtolower($row[0]) === 'upc' ) {
            return null;
        }

        while (isset($row[$startRow])) {
            if ( $row[$startRow] ) {
                array_push($tags, $row[$startRow]);
            }
            $startRow++;
        }

        $food->name = $name;
        $food->upc = $upc;
        $food->nutrition_source = $nutrition_source;
        $food->nutrition_method = $nutrition_method;
        $food->nutrition = array(
            'nf_sugars' => $sugars,
            'nf_sodium' => $sodium,
            'nf_saturated_fat' => $saturated_fat,
            'nf_added_sugars' => $added_sugars
        );
        $food->rankings = array(
            'swap' => array(
                'category' => $swap_category,
                'rank' => $swap_rank
            ),
            'tags' => $tags
        );
        $food->status = $status;


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
        return 5000;
    }
}
