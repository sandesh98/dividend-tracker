<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DividendController extends Controller
{
    public function index()
    {
        return view('dividend.index');
    }
}
