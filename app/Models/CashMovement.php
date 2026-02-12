<?php

namespace App\Models;

use App\Models\Casts\AsCashMovement;
use App\Models\Casts\AsMoney;
use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'amount' => AsMoney::class,
            'description' => AsCashMovement::class,
            'occurred_at' => 'datetime',
        ];
    }
}
