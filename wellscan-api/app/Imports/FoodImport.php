<?php

namespace App\Imports;

use App\Models\Food;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithUpsertColumns;

class FoodImport implements ToModel, WithUpserts, WithUpsertColumns
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $food = new Food();

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
            )
        );
        $food->status = '200';


        return $food;
    }

    public function uniqueBy() {
        return 'upc';
    }

    public function upsertColumns() {
        return ['nutrition_method', 'nutrition'];
    }
}
