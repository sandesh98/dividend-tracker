<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    protected $fillable = ['product', 'isin', 'type', 'ticker', 'currency', 'price', 'display_name'];

    /**
     * Query the trades.
     *
     * @return HasMany
     */
    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    /**
     * Query the dividends.
     *
     * @return HasMany
     */
    public function dividends(): HasMany
    {
        return $this->hasMany(Dividend::class);
    }
}
