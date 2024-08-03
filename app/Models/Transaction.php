<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use HasFactory;

    public static function getAvailableCash()
    {
        $transaction = self::where('description', 'LIKE', 'Valuta Creditering')->first();
        return $transaction ? $transaction->balance_value : 0;
    }

    public static function getTransactionCosts()
    {
        $totalTransactionCost = self::where('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden')
            ->orWhere('description', 'LIKE', 'DEGIRO Aansluitingskosten%')
            ->sum(DB::raw('CAST(mutation_value AS DECIMAL(10,2))'));

        return (int) $totalTransactionCost;
    }
}
