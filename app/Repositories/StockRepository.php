<?php

namespace App\Repositories;

use App\Models\Stock;
use Illuminate\Support\Collection;

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

    public function findIsinByStock($stock): Collection
    {
        return $this->stock->newQuery()->where('product', 'LIKE', $stock)->pluck('isin');
    }

    public function findByTicker(string $ticker): Stock
    {
        return $this->stock->newQuery()->where('ticker', $ticker)->first();
    }

    public function getTickers(): Collection
    {
        return $this->stock->distinct()->pluck('ticker');
    }

    public function getAllStockNames(): Collection
    {
        return $this->stock->distinct()->pluck('product', 'display_name');
    }
}
