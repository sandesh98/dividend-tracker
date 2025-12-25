<?php

namespace App\Models;

use App\Models\Casts\AsCurrency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stock extends Model
{
    protected $fillable = ['product', 'isin', 'type', 'ticker', 'currency', 'price', 'display_name'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'currency' => AsCurrency::class,
        ];
    }

    /**
     * Query the trades.
     */
    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    /**
     * Query the dividends.
     */
    public function dividends(): HasMany
    {
        return $this->hasMany(Dividend::class);
    }
}
