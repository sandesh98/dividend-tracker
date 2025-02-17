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
     *
     * @param string $isin
     * @return Stock
     */
    public function findByIsin(string $isin): Stock
    {
        return $this->stock->newQuery()->where('isin', $isin)->first();
    }

    /**
     * Get a Collection of ISINS by stock name
     *
     * @param string $stock
     * @return Collection
     */
    public function getIsinsByName(string $stock): Collection
    {
        return $this->stock->newQuery()->where('product', 'LIKE', $stock)->pluck('isin');
    }

    /**
     * Get a Stock by name
     *
     * @param string $stock
     * @return Stock
     */
    public function findByName(string $stock): Stock
    {
        return $this->stock->newQuery()->where('product', 'LIKE', $stock)->first();
    }

    /**
     * Get a Stock by ticker
     *
     * @param string $ticker
     * @return Stock
     */
    public function findByTicker(string $ticker): Stock
    {
        return $this->stock->newQuery()->where('ticker', $ticker)->first();
    }

    /**
     * Get a Collection of all tickers
     *
     * @return Collection
     */
    public function getAllTickers(): Collection
    {
        return $this->stock->distinct()->pluck('ticker');
    }

    /**
     * Get a collection of all stocks by name
     *
     * @return Collection
     */
    public function getAllStockNames(): Collection
    {
        return $this->stock->distinct()->pluck('product', 'display_name');
    }

    /**
     * Get the type for a given stock
     *
     * @param string $stock
     * @return string
     */
    public function getType(string $stock): string
    {
        return $this->stock->newQuery()->where('product', 'LIKE', $stock)->first()->getType();
    }

    /**
     * Get the currency type for a given stock
     *
     * @param string $stock
     * @return string
     */
    public function getCurrency(string $stock): string
    {
        return $this->stock->newQuery()->where('product', 'LIKE', $stock)->first()->getCurrency();
    }
}
