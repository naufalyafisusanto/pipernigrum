<?php

use App\Http\Controllers\HelpersController;

if (!function_exists('stations')) {
    function stations()
    {
        $helpers = new HelpersController;
        return $helpers->stations();
    }
}