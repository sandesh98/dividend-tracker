<?php

namespace App\Value;

enum TransactionType: string
{
    case Buy = 'buy';
    case Sell = 'sell';


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
        };
    }
}
