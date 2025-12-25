<?php

namespace App\Models;

use App\Models\Casts\AsMoney;
use App\Value\DividendType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dividend extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'description' => DividendType::class,
            'dividend_amount' => AsMoney::class,
            'paid_out_at' => 'datetime',
        ];
    }

    /**
     * Query the stock.
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
