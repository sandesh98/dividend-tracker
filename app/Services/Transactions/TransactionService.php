<?php

namespace App\Services\Transactions;

use App\Repositories\TradeRepository;
use App\Repositories\TransactionRepository;

class TransactionService
{
    public function __construct(
        readonly private TransactionRepository $transactionRepository,
        readonly private TradeRepository $tradeRepository
    ) {
    }

    /**
     * Get available cash
     *
     * @return void
     */
    public function getAvailableCash()
    {
        // TODO: Alle gemaakte kosten moeten hier nog van afgetrokken worden.
        $deposits = $this->transactionRepository->getDeposits() / 100;
        $withdrawals = $this->transactionRepository->getWithdrawals() / 100;

        return $deposits - $withdrawals;
    }

    /**
     * Get the sum of transactionscosts
     *
     * @return integer
     */
    public function getTransactionscostsSum(): int
    {
        return $this->tradeRepository->getAllTransactionscosts()->sum();
    }
}
