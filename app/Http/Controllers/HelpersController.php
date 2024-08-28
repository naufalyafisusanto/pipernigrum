<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\Request;

class HelpersController extends Controller
{
    public function stations()
    {
        return Station::select('id', 'name', 'running', 'active')->get();
    }
}
