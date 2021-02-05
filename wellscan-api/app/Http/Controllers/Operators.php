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

    public static function gt($x, $y) {
        return $x > $y;
    }

    public static function gte($x, $y) {
        return $x >= $y;
    }

    public static function eq($x, $y) {
        return $x == $y;
    }
}
