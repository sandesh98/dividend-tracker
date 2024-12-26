<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManualTransactionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = Transaction::where('description', 'LIKE', 'iDEAL Deposit')
                            ->orWhere('description', 'LIKE', 'flatex terugstorting')
                            ->get();

        foreach($transactions as $transaction) {
            DB::insert('INSERT INTO manual_transactions (date, time, description, currency, total_transaction_value, created_at, updated_at) VALUES (:date, :time, :description, :currency, :total_transaction_value, :created_at, :updated_at)', [
                'date' => $transaction->date,
                'time' => $transaction->time,
                'description' => $transaction->description,
                'currency' => $transaction->mutation,
                'total_transaction_value' => $transaction->mutation_value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
