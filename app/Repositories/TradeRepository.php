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

    public function allUniqueProductAndIsins(): Collection
    {
        return $this->trade->distinct()->pluck('isin', 'product');
    }

    public function convertPriceToCents(float $initialPrice): int
    {
        return round($initialPrice, 2) * 100;
    }
}
