<?php

namespace App\Repositories;

use App\Models\Trade;
use Illuminate\Support\Collection;

class TradeRepository extends AbstractRepository
{
    public function __construct(private readonly Trade $trade)
    {
        parent::__construct($this->trade);
    }

    /**
     * Get a Collection of ISINS and products from Trade
     *
     * @return Collection
     */
    public function allUniqueProductAndIsins(): Collection
    {
        return $this->trade->distinct()->pluck('isin', 'product');
    }

    /**
     * Get all trades from a given stock
     *
     * @param string $stock
     * @return Collection
     */
    public function getAllTradesFor(string $stock): Collection
    {
        return $this->trade->newQuery()->where('product', 'like', $stock)->get();
    }

    /**
     * Get all transactionscosts from trades
     *
     * @return Collection
     */
    public function getAllTransactionscosts(): Collection
    {
        return $this->trade->where('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden')
            ->pluck('total_transaction_value');
    }

    /**
     * Get the sum of the transactioncosts for a given stock
     *
     * @param string $stock
     * @return float
     */
    public function getTransactioncostsFor(string $stock): float
    {
        return $this->trade->where('product', 'LIKE', $stock)
            ->where('description', 'LIKE', 'DEGIRO Transactiekosten en/of kosten van derden')
            ->pluck('total_transaction_value')
            ->sum() / 100;
    }
}
