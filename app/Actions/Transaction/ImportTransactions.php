<?php

namespace App\Actions\Transaction;

use App\Imports\TransactionsImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportTransactions
{
    /**
     * Create a new action instance.
     */
    public function __invoke(): void
    {
        $filepath = storage_path('app/public/Account-full.csv');

        if (! file_exists($filepath)) {
            echo 'Account.csv file not found inside app/public folder.';

            return;
        }

        Excel::import(new TransactionsImport, $filepath);
    }
}
