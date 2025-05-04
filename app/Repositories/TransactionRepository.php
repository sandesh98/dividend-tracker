<?php

namespace App\Repositories;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class TransactionRepository {

    /**
     * Get all transactions from the manual_transactions table
     *
     * @return Builder
     */
    public function manualTransactions(): Builder
    {
        return DB::table('cash_movements');
    }

    /**
     * Get all deposits
     *
     * @return integer
     */
    public function getDeposits(): int
    {
        return $this->manualTransactions()->where('total_transaction_value', '>', 0)->pluck('total_transaction_value')->sum();
    }

    /**
     * Get all withdrawals
     *
     * @return integer
     */
    public function getWithdrawals(): int
    {
        return $this->manualTransactions()->where('total_transaction_value', '<', 0)->pluck('total_transaction_value')->sum();
    }
}
