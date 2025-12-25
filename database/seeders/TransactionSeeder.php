<?php

namespace Database\Seeders;

use App\Actions\Transaction\ImportTransactions;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(ImportTransactions $importTransactions): void
    {
        $importTransactions->__invoke();
    }
}
