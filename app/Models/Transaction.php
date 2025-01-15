<?php

namespace App\Models;

use App\Models\Trade;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    public function setMutationValueAttribute($value)
    {
        $this->attributes['mutation_value'] = (int) round(floatval($value) * 100);
    }

    public static function getAvailableCash()
    {
        $cash = DB::table('manual_transactions')->pluck('total_transaction_value')->sum() / 100;

        // $investedCash = 0;
        
        // foreach (Trade::getNames() as $name) {
        //     $investedCash += Trade::getTotalAmoundInvested($name);
        // }

        // return $cash - $investedCash;

        return 100;
    }
}
