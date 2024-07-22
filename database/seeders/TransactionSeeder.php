<?php

namespace Database\Seeders;

use App\Imports\TransactionsImport;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = storage_path('app/public/Account.csv');

        Excel::import(new TransactionsImport(), $filePath);
    }
}
