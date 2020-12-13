<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Operators extends Controller
{
    public static function lte($x,$y) {
        return $x <= $y;
    }

    public static function lt($x,$y) {
        return $x < $y;
    }
}
