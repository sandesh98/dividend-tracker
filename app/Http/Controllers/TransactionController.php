<?php

namespace App\Http\Controllers;

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
