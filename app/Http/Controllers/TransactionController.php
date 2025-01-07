<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        // transacties tonen
            // - aankopen van aandelen
            // - verkopen van aandelen
            // - ontvangen dividenden
            
        return view('transaction.index');
    }
}
