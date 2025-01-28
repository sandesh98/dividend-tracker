<?php

namespace App\Repositories;

use App\Models\Dividend;
use Illuminate\Support\Collection;

class DividendRepository extends AbstractRepository
{
    public function __construct(private readonly Dividend $dividend)
    {
        parent::__construct($this->dividend);
    }

    public function getTransactionsGroupsByDateAndTime(string $stock): Collection
    {
        return $this->dividend->newQuery()->where('product', 'LIKE', $stock)
            ->whereIn('description', ['Dividend', 'Dividendbelasting'])
            ->orderBy('date')
            ->orderBy('time')
            ->get();
    }
}
