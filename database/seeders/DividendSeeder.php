<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DividendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stocks = Transaction::whereNotNull('product')
            ->where('product', 'NOT LIKE', 'FLATEX EURO BANKACCOUNT')
            ->where('description', 'LIKE', 'iDEAL Deposit')
            ->get();
    }
}
