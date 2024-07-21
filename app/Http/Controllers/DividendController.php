<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DividendController extends Controller
{
    public function index()
    {
        $years = [2020, 2021, 2022, 2023, 2024];

        return view('dividend.index', compact('years'));
    }
}
