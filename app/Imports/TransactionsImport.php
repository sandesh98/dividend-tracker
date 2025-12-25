<?php

namespace App\Imports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class TransactionsImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Transaction([
            'date' => $row[0],
            'time' => $row[1],
            'value_date' => $row[2],
            'product' => $row[3],
            'isin' => $row[4],
            'description' => $row[5],
            'fx' => $row[6],
            'mutation' => $row[7],
            'mutation_value' => $row[8],
            'balance' => $row[9],
            'balance_value' => $row[10],
            'order_id' => $row[11],
        ]);
    }
}
