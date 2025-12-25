<?php

namespace App\Value;

enum DividendType: string
{
    case Dividend = 'Dividend';
    case DividendTax = 'Dividendbelasting';

    /**
     * Get the label for the transaction type
     */
    public function label(): string
    {
        return match ($this) {
            self::Dividend => 'Dividend',
            self::DividendTax => 'Dividendbelasting',
        };
    }
}
