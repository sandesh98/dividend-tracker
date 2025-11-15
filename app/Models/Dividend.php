<?php

namespace App\Models;

use App\Models\Casts\AsMoney;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dividend extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'time', 'description', 'fx', 'dividend_amount', 'dividend_amount_currency'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts()
    {
        return [
            'dividend_amount' => AsMoney::class,
        ];
    }

    /**
     * Query the stock
     *
     * @return BelongsTo
     */
    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }
}
