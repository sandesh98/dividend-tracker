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

    /**
     * Find a stock by ISIN
     */
    public function findByIsin(string $isin): Stock
    {
        return $this->stock->newQuery()->where('isin', $isin)->first();
    }

    /**
     * Get a Collection of ISINS by stock name
     */
    public function getIsinsByName(string $stock): Collection
    {
        return $this->stock->newQuery()->where('name', 'LIKE', $stock)->pluck('isin');
    }

    /**
     * Get a Stock by name
     */
    public function findByName(string $stock): Stock
    {
        return $this->stock->newQuery()->where('name', 'LIKE', $stock)->first();
    }

    /**
     * Get a Stock by ticker
     *
     * @param  string  $ticker
     */
    public function findByTicker(Stock $stock): Stock
    {
        return $stock->ticker;
    }

    /**
     * Get a Collection of all tickers
     */
    public function getAllTickers(): Collection
    {
        return $this->stock->distinct()->pluck('ticker');
    }

    /**
     * Get a collection of all stocks by name
     */
    public function getAllStockNames(): Collection
    {
        return $this->stock->distinct()->pluck('name', 'display_name');
    }

    /**
     * Get the type for a given stock
     *
     * @param  string  $stock
     * @return string
     */
    public function getType(Stock $stock)
    {
        return $stock->type;
    }
}
