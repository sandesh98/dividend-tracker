<?php

namespace App\Services;

use App\Repositories\TransactionRepository;

class TransactionService
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository
    ) {}

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
}
