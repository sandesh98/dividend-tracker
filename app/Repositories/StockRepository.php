<?php

namespace App\Repositories;

use App\Models\Stock;

class StockRepository extends AbstractRepository
{
    public function __construct(private readonly Stock $stock)
    {
        parent::__construct($stock);
    }

    public function findByIsin(string $isin): Stock
    {
        return $this->stock->newQuery()->where('isin', $isin)->first();
    }

    public function getTickers()
    {
        return $this->stock->distinct()->pluck('ticker');
    }
}
