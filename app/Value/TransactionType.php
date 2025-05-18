<?php

namespace App\Value;

enum TransactionType: string {
    case Buy = 'buy';
    case Sell = 'sell';
    case Dividend = 'dividend';
    case DividendTax = 'Dividendbelasting';


    /**
     * Get the label for the transaction type
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::Buy => 'Koop',
            self::Sell => 'Verkoop',
            self::Dividend => 'Dividend',
            self::DividendTax => 'Dividendbelasting',
        };
    }
}
