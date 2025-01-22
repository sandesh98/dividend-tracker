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
     * @return void
     */
    public function getAllTradesFor(string $stock)
    {
        return $this->trade->newQuery()->where('product', 'like', $stock)->get();
    }
}
